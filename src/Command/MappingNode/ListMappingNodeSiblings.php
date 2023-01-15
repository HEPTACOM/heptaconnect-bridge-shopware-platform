<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\ScalarCollection\StringCollection;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Overview\IdentityOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityDirection\Overview\IdentityDirectionOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityDirection\Overview\IdentityDirectionOverviewResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityDirection\IdentityDirectionOverviewActionInterface;
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

    private IdentityDirectionOverviewActionInterface $identityDirectionOverviewAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        IdentityOverviewActionInterface $identityOverviewAction,
        IdentityDirectionOverviewActionInterface $identityDirectionOverviewAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->identityOverviewAction = $identityOverviewAction;
        $this->identityDirectionOverviewAction = $identityDirectionOverviewAction;
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
        $identityCriteria = new IdentityOverviewCriteria();
        $sourceIdentityDirectionCriteria = new IdentityDirectionOverviewCriteria();

        if ($entityType !== '' && !\is_a($entityType, DatasetEntityContract::class, true)) {
            $io->error('The provided dataset entity class does not implement the DatasetEntityContract.');

            $identityCriteria->setEntityTypeFilter([$entityType]);
            $sourceIdentityDirectionCriteria->setEntityTypeFilter(new StringCollection($identityCriteria->getEntityTypeFilter()));

            return 1;
        }

        if ($portalNodeKeyParam !== '') {
            $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeKeyParam);

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

                return 2;
            }

            $identityCriteria->getPortalNodeKeyFilter()->push([$portalNodeKey]);
            $sourceIdentityDirectionCriteria->setSourcePortalNodeKeyFilter($identityCriteria->getPortalNodeKeyFilter());
        }

        $externalIds = \array_filter($externalIds);

        if ($externalIds === []) {
            $io->error('The provided external-ids are empty.');

            return 3;
        }

        $identityCriteria->setExternalIdFilter($externalIds);
        $sourceIdentityDirectionCriteria->setSourceExternalIdFilter(new StringCollection($identityCriteria->getExternalIdFilter()));

        $rows = [];

        $othersCriteria = new IdentityOverviewCriteria();
        $othersCriteria->setSort([
            IdentityOverviewCriteria::FIELD_ENTITY_TYPE => IdentityOverviewCriteria::SORT_ASC,
            IdentityOverviewCriteria::FIELD_MAPPING_NODE => IdentityOverviewCriteria::SORT_ASC,
            IdentityOverviewCriteria::FIELD_PORTAL_NODE => IdentityOverviewCriteria::SORT_ASC,
        ]);
        $sourceIdentityDirectionCriteria->setSort([
            IdentityDirectionOverviewCriteria::FIELD_ENTITY_TYPE => IdentityDirectionOverviewCriteria::SORT_ASC,
            IdentityDirectionOverviewCriteria::FIELD_TARGET_PORTAL_NODE => IdentityDirectionOverviewCriteria::SORT_ASC,
            IdentityDirectionOverviewCriteria::FIELD_TARGET_EXTERNAL_ID => IdentityDirectionOverviewCriteria::SORT_ASC,
        ]);

        foreach ($this->identityOverviewAction->overview($identityCriteria) as $identity) {
            $othersCriteria->getMappingNodeKeyFilter()->push([$identity->getMappingNodeKey()]);
        }

        foreach ($this->identityOverviewAction->overview($othersCriteria) as $identity) {
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identity->getPortalNodeKey()->withAlias()),
                'external-id' => $identity->getExternalId(),
                'group-key' => $this->storageKeyGenerator->serialize($identity->getMappingNodeKey()),
                'entity-type' => $identity->getEntityType(),
            ];
        }

        $groupKeys = [];

        /** @var IdentityDirectionOverviewResult $identityDirection */
        foreach ($this->identityDirectionOverviewAction->overview($sourceIdentityDirectionCriteria) as $identityDirection) {
            $groupKey = $this->storageKeyGenerator->serialize($identityDirection->getIdentityDirectionKey());

            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identityDirection->getSourcePortalNodeKey()->withAlias()),
                'external-id' => $identityDirection->getSourceExternalId(),
                'group-key' => $groupKey,
                'entity-type' => $identityDirection->getEntityType(),
            ];
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identityDirection->getTargetPortalNodeKey()->withAlias()),
                'external-id' => $identityDirection->getTargetExternalId(),
                'group-key' => $groupKey,
                'entity-type' => $identityDirection->getEntityType(),
            ];

            $groupKeys[] = $groupKey;
        }

        $targetIdentityDirectionCriteria = new IdentityDirectionOverviewCriteria();
        $targetIdentityDirectionCriteria->setSort($sourceIdentityDirectionCriteria->getSort());
        $targetIdentityDirectionCriteria->setTargetExternalIdFilter($sourceIdentityDirectionCriteria->getSourceExternalIdFilter());
        $targetIdentityDirectionCriteria->setTargetPortalNodeKeyFilter($sourceIdentityDirectionCriteria->getSourcePortalNodeKeyFilter());
        $targetIdentityDirectionCriteria->setEntityTypeFilter($sourceIdentityDirectionCriteria->getEntityTypeFilter());

        /** @var IdentityDirectionOverviewResult $identityDirection */
        foreach ($this->identityDirectionOverviewAction->overview($targetIdentityDirectionCriteria) as $identityDirection) {
            $groupKey = $this->storageKeyGenerator->serialize($identityDirection->getIdentityDirectionKey());

            if (\in_array($groupKey, $groupKeys)) {
                continue;
            }

            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identityDirection->getSourcePortalNodeKey()->withAlias()),
                'external-id' => $identityDirection->getSourceExternalId(),
                'group-key' => $groupKey,
                'entity-type' => $identityDirection->getEntityType(),
            ];
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identityDirection->getTargetPortalNodeKey()->withAlias()),
                'external-id' => $identityDirection->getTargetExternalId(),
                'group-key' => $groupKey,
                'entity-type' => $identityDirection->getEntityType(),
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
