<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\MappingKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\MappingNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
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

    public function __construct(
        ComposerPortalLoader $portalLoader,
        PortalNodeRepositoryContract $portalNodeRepository,
        MappingRepositoryContract $mappingRepository,
        MappingNodeRepositoryContract $mappingNodeRepository,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->portalLoader = $portalLoader;
        $this->portalNodeRepository = $portalNodeRepository;
        $this->mappingRepository = $mappingRepository;
        $this->mappingNodeRepository = $mappingNodeRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function configure()
    {
        $this->addArgument('external-ids', InputArgument::REQUIRED | InputArgument::IS_ARRAY);
        $this->addOption('portal-node-key', 'p', InputArgument::OPTIONAL);
        $this->addOption('dataset-entity-class', 'd', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $datasetEntityClass = (string) $input->getOption('dataset-entity-class');
        $portalNodeKeyParam = (string) $input->getOption('portal-node-key');
        $externalIds = (array) $input->getArgument('external-ids');

        if ($datasetEntityClass !== '' && !\is_a($datasetEntityClass, DatasetEntityContract::class, true)) {
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

        if ($datasetEntityClass === '') {
            $types = $this->getDatasetEntityClasses();
        } else {
            $types[] = $datasetEntityClass;
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
                            'dataset-entity-class' => $mapping->getDatasetEntityClassName(),
                        ];
                    }
                }
            }
        }

        if (empty($rows)) {
            $io->note('There are no mapping nodes of the selected portal with given external id.');

            return 0;
        }

        \usort($rows, static fn (array $a, array $b) =>
            ($a['dataset-entity-class'] <=> $b['dataset-entity-class']) * 10
            + ($a['mapping-node-key'] <=> $b['mapping-node-key']) * 5
            + ($a['portal-node-key'] <=> $b['portal-node-key'])
        );

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }

    /**
     * @TODO extract into service and resolve unroutable but mappable subtypes
     */
    protected function getDatasetEntityClasses(): array
    {
        $result = [];

        /** @var PortalContract $portal */
        foreach ($this->portalLoader->getPortals() as $portal) {
            /** @var EmitterContract $emitter */
            foreach ($portal->getEmitters() as $emitter) {
                foreach ($emitter->supports() as $support) {
                    $result[$support] = true;
                }
            }

            /** @var ExplorerContract $explorer */
            foreach ($portal->getExplorers() as $explorer) {
                $result[$explorer->supports()] = true;
            }

            /** @var ReceiverContract $receiver */
            foreach ($portal->getReceivers() as $receiver) {
                foreach ($receiver->supports() as $support) {
                    $result[$support] = true;
                }
            }
        }

        /** @var PortalExtensionContract $portalExtension */
        foreach ($this->portalLoader->getPortalExtensions() as $portalExtension) {
            /** @var EmitterContract $emitter */
            foreach ($portalExtension->getEmitterDecorators() as $emitter) {
                foreach ($emitter->supports() as $support) {
                    $result[$support] = true;
                }
            }

            /** @var ExplorerContract $explorer */
            foreach ($portalExtension->getExplorerDecorators() as $explorer) {
                $result[$explorer->supports()] = true;
            }

            /** @var ReceiverContract $receiver */
            foreach ($portalExtension->getReceiverDecorators() as $receiver) {
                foreach ($receiver->supports() as $support) {
                    $result[$support] = true;
                }
            }
        }

        return \array_keys($result);
    }
}
