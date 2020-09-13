<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\CronjobStorage;
use Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Cronjob\CronjobRunEntity;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;

class CronjobRunMessageHandler extends AbstractMessageHandler
{
    private CronjobStorage $cronjobStorage;

    private CronjobRunHandler $runHandler;

    public function __construct(CronjobStorage $cronjobStorage, CronjobRunHandler $runHandler)
    {
        $this->cronjobStorage = $cronjobStorage;
        $this->runHandler = $runHandler;
    }

    public function handle($message): void
    {
        if (!$message instanceof CronjobRunMessage) {
            return;
        }

        $run = $this->cronjobStorage->getRun($message->getRunId());

        if (!$run instanceof CronjobRunEntity) {
            // TODO log cases
            return;
        }

        $this->runHandler->run($run);
    }

    public static function getHandledMessages(): iterable
    {
        yield CronjobRunMessage::class;
    }
}
