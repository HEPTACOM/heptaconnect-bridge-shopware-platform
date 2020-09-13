<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Cronjob;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob\CronjobRunMessage;
use Heptacom\HeptaConnect\Storage\Base\Contract\CronjobRunRepositoryContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class EnsureQueue extends Command
{
    protected static $defaultName = 'heptaconnect:cronjob:ensure-queue';

    private CronjobRunRepositoryContract $cronjobRunRepository;

    private MessageBusInterface $messageBus;

    public function __construct(CronjobRunRepositoryContract $cronjobRunRepository, MessageBusInterface $messageBus)
    {
        parent::__construct();
        $this->cronjobRunRepository = $cronjobRunRepository;
        $this->messageBus = $messageBus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->section('Ensure cronjobs to be queued');
        $now = \date_create();

        foreach ($this->cronjobRunRepository->listExecutables($now) as $runKey) {
            $this->messageBus->dispatch(
                Envelope::wrap(new CronjobRunMessage($runKey->getId()))
                    ->with(new DelayStamp(($run->getQueuedFor()->getTimestamp() - $now->getTimestamp()) * 1000))
            );
            $io->success(\sprintf('Requeued cronjob run %s', $runKey));
        }

        return 0;
    }
}
