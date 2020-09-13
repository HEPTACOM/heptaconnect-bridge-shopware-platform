<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Cronjob;

use Cron\CronExpression;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob\CronjobRunMessage;
use Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\CronjobRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\CronjobRunRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class Queue extends Command
{
    protected static $defaultName = 'heptaconnect:cronjob:queue';

    private CronjobRepositoryContract $cronjobRepository;

    private CronjobRunRepositoryContract $cronjobRunRepository;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private MessageBusInterface $messageBus;

    public function __construct(
        CronjobRepositoryContract $cronjobRepository,
        CronjobRunRepositoryContract $cronjobRunRepository,
        StorageKeyGeneratorContract $storageKeyGenerator,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();
        $this->cronjobRepository = $cronjobRepository;
        $this->cronjobRunRepository = $cronjobRunRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->messageBus = $messageBus;
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

        foreach ($this->cronjobRepository->listExecutables($force ? null : $forUntil) as $cronjobKey) {
            $cronjob = $this->cronjobRepository->read($cronjobKey);

            if (!$cronjob instanceof CronjobInterface) {
                continue;
            }

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
                $runKey = $this->cronjobRunRepository->create($cronjobKey, $nextRun);
                $this->messageBus->dispatch(
                    Envelope::wrap(new CronjobRunMessage($this->storageKeyGenerator->serialize($runKey)))
                        ->with(new DelayStamp(($nextRun->getTimestamp() - $now->getTimestamp()) * 1000))
                );
                $nextRun = $cronExpr->getNextRunDate($nextRun);
            }

            $this->cronjobRepository->updateNextExecutionTime($cronjobKey, $forUntil);
            $io->success(\sprintf('Queued cronjob %s %d times', $this->storageKeyGenerator->serialize($cronjobKey), $times));
        }

        return 0;
    }
}
