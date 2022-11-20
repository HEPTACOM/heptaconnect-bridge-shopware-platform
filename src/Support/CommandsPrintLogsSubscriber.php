<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CommandsPrintLogsSubscriber implements EventSubscriberInterface
{
    public const LOGGER_STREAM = 'php://stderr';

    public function __construct(private StreamHandler $loggerHandler)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleCommandEvent::class => 'onPreCommandRun',
            ConsoleEvents::COMMAND => 'onPreCommandRun',
        ];
    }

    public function onPreCommandRun(ConsoleCommandEvent $event): void
    {
        $output = $event->getOutput();

        $this->loggerHandler->setLevel($this->getLogLevelForOutput($output));
    }

    protected function getLogLevelForOutput(OutputInterface $output): int
    {
        if ($output->isDebug()) {
            return Logger::DEBUG;
        }

        if ($output->isVeryVerbose()) {
            return Logger::INFO;
        }

        if ($output->isVerbose()) {
            return Logger::WARNING;
        }

        return 1000;
    }
}
