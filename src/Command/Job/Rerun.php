<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job;

use Heptacom\HeptaConnect\Core\Job\Contract\DelegatingJobActorContract;
use Heptacom\HeptaConnect\Core\Job\JobData;
use Heptacom\HeptaConnect\Core\Job\JobDataCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\JobKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobPayloadRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Rerun extends Command
{
    protected static $defaultName = 'heptaconnect:job:rerun';

    private JobRepositoryContract $jobRepository;

    private JobPayloadRepositoryContract $jobPayloadRepository;

    private DelegatingJobActorContract $jobActor;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        JobRepositoryContract $jobRepository,
        JobPayloadRepositoryContract $jobPayloadRepository,
        DelegatingJobActorContract $jobActor,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->jobPayloadRepository = $jobPayloadRepository;
        $this->jobRepository = $jobRepository;
        $this->jobActor = $jobActor;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function configure(): void
    {
        $this->addArgument('job-key', InputArgument::REQUIRED, 'The key of the job');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $jobKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('job-key'));

        if (!\is_a($jobKey, JobKeyInterface::class, true)) {
            $io->error('The provided job-key is not a JobKeyInterface.');

            return 1;
        }

        try {
            $job = $this->jobRepository->get($jobKey);
            $payloadKey = $job->getPayloadKey();
            $payload = $payloadKey !== null ? $this->jobPayloadRepository->get($payloadKey) : null;
        } catch (UnsupportedStorageKeyException $exception) {
            return 1;
        }

        $jobData = new JobData($job->getMapping(), $payload, $jobKey);
        $jobType = $job->getJobType();
        $jobDataCollection = new JobDataCollection();
        $jobDataCollection->push([$jobData]);

        try {
            $this->jobRepository->start($jobData->getJobKey(), null);
            $this->jobActor->performJobs($jobType, $jobDataCollection);
            $this->jobRepository->finish($jobData->getJobKey(), null);
        } catch (UnsupportedStorageKeyException $exception) {
            return 1;
        }

        return 0;
    }
}
