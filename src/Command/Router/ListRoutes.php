<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Storage\Base\Action\Route\Overview\RouteOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Overview\RouteOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListRoutes extends Command
{
    protected static $defaultName = 'heptaconnect:router:list-routes';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private RouteOverviewActionInterface $routeOverviewAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        RouteOverviewActionInterface $routeOverviewAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->routeOverviewAction = $routeOverviewAction;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $targets = [];

        $criteria = new RouteOverviewCriteria();
        $criteria->setSort([
            RouteOverviewCriteria::FIELD_ENTITY_TYPE => RouteOverviewCriteria::SORT_ASC,
            RouteOverviewCriteria::FIELD_CREATED => RouteOverviewCriteria::SORT_DESC,
        ]);

        foreach ($this->routeOverviewAction->overview($criteria) as $route) {
            $capabilities = $route->getCapabilities();
            \sort($capabilities);

            $targets[] = [
                'id' => $this->storageKeyGenerator->serialize($route->getRouteKey()),
                'type' => $route->getEntityType(),
                'source' => $this->storageKeyGenerator->serialize($route->getSourcePortalNodeKey()),
                'target' => $this->storageKeyGenerator->serialize($route->getTargetPortalNodeKey()),
                'capabilities' => \implode(', ', $capabilities),
            ];
        }

        if (\count($targets) === 0) {
            $io->note('There are no routes.');

            return 0;
        }

        $io->table(\array_keys(\current($targets)), $targets);

        return 0;
    }
}
