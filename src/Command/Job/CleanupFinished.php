<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job;

use Heptacom\HeptaConnect\Storage\Base\Action\Job\Delete\JobDeleteCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\Job\Listing\JobListFinishedResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobDeleteActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobListFinishedActionInterface;
use Heptacom\HeptaConnect\Storage\Base\JobKeyCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupFinished extends Command
{
    protected static $defaultName = 'heptaconnect:job:cleanup-finished';

    public function __construct(
        private JobListFinishedActionInterface $jobListFinishedAction,
        private JobDeleteActionInterface $jobDeleteAction
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobKeys = \iterable_map(
            $this->jobListFinishedAction->list(),
            static fn (JobListFinishedResult $jobListFinishedResult) => $jobListFinishedResult->getJobKey()
        );

        $progressBar = new ProgressBar($output);
        $progressBar->start();

        foreach (self::iterableChunk($jobKeys, 1000) as $jobKeys) {
            $this->jobDeleteAction->delete(new JobDeleteCriteria(new JobKeyCollection($jobKeys)));
            $progressBar->advance();
        }

        $progressBar->finish();

        return 0;
    }

    /**
     * @return iterable<array>
     */
    private function iterableChunk(iterable $items, int $size): iterable
    {
        $buffer = [];

        foreach ($items as $item) {
            $buffer[] = $item;

            if (\count($buffer) >= $size) {
                yield $buffer;
                $buffer = [];
            }
        }

        if (\count($buffer) > 0) {
            yield $buffer;
        }
    }
}
