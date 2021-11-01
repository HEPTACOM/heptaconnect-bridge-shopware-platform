<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Storage\Base\Contract\RouteCapabilityOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\RouteCapabilityOverviewCriteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListRouteCapabilities extends Command
{
    protected static $defaultName = 'heptaconnect:router:list-capabilities';

    private RouteCapabilityOverviewActionInterface $routeCapabilityOverviewAction;

    public function __construct(RouteCapabilityOverviewActionInterface $routeCapabilityOverviewAction)
    {
        parent::__construct();
        $this->routeCapabilityOverviewAction = $routeCapabilityOverviewAction;
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
