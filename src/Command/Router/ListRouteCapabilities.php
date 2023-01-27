<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Storage\Base\Action\RouteCapability\Overview\RouteCapabilityOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\RouteCapability\RouteCapabilityOverviewActionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListRouteCapabilities extends Command
{
    protected static $defaultName = 'heptaconnect:router:list-capabilities';

    public function __construct(
        private RouteCapabilityOverviewActionInterface $routeCapabilityOverviewAction
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $result = [];

        $criteria = new RouteCapabilityOverviewCriteria();
        $criteria->setSort([
            RouteCapabilityOverviewCriteria::FIELD_NAME => RouteCapabilityOverviewCriteria::SORT_ASC,
            RouteCapabilityOverviewCriteria::FIELD_CREATED => RouteCapabilityOverviewCriteria::SORT_DESC,
        ]);

        foreach ($this->routeCapabilityOverviewAction->overview($criteria) as $capability) {
            $result[] = $capability->getName();
        }

        if (\count($result) === 0) {
            $io->error('There are no capabilities.');

            return 1;
        }

        $io->listing($result);

        return 0;
    }
}
