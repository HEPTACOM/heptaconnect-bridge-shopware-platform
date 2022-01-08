<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job;

use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\Delete\JobDeleteActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\Delete\JobDeleteCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\Listing\JobListFinishedActionInterface;
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
        $this->jobDeleteAction->delete(new JobDeleteCriteria(new JobKeyCollection($this->jobListFinishedAction->list())));

        return 0;
    }
}
