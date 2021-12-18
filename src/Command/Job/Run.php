<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job;

use Heptacom\HeptaConnect\Core\Job\Contract\DelegatingJobActorContract;
use Heptacom\HeptaConnect\Core\Job\JobData;
use Heptacom\HeptaConnect\Core\Job\JobDataCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\Get\JobGetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\Get\JobGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\JobKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\JobKeyCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Run extends Command
{
    protected static $defaultName = 'heptaconnect:job:run';

    private JobGetActionInterface $jobGetAction;

    private DelegatingJobActorContract $jobActor;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        JobGetActionInterface $jobGetAction,
        DelegatingJobActorContract $jobActor,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->jobGetAction = $jobGetAction;
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

        $jobDataCollection = new JobDataCollection();

        foreach ($this->jobGetAction->get(new JobGetCriteria(new JobKeyCollection([$jobKey]))) as $job) {
            if ($jobDataCollection->count() > 0) {
                return 2;
            }

            $jobData = new JobData($job->getMapping(), $job->getPayload(), $jobKey);
            $jobType = $job->getJobType();
            $jobDataCollection->push([$jobData]);

            $this->jobActor->performJobs($jobType, $jobDataCollection);
        }

        return 0;
    }
}
