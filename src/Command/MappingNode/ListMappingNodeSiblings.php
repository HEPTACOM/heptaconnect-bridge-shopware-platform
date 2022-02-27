<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Overview\IdentityOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListMappingNodeSiblings extends Command
{
    protected static $defaultName = 'heptaconnect:mapping-node:siblings-list';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private IdentityOverviewActionInterface $identityOverviewAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        IdentityOverviewActionInterface $identityOverviewAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->identityOverviewAction = $identityOverviewAction;
    }

    protected function configure(): void
    {
        $this->addArgument('external-ids', InputArgument::REQUIRED | InputArgument::IS_ARRAY);
        $this->addOption('portal-node-key', 'p', InputArgument::OPTIONAL);
        $this->addOption('entity-type', 't', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $entityType = (string) $input->getOption('entity-type');
        $portalNodeKeyParam = (string) $input->getOption('portal-node-key');
        $externalIds = (array) $input->getArgument('external-ids');
        $criteria = new IdentityOverviewCriteria();

        if ($entityType !== '' && !\is_a($entityType, DatasetEntityContract::class, true)) {
            $io->error('The provided dataset entity class does not implement the DatasetEntityContract.');

            return 1;
        }

        if ($portalNodeKeyParam !== '') {
            $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeKeyParam);

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

                return 2;
            }

            $criteria->getPortalNodeKeyFilter()->push([$portalNodeKey]);
        }

        $externalIds = \array_filter($externalIds);

        if ($externalIds === []) {
            $io->error('The provided external-ids are empty.');

            return 3;
        }

        $criteria->setExternalIdFilter($externalIds);

        $othersCriteria = new IdentityOverviewCriteria();
        $othersCriteria->setSort([
            IdentityOverviewCriteria::FIELD_ENTITY_TYPE => IdentityOverviewCriteria::SORT_ASC,
            IdentityOverviewCriteria::FIELD_MAPPING_NODE => IdentityOverviewCriteria::SORT_ASC,
            IdentityOverviewCriteria::FIELD_PORTAL_NODE => IdentityOverviewCriteria::SORT_ASC,
        ]);

        foreach ($this->identityOverviewAction->overview($criteria) as $identity) {
            $othersCriteria->getMappingNodeKeyFilter()->push([$identity->getMappingNodeKey()]);
        }

        $rows = [];

        foreach ($this->identityOverviewAction->overview($othersCriteria) as $identity) {
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identity->getPortalNodeKey()),
                'external-id' => $identity->getExternalId(),
                'mapping-node-key' => $this->storageKeyGenerator->serialize($identity->getMappingNodeKey()),
                'entity-type' => $identity->getEntityType(),
            ];
        }

        if ($rows === []) {
            $io->note('There are no mapping nodes of the selected portal with given external id.');

            return 0;
        }

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
