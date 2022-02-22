<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias;

use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Overview\PortalNodeAliasOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Overview\PortalNodeAliasOverviewResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasOverviewActionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Overview extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:alias:overview';

    private PortalNodeAliasOverviewActionInterface $aliasOverviewAction;

    public function __construct(PortalNodeAliasOverviewActionInterface $aliasOverviewAction)
    {
        parent::__construct();
        $this->aliasOverviewAction = $aliasOverviewAction;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $criteria = new PortalNodeAliasOverviewCriteria();
        $rows = [];
        /** @var PortalNodeAliasOverviewResult $result */
        foreach ($this->aliasOverviewAction->overview($criteria) as $result) {
            $rows[] = [
                'portal-node-key' => $result->getKeyData(),
                'alias' => $result->getAlias(),
            ];
        }

        if (empty($rows)) {
            $io->note('There are no portal nodes of the selected portal.');

            return 0;
        }

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
