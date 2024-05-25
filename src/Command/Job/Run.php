<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job;

use Heptacom\HeptaConnect\Core\Job\Contract\DelegatingJobActorContract;
use Heptacom\HeptaConnect\Core\Job\JobData;
use Heptacom\HeptaConnect\Core\Job\JobDataCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\Job\Get\JobGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\Job\Get\JobGetResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobGetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\JobKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\JobKeyCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:job:run')]
class Run extends Command
{
    public function __construct(
        private JobGetActionInterface $jobGetAction,
        private DelegatingJobActorContract $jobActor,
        private StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'job-key',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'The key of the job'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $jobKeys = $this->getJobKeys($input);
        } catch (\Throwable $exception) {
            (new SymfonyStyle($input, $output))->error($exception->getMessage());

            return 1;
        }

        $jobDataCollections = [];

        /** @var JobGetResult $job */
        foreach ($this->jobGetAction->get(new JobGetCriteria($jobKeys)) as $job) {
            $jobData = new JobData(
                $job->getMappingComponent(),
                $job->getPayload(),
                $job->getJobKey()
            );

            $jobType = $job->getJobType();

            $jobDataCollections[$jobType] ??= new JobDataCollection();
            $jobDataCollections[$jobType]->push([$jobData]);
        }

        foreach ($jobDataCollections as $jobType => $jobDataCollection) {
            $this->jobActor->performJobs($jobType, $jobDataCollection);
        }

        return 0;
    }

    private function getJobKeys(InputInterface $input): JobKeyCollection
    {
        $jobKeys = [];

        foreach ((array) $input->getArgument('job-key') as $keyData) {
            $jobKey = $this->storageKeyGenerator->deserialize($keyData);

            if (!\is_a($jobKey, JobKeyInterface::class, true)) {
                throw new \InvalidArgumentException(
                    'The provided job-key is not a JobKeyInterface: ' . $keyData,
                    1700157129
                );
            }

            $jobKeys[] = $jobKey;
        }

        return new JobKeyCollection($jobKeys);
    }
}
