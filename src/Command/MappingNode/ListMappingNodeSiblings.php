<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\ExplorerCollection;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\ReceiverCollection;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\MappingKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\MappingNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\PreviewPortalNodeKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListMappingNodeSiblings extends Command
{
    protected static $defaultName = 'heptaconnect:mapping-node:siblings-list';

    private ComposerPortalLoader $portalLoader;

    private PortalNodeRepositoryContract $portalNodeRepository;

    private MappingRepositoryContract $mappingRepository;

    private MappingNodeRepositoryContract $mappingNodeRepository;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalStackServiceContainerFactory $portalStackServiceContainerFactory;

    public function __construct(
        ComposerPortalLoader $portalLoader,
        PortalNodeRepositoryContract $portalNodeRepository,
        MappingRepositoryContract $mappingRepository,
        MappingNodeRepositoryContract $mappingNodeRepository,
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalStackServiceContainerFactory $portalStackServiceContainerFactory
    ) {
        parent::__construct();
        $this->portalLoader = $portalLoader;
        $this->portalNodeRepository = $portalNodeRepository;
        $this->mappingRepository = $mappingRepository;
        $this->mappingNodeRepository = $mappingNodeRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalStackServiceContainerFactory = $portalStackServiceContainerFactory;
    }

    protected function configure()
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

        if ($entityType !== '' && !\is_a($entityType, DatasetEntityContract::class, true)) {
            $io->error('The provided dataset entity class does not implement the DatasetEntityContract.');

            return 1;
        }

        if ($portalNodeKeyParam !== '' && !\is_a($this->storageKeyGenerator->deserialize($portalNodeKeyParam), PortalNodeKeyInterface::class, false)) {
            $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

            return 2;
        }

        $externalIds = \array_filter($externalIds);

        if ($externalIds === []) {
            $io->error('The provided external-ids are empty.');

            return 3;
        }

        $portalNodeKeys = [];

        if ($portalNodeKeyParam === '') {
            $portalNodeKeys = \iterable_to_array($this->portalNodeRepository->listAll());
        } else {
            $portalNodeKeys[] = $this->storageKeyGenerator->deserialize($portalNodeKeyParam);
        }

        $types = [];

        if ($entityType === '') {
            $types = $this->getEntityTypes();
        } else {
            $types[] = $entityType;
        }

        $rows = [];

        foreach ($portalNodeKeys as $portalNodeKey) {
            foreach ($types as $type) {
                $nodeKeys = $this->mappingNodeRepository->listByTypeAndPortalNodeAndExternalIds(
                    $type,
                    $portalNodeKey,
                    $externalIds
                );

                /** @var MappingNodeKeyInterface $nodeKey */
                foreach ($nodeKeys as $nodeKey) {
                    $mappingKeys = $this->mappingRepository->listByMappingNode($nodeKey);

                    /** @var MappingKeyInterface $mappingKey */
                    foreach ($mappingKeys as $mappingKey) {
                        $mapping = $this->mappingRepository->read($mappingKey);

                        if (\is_null($mapping->getExternalId())) {
                            continue;
                        }

                        $rows[] = [
                            'portal-node-key' => $this->storageKeyGenerator->serialize($mapping->getPortalNodeKey()),
                            'external-id' => $mapping->getExternalId(),
                            'mapping-node-key' => $this->storageKeyGenerator->serialize($mapping->getMappingNodeKey()),
                            'entity-type' => $mapping->getEntityType(),
                        ];
                    }
                }
            }
        }

        if (empty($rows)) {
            $io->note('There are no mapping nodes of the selected portal with given external id.');

            return 0;
        }

        \usort($rows, static fn (array $a, array $b) => ($a['entity-type'] <=> $b['entity-type']) * 10
            + ($a['mapping-node-key'] <=> $b['mapping-node-key']) * 5
            + ($a['portal-node-key'] <=> $b['portal-node-key'])
        );

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }

    /**
     * @TODO extract into service and resolve unroutable but mappable subtypes
     */
    protected function getEntityTypes(): array
    {
        $result = [];

        /** @var PortalContract $portal */
        foreach ($this->portalLoader->getPortals() as $portal) {
            $container = $this->portalStackServiceContainerFactory->create(new PreviewPortalNodeKey(\get_class($portal)));

            /** @var ExplorerCollection $explorers */
            $explorers = $container->get(ExplorerCollection::class);
            /** @var ExplorerCollection $explorerDecorators */
            $explorerDecorators = $container->get(ExplorerCollection::class.'.decorator');
            $explorers->push($explorerDecorators);

            /** @var EmitterCollection $emitters */
            $emitters = $container->get(EmitterCollection::class);
            /** @var EmitterCollection $emitterDecorators */
            $emitterDecorators = $container->get(EmitterCollection::class.'.decorator');
            $emitters->push($emitterDecorators);

            /** @var ReceiverCollection $receivers */
            $receivers = $container->get(ReceiverCollection::class);
            /** @var ReceiverCollection $receiverDecorators */
            $receiverDecorators = $container->get(ReceiverCollection::class.'.decorator');
            $receivers->push($receiverDecorators);

            /** @var ExplorerContract $explorer */
            foreach ($explorers as $explorer) {
                $result[$explorer->supports()] = true;
            }

            /** @var EmitterContract $emitter */
            foreach ($emitters as $emitter) {
                $result[$emitter->supports()] = true;
            }

            /** @var ReceiverContract $receiver */
            foreach ($receivers as $receiver) {
                $result[$receiver->supports()] = true;
            }
        }

        return \array_keys($result);
    }
}
