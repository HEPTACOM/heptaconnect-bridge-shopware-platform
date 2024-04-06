<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\MappingNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Overview\IdentityOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Persist\IdentityPersistCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Persist\IdentityPersistDeletePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Persist\IdentityPersistPayload;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Persist\IdentityPersistPayloadCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityPersistActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MergeMappingNodes extends Command
{
    protected static $defaultName = 'heptaconnect:mapping-node:merge';

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private IdentityOverviewActionInterface $identityOverviewAction,
        private IdentityPersistActionInterface $identityPersistAction
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('mapping-node-key-from', InputArgument::REQUIRED)
            ->addArgument('mapping-node-key-into', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $mappingNodeFrom = $this->storageKeyGenerator->deserialize((string) $input->getArgument('mapping-node-key-from'));
        $mappingNodeInto = $this->storageKeyGenerator->deserialize((string) $input->getArgument('mapping-node-key-into'));

        if (!$mappingNodeFrom instanceof MappingNodeKeyInterface) {
            $io->error('The provided mapping-node-key-from is not a MappingNodeKeyInterface.');

            return 1;
        }

        if (!$mappingNodeInto instanceof MappingNodeKeyInterface) {
            $io->error('The provided mapping-node-key-into is not a MappingNodeKeyInterface.');

            return 2;
        }

        if ($mappingNodeFrom->equals($mappingNodeInto)) {
            $io->error('The mapping nodes could not be merged as they overlap');

            return 3;
        }

        $criteria = new IdentityOverviewCriteria();
        $criteria->getMappingNodeKeyFilter()->push([$mappingNodeFrom, $mappingNodeInto]);

        $nodesFrom = [];
        $nodesInto = [];
        $entityTypes = [];

        foreach ($this->identityOverviewAction->overview($criteria) as $node) {
            if ($node->getMappingNodeKey()->equals($mappingNodeFrom)) {
                $nodesFrom[] = $node;
            }

            if ($node->getMappingNodeKey()->equals($mappingNodeInto)) {
                $nodesInto[] = $node;
            }

            $entityTypes[(string) $node->getEntityType()] = true;
        }

        unset($node);

        if ($nodesFrom === [] || $nodesInto === []) {
            $io->error('The mapping nodes could not be merged as they overlap');

            return 4;
        }

        if (\count($entityTypes) !== 1) {
            $io->error('The mapping nodes could not be merged as they overlap');

            return 5;
        }

        $intoPortalExistences = [];

        foreach ($nodesInto as $node) {
            $portalNode = $this->storageKeyGenerator->serialize($node->getPortalNodeKey());
            $intoPortalExistences[$portalNode] = $node->getExternalId();
        }

        unset($node);

        $payloads = [];

        foreach ($nodesFrom as $node) {
            $portalNode = $this->storageKeyGenerator->serialize($node->getPortalNodeKey());
            $payloads[$portalNode] ??= new IdentityPersistPayload($node->getPortalNodeKey(), new IdentityPersistPayloadCollection());

            if (\array_key_exists($portalNode, $intoPortalExistences)) {
                if ($intoPortalExistences[$portalNode] !== $node->getExternalId()) {
                    $io->error('The mapping nodes could not be merged as they overlap');

                    return 6;
                }
            } else {
                $payloads[$portalNode]->getIdentityPersistPayloads()->push([
                    new IdentityPersistCreatePayload($mappingNodeInto, $node->getExternalId()),
                ]);
            }

            $payloads[$portalNode]->getIdentityPersistPayloads()->push([
                new IdentityPersistDeletePayload($node->getMappingNodeKey()),
            ]);
        }

        foreach ($payloads as $payload) {
            $this->identityPersistAction->persist($payload);
        }

        return 0;
    }
}
