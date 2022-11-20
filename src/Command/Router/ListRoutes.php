<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Storage\Base\Action\Route\Overview\RouteOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListRoutes extends Command
{
    protected static $defaultName = 'heptaconnect:router:list-routes';

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private RouteOverviewActionInterface $routeOverviewAction
    ) {
        parent::__construct();
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
                'type' => (string) $route->getEntityType(),
                'source' => $this->storageKeyGenerator->serialize($route->getSourcePortalNodeKey()->withAlias()),
                'target' => $this->storageKeyGenerator->serialize($route->getTargetPortalNodeKey()->withAlias()),
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
