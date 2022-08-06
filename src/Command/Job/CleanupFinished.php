<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job;

use Heptacom\HeptaConnect\Storage\Base\Action\Job\Delete\JobDeleteCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\Job\Listing\JobListFinishedResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobDeleteActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobListFinishedActionInterface;
use Heptacom\HeptaConnect\Storage\Base\JobKeyCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupFinished extends Command
{
    protected static $defaultName = 'heptaconnect:job:cleanup-finished';

    private JobListFinishedActionInterface $jobListFinishedAction;

    private JobDeleteActionInterface $jobDeleteAction;

    public function __construct(
        JobListFinishedActionInterface $jobListFinishedAction,
        JobDeleteActionInterface $jobDeleteAction
    ) {
        parent::__construct();

        $this->jobListFinishedAction = $jobListFinishedAction;
        $this->jobDeleteAction = $jobDeleteAction;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobKeys = \iterable_map(
            $this->jobListFinishedAction->list(),
            static fn (JobListFinishedResult $jobListFinishedResult) => $jobListFinishedResult->getJobKey()
        );

        $this->jobDeleteAction->delete(new JobDeleteCriteria(new JobKeyCollection($jobKeys)));

        return 0;
    }
}
