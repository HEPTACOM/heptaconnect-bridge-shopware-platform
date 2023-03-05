<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\ScalarCollection\StringCollection;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Overview\IdentityOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityRedirect\Overview\IdentityRedirectOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityRedirect\Overview\IdentityRedirectOverviewResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityRedirect\IdentityRedirectOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListMappingNodeSiblings extends Command
{
    protected static $defaultName = 'heptaconnect:mapping-node:siblings-list';

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private IdentityOverviewActionInterface $identityOverviewAction
        private IdentityRedirectOverviewActionInterface $identityRedirectOverviewAction
    ) {
        parent::__construct();
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
        $sourceIdentityRedirectCriteria = new IdentityRedirectOverviewCriteria();

        if ($entityType !== '') {
            if (!\is_a($entityType, DatasetEntityContract::class, true)) {
                $io->error('The provided dataset entity class does not implement the DatasetEntityContract.');

                return 1;
            }

            $identityCriteria->setEntityTypeFilter([$entityType]);
            $sourceIdentityRedirectCriteria->setEntityTypeFilter(new StringCollection($identityCriteria->getEntityTypeFilter()));
        }

        if ($portalNodeKeyParam !== '') {
            $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeKeyParam);

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

                return 2;
            }

            $identityCriteria->getPortalNodeKeyFilter()->push([$portalNodeKey]);
            $sourceIdentityRedirectCriteria->setSourcePortalNodeKeyFilter($identityCriteria->getPortalNodeKeyFilter());
        }

        $externalIds = \array_filter($externalIds);

        if ($externalIds === []) {
            $io->error('The provided external-ids are empty.');

            return 3;
        }

        $identityCriteria->setExternalIdFilter($externalIds);
        $sourceIdentityRedirectCriteria->setSourceExternalIdFilter(new StringCollection($identityCriteria->getExternalIdFilter()));

        $rows = [];

        $othersCriteria = new IdentityOverviewCriteria();
        $othersCriteria->setSort([
            IdentityOverviewCriteria::FIELD_ENTITY_TYPE => IdentityOverviewCriteria::SORT_ASC,
            IdentityOverviewCriteria::FIELD_MAPPING_NODE => IdentityOverviewCriteria::SORT_ASC,
            IdentityOverviewCriteria::FIELD_PORTAL_NODE => IdentityOverviewCriteria::SORT_ASC,
        ]);
        $sourceIdentityRedirectCriteria->setSort([
            IdentityRedirectOverviewCriteria::FIELD_ENTITY_TYPE => IdentityRedirectOverviewCriteria::SORT_ASC,
            IdentityRedirectOverviewCriteria::FIELD_TARGET_PORTAL_NODE => IdentityRedirectOverviewCriteria::SORT_ASC,
            IdentityRedirectOverviewCriteria::FIELD_TARGET_EXTERNAL_ID => IdentityRedirectOverviewCriteria::SORT_ASC,
        ]);

        foreach ($this->identityOverviewAction->overview($identityCriteria) as $identity) {
            $othersCriteria->getMappingNodeKeyFilter()->push([$identity->getMappingNodeKey()]);
        }

        if (!$othersCriteria->getMappingNodeKeyFilter()->isEmpty()) {
            foreach ($this->identityOverviewAction->overview($othersCriteria) as $identity) {
                $rows[] = [
                    'portal-node-key' => $this->storageKeyGenerator->serialize($identity->getPortalNodeKey()->withAlias()),
                    'external-id' => $identity->getExternalId(),
                    'group-key' => $this->storageKeyGenerator->serialize($identity->getMappingNodeKey()),
                    'entity-type' => $identity->getEntityType(),
                ];
            }
        }

        $groupKeys = [];

        /** @var IdentityRedirectOverviewResult $identityRedirect */
        foreach ($this->identityRedirectOverviewAction->overview($sourceIdentityRedirectCriteria) as $identityRedirect) {
            $groupKey = $this->storageKeyGenerator->serialize($identityRedirect->getIdentityRedirectKey());

            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identityRedirect->getSourcePortalNodeKey()->withAlias()),
                'external-id' => $identityRedirect->getSourceExternalId(),
                'group-key' => $groupKey,
                'entity-type' => $identityRedirect->getEntityType(),
            ];
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identityRedirect->getTargetPortalNodeKey()->withAlias()),
                'external-id' => $identityRedirect->getTargetExternalId(),
                'group-key' => $groupKey,
                'entity-type' => $identityRedirect->getEntityType(),
            ];

            $groupKeys[] = $groupKey;
        }

        $targetIdentityRedirectCriteria = new IdentityRedirectOverviewCriteria();
        $targetIdentityRedirectCriteria->setSort($sourceIdentityRedirectCriteria->getSort());
        $targetIdentityRedirectCriteria->setTargetExternalIdFilter($sourceIdentityRedirectCriteria->getSourceExternalIdFilter());
        $targetIdentityRedirectCriteria->setTargetPortalNodeKeyFilter($sourceIdentityRedirectCriteria->getSourcePortalNodeKeyFilter());
        $targetIdentityRedirectCriteria->setEntityTypeFilter($sourceIdentityRedirectCriteria->getEntityTypeFilter());

        /** @var IdentityRedirectOverviewResult $identityRedirect */
        foreach ($this->identityRedirectOverviewAction->overview($targetIdentityRedirectCriteria) as $identityRedirect) {
            $groupKey = $this->storageKeyGenerator->serialize($identityRedirect->getIdentityRedirectKey());

            if (\in_array($groupKey, $groupKeys, true)) {
                continue;
            }

            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identityRedirect->getSourcePortalNodeKey()->withAlias()),
                'external-id' => $identityRedirect->getSourceExternalId(),
                'group-key' => $groupKey,
                'entity-type' => $identityRedirect->getEntityType(),
            ];
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($identityRedirect->getTargetPortalNodeKey()->withAlias()),
                'external-id' => $identityRedirect->getTargetExternalId(),
                'group-key' => $groupKey,
                'entity-type' => $identityRedirect->getEntityType(),
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
