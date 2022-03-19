<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandsPrintLogsSubscriber implements EventSubscriberInterface
{
    public const LOGGER_STREAM = 'php://stderr';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleCommandEvent::class => 'onPreCommandRun',
            ConsoleEvents::COMMAND => 'onPreCommandRun',
            ConsoleTerminateEvent::class => 'onPostCommandRun',
            ConsoleEvents::TERMINATE => 'onPostCommandRun',
        ];
    }

    public function onPreCommandRun(ConsoleCommandEvent $event): void
    {
        $output = $event->getOutput();

        if ($output->isQuiet()) {
            return;
        }

        if (!$this->logger instanceof Logger) {
            return;
        }

        $logLevel = $this->getLogLevelForOutput($output);

        if ($this->hasLoggerAlreadyHandler($logLevel)) {
            return;
        }

        $this->logger->pushHandler(new StreamHandler(self::LOGGER_STREAM, $logLevel));
    }

    public function onPostCommandRun(ConsoleTerminateEvent $event): void
    {
        if (!$this->logger instanceof Logger) {
            return;
        }

        $this->logger->setHandlers(\array_filter(
            $this->logger->getHandlers(),
            $this->removeErrorStreamHandlerOnce($this->getLogLevelForOutput($event->getOutput()))
        ));
    }

    /**
     * @return callable(HandlerInterface): bool
     */
    protected function removeErrorStreamHandlerOnce(int $logLevel): callable
    {
        $removedOnce = false;

        return function (HandlerInterface $h) use (&$removedOnce, $logLevel): bool {
            if (!$this->isErrorStreamHandler($h, $logLevel)) {
                return false;
            }

            if ($removedOnce) {
                return false;
            }

            $removedOnce = true;

            return true;
        };
    }

    protected function getLogLevelForOutput(OutputInterface $output): int
    {
        if ($output->isDebug()) {
            return Logger::DEBUG;
        }

        if ($output->isVeryVerbose()) {
            return Logger::INFO;
        }

        return Logger::WARNING;
    }

    protected function hasLoggerAlreadyHandler(int $logLevel): bool
    {
        $logger = $this->logger;

        if (!$logger instanceof Logger) {
            return false;
        }

        foreach ($logger->getHandlers() as $handler) {
            if ($this->isErrorStreamHandler($handler, $logLevel)) {
                return true;
            }
        }

        return false;
    }

    protected function isErrorStreamHandler(HandlerInterface $handler, int $logLevel): bool
    {
        if (!$handler instanceof StreamHandler) {
            return false;
        }

        if ($handler->getLevel() !== $logLevel) {
            return false;
        }

        if ($handler->getUrl() !== self::LOGGER_STREAM) {
            return false;
        }

        return true;
    }
}
