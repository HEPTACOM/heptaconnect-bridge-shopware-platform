<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Dataset\Base\EntityType;
use Heptacom\HeptaConnect\Dataset\Base\Exception\InvalidClassNameException;
use Heptacom\HeptaConnect\Dataset\Base\Exception\InvalidSubtypeClassNameException;
use Heptacom\HeptaConnect\Dataset\Base\Exception\UnexpectedLeadingNamespaceSeparatorInClassNameException;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterCodeOriginFinderInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerCodeOriginFinderInterface;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\ExplorerCollection;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverCodeOriginFinderInterface;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\ReceiverCollection;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterCodeOriginFinderInterface;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\StatusReporterCollection;
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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListFlowComponentsForPortalNode extends Command
{
    private const FLOW_COMPONENT_INPUT_MAP = [
        'emitter' => EmitterContract::class,
        'explorer' => ExplorerContract::class,
        'http-handler' => HttpHandlerContract::class,
        'receiver' => ReceiverContract::class,
        'status-reporter' => StatusReporterContract::class,
    ];

    protected static $defaultName = 'heptaconnect:portal-node:list-flow-components';

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private PortalStackServiceContainerFactory $portalStackServiceContainerFactory,
        private HttpHandlerCodeOriginFinderInterface $httpHandlerCodeOriginFinder,
        private EmitterCodeOriginFinderInterface $emitterCodeOriginFinder,
        private ExplorerCodeOriginFinderInterface $explorerCodeOriginFinder,
        private ReceiverCodeOriginFinderInterface $receiverCodeOriginFinder,
        private StatusReporterCodeOriginFinderInterface $statusReporterCodeOriginFinder
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('entity-type', InputArgument::REQUIRED);
        $this->addArgument('flow-component-contract', InputArgument::REQUIRED);
        $this->addOption('pretty', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
            }
        } catch (UnsupportedStorageKeyException) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $entityType = (string) $input->getArgument('entity-type');
        $flowComponentContract = (string) $input->getArgument('flow-component-contract');
        $isPretty = (bool) $input->getOption('pretty');
        $flowComponentContract = self::FLOW_COMPONENT_INPUT_MAP[$flowComponentContract] ?? $flowComponentContract;

        if (
            $flowComponentContract !== HttpHandlerContract::class
            && $flowComponentContract !== StatusReporterContract::class
        ) {
            try {
                new EntityType($entityType);
            } catch (\Throwable) {
                $io->error('The specified type does not implement the DatasetEntityContract.');

                return 1;
            }
        }

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
            case StatusReporterContract::class:
                $flowComponentDescriptions = $this->getStatusReporterImplementations($portalNodeKey, $entityType);

                break;
            default:
                $io->error('The specified flow-component-contract does not exist.');
        }

        $flowComponentDescriptions = \array_map('strval', $flowComponentDescriptions);
        $flags = $isPretty ? (\JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES) : 0;
        $io->writeln((string) \json_encode($flowComponentDescriptions, $flags | \JSON_THROW_ON_ERROR));

        return 0;
    }

    /**
     * @throws InvalidClassNameException
     * @throws InvalidSubtypeClassNameException
     * @throws UnexpectedLeadingNamespaceSeparatorInClassNameException
     */
    private function getExplorerImplementations(PortalNodeKeyInterface $portalNodeKey, string $entityType): array
    {
        $flowComponentRegistry = $this->portalStackServiceContainerFactory->create($portalNodeKey)->getFlowComponentRegistry();
        $components = new ExplorerCollection();

        foreach ($flowComponentRegistry->getOrderedSources() as $source) {
            $components->push($flowComponentRegistry->getExplorers($source));
        }

        $components = $components->bySupport(new EntityType($entityType));

        return \iterable_to_array($components->map([$this->explorerCodeOriginFinder, 'findOrigin']));
    }

    /**
     * @throws InvalidClassNameException
     * @throws InvalidSubtypeClassNameException
     * @throws UnexpectedLeadingNamespaceSeparatorInClassNameException
     */
    private function getReceiverImplementations(PortalNodeKeyInterface $portalNodeKey, string $entityType): array
    {
        $flowComponentRegistry = $this->portalStackServiceContainerFactory->create($portalNodeKey)->getFlowComponentRegistry();
        $components = new ReceiverCollection();

        foreach ($flowComponentRegistry->getOrderedSources() as $source) {
            $components->push($flowComponentRegistry->getReceivers($source));
        }

        $components = $components->bySupport(new EntityType($entityType));

        return \iterable_to_array($components->map([$this->receiverCodeOriginFinder, 'findOrigin']));
    }

    /**
     * @throws InvalidClassNameException
     * @throws InvalidSubtypeClassNameException
     * @throws UnexpectedLeadingNamespaceSeparatorInClassNameException
     */
    private function getEmitterImplementations(PortalNodeKeyInterface $portalNodeKey, string $entityType): array
    {
        $flowComponentRegistry = $this->portalStackServiceContainerFactory->create($portalNodeKey)->getFlowComponentRegistry();
        $components = new EmitterCollection();

        foreach ($flowComponentRegistry->getOrderedSources() as $source) {
            $components->push($flowComponentRegistry->getEmitters($source));
        }

        $components = $components->bySupport(new EntityType($entityType));

        return \iterable_to_array($components->map([$this->emitterCodeOriginFinder, 'findOrigin']));
    }

    private function getHttpHandlerImplementations(PortalNodeKeyInterface $portalNodeKey, string $path): array
    {
        $flowComponentRegistry = $this->portalStackServiceContainerFactory->create($portalNodeKey)->getFlowComponentRegistry();
        $components = new HttpHandlerCollection();

        foreach ($flowComponentRegistry->getOrderedSources() as $source) {
            $components->push($flowComponentRegistry->getWebHttpHandlers($source));
        }

        $components = $components->bySupport($path);

        return \iterable_to_array($components->map([$this->httpHandlerCodeOriginFinder, 'findOrigin']));
    }

    private function getStatusReporterImplementations(PortalNodeKeyInterface $portalNodeKey, string $topic): array
    {
        $flowComponentRegistry = $this->portalStackServiceContainerFactory->create($portalNodeKey)->getFlowComponentRegistry();
        $components = new StatusReporterCollection();

        foreach ($flowComponentRegistry->getOrderedSources() as $source) {
            $components->push($flowComponentRegistry->getStatusReporters($source));
        }

        $components = $components->bySupportedTopic($topic);

        return \iterable_to_array($components->map([$this->statusReporterCodeOriginFinder, 'findOrigin']));
    }
}
