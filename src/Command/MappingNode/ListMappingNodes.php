<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\UnsafeClassString;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Overview\IdentityOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListMappingNodes extends Command
{
    protected static $defaultName = 'heptaconnect:mapping-node:list';

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private IdentityOverviewActionInterface $identityOverviewAction
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('entity-type', InputArgument::REQUIRED)
            ->addArgument('portal-node-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $entityType = (string) $input->getArgument('entity-type');
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));
        $criteria = new IdentityOverviewCriteria();

        if (!\is_a($entityType, DatasetEntityContract::class, true)) {
            $io->error('The provided dataset entity class does not implement the DatasetEntityContract.');

            return 1;
        }

        $criteria->setEntityTypeFilter([new UnsafeClassString($entityType)]);

        if (!\is_a($portalNodeKey, PortalNodeKeyInterface::class, false)) {
            $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

            return 2;
        }

        $criteria->getPortalNodeKeyFilter()->push([$portalNodeKey]);

        $rows = [];

        foreach ($this->identityOverviewAction->overview($criteria) as $identity) {
            $rows[] = [
                'mapping-node-id' => $this->storageKeyGenerator->serialize($identity->getMappingNodeKey()),
                'portal-node-id' => $this->storageKeyGenerator->serialize($identity->getPortalNodeKey()->withAlias()),
                'external-id' => $identity->getExternalId(),
            ];
        }

        if ($rows === []) {
            $io->note('There are no mapping nodes of the selected portal.');

            return 0;
        }

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
