<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Core\Exploration\ExplorerStackBuilderFactory;
use Heptacom\HeptaConnect\Core\Portal\FlowComponentRegistry;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Core\Reception\ReceiverStackBuilderFactory;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterCodeOriginFinderInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\ExplorerStack;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\ReceiverStack;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerCodeOriginFinderInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListFlowComponentsForPortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:list-flow-components';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private ExplorerStackBuilderFactory $explorerStackBuilderFactory;

    private ReceiverStackBuilderFactory $receiverStackBuilderFactory;

    private PortalStackServiceContainerFactory $portalStackServiceContainerFactory;

    private HttpHandlerCodeOriginFinderInterface $httpHandlerCodeOriginFinder;

    private EmitterCodeOriginFinderInterface $emitterCodeOriginFinder;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        ExplorerStackBuilderFactory $explorerStackBuilderFactory,
        ReceiverStackBuilderFactory $receiverStackBuilderFactory,
        PortalStackServiceContainerFactory $portalStackServiceContainerFactory,
        HttpHandlerCodeOriginFinderInterface $httpHandlerCodeOriginFinder,
        EmitterCodeOriginFinderInterface $emitterCodeOriginFinder
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->explorerStackBuilderFactory = $explorerStackBuilderFactory;
        $this->receiverStackBuilderFactory = $receiverStackBuilderFactory;
        $this->portalStackServiceContainerFactory = $portalStackServiceContainerFactory;
        $this->httpHandlerCodeOriginFinder = $httpHandlerCodeOriginFinder;
        $this->emitterCodeOriginFinder = $emitterCodeOriginFinder;
    }

    protected function configure()
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('entity-type', InputArgument::REQUIRED);
        $this->addArgument('flow-component-contract', InputArgument::REQUIRED);
        $this->addOption('pretty', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
            }
        } catch (UnsupportedStorageKeyException $exception) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $entityType = $input->getArgument('entity-type');

        if (!\is_a($entityType, DatasetEntityContract::class, true)) {
            $io->error('The specified type does not implement the DatasetEntityContract.');

            return 1;
        }

        $flowComponentContract = (string) $input->getArgument('flow-component-contract');
        $flowComponentDescriptions = [];

        switch ($flowComponentContract) {
            case ExplorerContract::class:
                $flowComponentDescriptions = $this->getExplorerImplementations($portalNodeKey, $entityType);
                break;
            case ReceiverContract::class:
                $flowComponentDescriptions = $this->getReceiverImplementations($portalNodeKey, $entityType);
                break;
            case EmitterContract::class:
                $flowComponentDescriptions = $this->getEmitterImplementations($portalNodeKey, $entityType);
                break;
            case HttpHandlerContract::class:
                $flowComponentDescriptions = $this->getHttpHandlerImplementations($portalNodeKey, $entityType);
                break;
            default:
                $io->error('The specified flow-component-contract does not exist.');
        }

        $flowComponentDescriptions = \array_map('strval', $flowComponentDescriptions);
        $flags = $input->getOption('pretty') ? \JSON_PRETTY_PRINT : 0;
        $io->writeln(\json_encode($flowComponentDescriptions, $flags));

        return 0;
    }

    private function getExplorerImplementations(PortalNodeKeyInterface $portalNodeKey, string $entityType): array
    {
        $explorerStackBuilder = $this->explorerStackBuilderFactory
            ->createExplorerStackBuilder($portalNodeKey, $entityType)
            ->pushSource()
            ->pushDecorators();

        /**
         * @var ExplorerStack $explorerStack
         */
        $explorerStack = $explorerStackBuilder->build();

        return $explorerStack->listOrigins();
    }

    private function getReceiverImplementations(PortalNodeKeyInterface $portalNodeKey, string $entityType): array
    {
        $receiverStackBuilder = $this->receiverStackBuilderFactory
            ->createReceiverStackBuilder($portalNodeKey, $entityType)
            ->pushSource()
            ->pushDecorators();
        /**
         * @var ReceiverStack $receiverStack
         */
        $receiverStack = $receiverStackBuilder->build();

        return $receiverStack->listOrigins();
    }

    private function getEmitterImplementations(PortalNodeKeyInterface $portalNodeKey, string $entityType): array
    {
        $container = $this->portalStackServiceContainerFactory->create($portalNodeKey);
        /** @var FlowComponentRegistry $flowComponentRegistry */
        $flowComponentRegistry = $container->get(FlowComponentRegistry::class);
        $components = new EmitterCollection();

        foreach ($flowComponentRegistry->getOrderedSources() as $source) {
            $components->push($flowComponentRegistry->getWebHttpHandlers($source));
        }

        $components = new EmitterCollection($components->bySupport($entityType));

        return \iterable_to_array($components->map([$this->emitterCodeOriginFinder, 'findOrigin']));
    }

    private function getHttpHandlerImplementations(PortalNodeKeyInterface $portalNodeKey, string $path): array
    {
        $container = $this->portalStackServiceContainerFactory->create($portalNodeKey);
        /** @var FlowComponentRegistry $flowComponentRegistry */
        $flowComponentRegistry = $container->get(FlowComponentRegistry::class);
        $components = new HttpHandlerCollection();

        foreach ($flowComponentRegistry->getOrderedSources() as $source) {
            $components->push($flowComponentRegistry->getWebHttpHandlers($source));
        }

        $components = new HttpHandlerCollection($components->bySupport($path));

        return \iterable_to_array($components->map([$this->httpHandlerCodeOriginFinder, 'findOrigin']));
    }
}
