<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job;

use Heptacom\HeptaConnect\Core\Job\Contract\DelegatingJobActorContract;
use Heptacom\HeptaConnect\Core\Job\JobData;
use Heptacom\HeptaConnect\Core\Job\JobDataCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobPayloadRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\JobStorageKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rerun extends Command
{
    protected static $defaultName = 'heptaconnect:job:rerun';

    private JobRepositoryContract $jobRepository;

    private JobPayloadRepositoryContract $jobPayloadRepository;

    private DelegatingJobActorContract $jobActor;

    public function __construct(
        JobRepositoryContract $jobRepository,
        JobPayloadRepositoryContract $jobPayloadRepository,
        DelegatingJobActorContract $jobActor
    ) {
        parent::__construct();
        $this->jobPayloadRepository = $jobPayloadRepository;
        $this->jobRepository = $jobRepository;
        $this->jobActor = $jobActor;
    }

    protected function configure(): void
    {
        $this->addArgument('jobId', InputArgument::REQUIRED, 'The id of the job');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputJobId = $input->getArgument('jobId');
        // For easier copy/paste from database id
        $jobId = \strtolower(\str_replace(' ', '', $inputJobId));

        try {
            $jobKey = new JobStorageKey($jobId);
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
