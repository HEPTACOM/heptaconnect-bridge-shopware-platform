<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\CronjobRunEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\CronjobStorage;
use Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobHandlerContract;

class CronjobRunHandler
{
    private CronjobStorage $cronjobStorage;

    public function __construct(CronjobStorage $cronjobStorage)
    {
        $this->cronjobStorage = $cronjobStorage;
    }

    public function run(CronjobRunEntity $run): void
    {
        if ($run->getStartedAt() instanceof \DateTimeInterface) {
            // TODO log
            return;
        }

        $this->cronjobStorage->markRunAsStarted($run->getId(), \date_create());

        try {
            $handlerClass = $run->getHandler();
            /** @var CronjobHandlerContract $handler */
            $handler = new $handlerClass;
            $handler->handle($run);
        } catch (\Throwable $throwable) {
            $this->cronjobStorage->markRunAsFailed($run->getId(), $throwable);
        }

        $this->cronjobStorage->markRunAsFinished($run->getId(), \date_create());
    }
}
