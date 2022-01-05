<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\CronjobRunKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\CronjobRunRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Cronjob\CronjobRunEntity;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;

/**
 * @internal
 */
class CronjobRunMessageHandler extends AbstractMessageHandler
{
    private CronjobRunRepositoryContract $cronjobRunRepositoryContract;

    private CronjobRunHandler $runHandler;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        CronjobRunRepositoryContract $cronjobRunRepositoryContract,
        CronjobRunHandler $runHandler,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        $this->cronjobRunRepositoryContract = $cronjobRunRepositoryContract;
        $this->runHandler = $runHandler;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    /**
     * @param CronjobRunMessage|mixed $message
     */
    public function handle($message): void
    {
        if (!$message instanceof CronjobRunMessage) {
            return;
        }

        $runKey = $this->storageKeyGenerator->deserialize($message->getRunId());

        if (!$runKey instanceof CronjobRunKeyInterface) {
            // TODO log cases
            return;
        }

        $run = $this->cronjobRunRepositoryContract->read($runKey);

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
