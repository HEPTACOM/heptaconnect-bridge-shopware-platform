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
use Symfony\Component\Console\Input\InputOption;
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

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('time-limit', 't', InputOption::VALUE_REQUIRED, 'The time limit in seconds the cleanup process can run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = \microtime(true);
        $endTime = null;
        $timeLimit = (string) $input->getOption('time-limit');

        if (\is_numeric($timeLimit) && $timeLimit !== '') {
            $endTime = $startTime + (int) $timeLimit;
        }

        $progressBar = new ProgressBar($output);
        $progressBar->start();

        do {
            $deletedAny = false;

            $jobKeys = \iterable_map(
                $this->jobListFinishedAction->list(),
                static fn (JobListFinishedResult $jobListFinishedResult) => $jobListFinishedResult->getJobKey()
            );

            foreach (self::iterableChunk($jobKeys, 1000) as $jobKeysChunk) {
                $jobKeys = new JobKeyCollection($jobKeysChunk);
                $this->jobDeleteAction->delete(new JobDeleteCriteria($jobKeys));
                $deletedAny = true;
                $progressBar->advance($jobKeys->count());

                if ($endTime && $endTime < \microtime(true)) {
                    $progressBar->finish();
                    $output->writeln('');
                    $output->writeln(\sprintf('Cleanup command stopped due to time limit of %ds seconds reached', $timeLimit));

                    return 0;
                }
            }
        } while ($deletedAny);

        $progressBar->finish();
        $output->writeln('');

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
