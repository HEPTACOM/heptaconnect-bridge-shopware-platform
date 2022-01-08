<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob;

use Heptacom\HeptaConnect\Core\Cronjob\CronjobContextFactory;
use Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobRunInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\CronjobRunRepositoryContract;

/**
 * @internal
 */
class CronjobRunHandler
{
    private CronjobRunRepositoryContract $cronjobRunRepository;

    private CronjobContextFactory $cronjobContextFactory;

    public function __construct(
        CronjobRunRepositoryContract $cronjobRunRepository,
        CronjobContextFactory $cronjobContextFactory
    ) {
        $this->cronjobRunRepository = $cronjobRunRepository;
        $this->cronjobContextFactory = $cronjobContextFactory;
    }

    public function run(CronjobRunInterface $run): void
    {
        if ($run->getStartedAt() instanceof \DateTimeInterface) {
            // TODO log
            return;
        }

        $this->cronjobRunRepository->updateStartedAt($run->getRunKey(), \date_create());

        try {
            $handlerClass = $run->getHandler();
            /** @var CronjobHandlerContract $handler */
            $handler = new $handlerClass();
            $handler->handle($this->cronjobContextFactory->createContext($run));
        } catch (\Throwable $throwable) {
            $this->cronjobRunRepository->updateFailReason($run->getRunKey(), $throwable);
        }

        $this->cronjobRunRepository->updateFinishedAt($run->getRunKey(), \date_create());
    }
}
