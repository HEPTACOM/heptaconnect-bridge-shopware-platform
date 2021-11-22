<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Core\Emission\Contract\EmitterStackBuilderFactoryInterface;
use Heptacom\HeptaConnect\Core\Exploration\ExplorerStackBuilderFactory;
use Heptacom\HeptaConnect\Core\Reception\ReceiverStackBuilderFactory;
use Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerStackBuilderFactoryInterface;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterStack;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\ExplorerStack;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\ReceiverStack;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerStack;
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

    private EmitterStackBuilderFactoryInterface $emitterStackBuilderFactory;

    private ReceiverStackBuilderFactory $receiverStackBuilderFactory;

    private HttpHandlerStackBuilderFactoryInterface $httpHandlerStackBuilderFactory;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        ExplorerStackBuilderFactory $explorerStackBuilderFactory,
        EmitterStackBuilderFactoryInterface $emitterStackBuilderFactory,
        ReceiverStackBuilderFactory $receiverStackBuilderFactory,
        HttpHandlerStackBuilderFactoryInterface $httpHandlerStackBuilderFactory
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->explorerStackBuilderFactory = $explorerStackBuilderFactory;
        $this->emitterStackBuilderFactory = $emitterStackBuilderFactory;
        $this->receiverStackBuilderFactory = $receiverStackBuilderFactory;
        $this->httpHandlerStackBuilderFactory = $httpHandlerStackBuilderFactory;
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

        $flowComponentContract = $input->getArgument('flow-component-contract');
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
        $emitterStackBuilder = $this->emitterStackBuilderFactory
            ->createEmitterStackBuilder($portalNodeKey, $entityType)
            ->pushSource()
            ->pushDecorators();
        /**
         * @var EmitterStack $emitterStack
         */
        $emitterStack = $emitterStackBuilder->build();

        return $emitterStack->listOrigins();
    }

    private function getHttpHandlerImplementations(PortalNodeKeyInterface $portalNodeKey, string $entityType): array
    {
        $httpHandlerStackBuilder = $this->httpHandlerStackBuilderFactory
            ->createHttpHandlerStackBuilder($portalNodeKey, $entityType)
            ->pushSource()
            ->pushDecorators();
        /**
         * @var HttpHandlerStack $httpStack
         */
        $httpStack = $httpHandlerStackBuilder->build();

        return $httpStack->listOrigins();
    }
}
