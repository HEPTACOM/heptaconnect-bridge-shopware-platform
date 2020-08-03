<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Cronjob;

use Cron\CronExpression;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\CronjobStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Queue extends Command
{
    protected static $defaultName = 'heptaconnect:cronjob:queue';

    private CronjobStorage $cronjobStorage;

    public function __construct(CronjobStorage $cronjobStorage)
    {
        parent::__construct();
        $this->cronjobStorage = $cronjobStorage;
    }

    protected function configure()
    {
        parent::configure();
        $this->addArgument('for', InputArgument::REQUIRED, 'Seconds for how long it shall be pre-scheduled. Will be rounded up to the next minute');
        $this->addOption('force', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->section('Queue cronjobs');

        $now = \date_create();
        $for = (int) $input->getArgument('for');
        $for += ($for % 60);
        $forUntil = (clone $now)->add(new \DateInterval(\sprintf('PT%dS', $for)));
        $force = (bool) $input->getOption('force');

        foreach ($this->cronjobStorage->getNextToQueue($force ? null : $forUntil) as $cronjob) {
            $cronExpr = CronExpression::factory($cronjob->getCronExpression());
            $nextRun = null;

            if (!$force) {
                $nextRun = $cronjob->getQueuedUntil();

                if ($nextRun < $now) {
                    $nextRun = $now;
                }
            }

            $nextRun = $cronExpr->getNextRunDate($nextRun ?? $now);
            $times = 0;

            while ($nextRun < $forUntil) {
                ++$times;
                $this->cronjobStorage->createRun($cronjob->getId(), $nextRun);
                $nextRun = $cronExpr->getNextRunDate($nextRun);
            }

            $this->cronjobStorage->markAsQueuedUntil($cronjob->getId(), $forUntil);
            $io->success(\sprintf('Queued cronjob %s %d times', $cronjob->getId(), $times));
        }

        return 0;
    }
}
