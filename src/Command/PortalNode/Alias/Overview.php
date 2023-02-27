<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias;

use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Overview\PortalNodeAliasOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Overview\PortalNodeAliasOverviewResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Overview extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:alias:overview';

    public function __construct(
        private PortalNodeAliasOverviewActionInterface $aliasOverviewAction,
        private StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('sort', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $sort = (string) $input->getArgument('sort');

        $criteria = new PortalNodeAliasOverviewCriteria();
        $sortDirection = $sort === 'desc' ? PortalNodeAliasOverviewCriteria::SORT_DESC : PortalNodeAliasOverviewCriteria::SORT_ASC;
        $criteria->setSort([
            PortalNodeAliasOverviewCriteria::FIELD_ALIAS => $sortDirection,
        ]);

        $rows = [];

        /** @var PortalNodeAliasOverviewResult $result */
        foreach ($this->aliasOverviewAction->overview($criteria) as $result) {
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($result->getKey()->withoutAlias()),
                'alias' => $result->getAlias(),
            ];
        }

        if ($rows === []) {
            $io->note('There are no portal node aliases.');

            return 0;
        }

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
