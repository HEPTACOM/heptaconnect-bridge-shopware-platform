<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Core\Job\Contract\JobContract;
use Heptacom\HeptaConnect\Core\Job\Type\Emission;
use Heptacom\HeptaConnect\Core\Job\Type\Exploration;
use Heptacom\HeptaConnect\Core\Job\Type\Reception;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\Builder\ShorthandFlowComponent;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\Exploration\ExplorerCollection;
use Heptacom\HeptaConnect\Portal\Base\Reception\ReceiverCollection;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Support\Contract\FlowComponentContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Opis\Closure\ReflectionClosure;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListFlowComponentsForPortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:list-flow-components';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalStackServiceContainerFactory $portalStackServiceContainerFactory;

    private PortalNodeRepositoryContract $portalNodeRepository;

    private array $jobToCollectionMap = [
        Exploration::class => ExplorerCollection::class,
        Emission::class => EmitterCollection::class,
        Reception::class => ReceiverCollection::class,
    ];

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalStackServiceContainerFactory $portalStackServiceContainerFactory,
        PortalNodeRepositoryContract $portalNodeRepository
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalStackServiceContainerFactory = $portalStackServiceContainerFactory;
        $this->portalNodeRepository = $portalNodeRepository;
    }

    protected function configure()
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('type', InputArgument::REQUIRED);
        $this->addArgument('job', InputArgument::REQUIRED);
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

        $type = $input->getArgument('type');

        if (!\is_a($type, DatasetEntityContract::class, true)) {
            $io->error('The specified type does not implement the DatasetEntityContract.');

            return 1;
        }

        $job = $input->getArgument('job');

        if (!\is_a($job, JobContract::class, true)) {
            $io->error('The specified type does not implement the JobContract.');

            return 1;
        }

        $container = $this->portalStackServiceContainerFactory->create($portalNodeKey);
        $flowComponents = $container->get($this->jobToCollectionMap[$job]);
        $flowComponentDecorators = $container->get($this->jobToCollectionMap[$job].'.decorator');
        $flowComponents->push($flowComponentDecorators);
        $portalName = $this->portalNodeRepository->read($portalNodeKey);

        /**
         * @var FlowComponentContract $flowComponent
         */
        $flowComponentDescriptions = [];
        foreach ($flowComponents->getIterator() as $flowComponent) {
            if ($flowComponent->supports() === $type) {
                if ($flowComponent instanceof ShorthandFlowComponent) {
                    foreach ($flowComponent->getMethods() as $methodName => $method) {
                        /**
                         * @var ReflectionClosure $reflectedMethod
                         */
                        $reflectedMethod = $method->getReflector();
                        $method = [
                            'method' => $methodName,
                            'start_line' => $reflectedMethod->getStartLine(),
                            'end_line' => $reflectedMethod->getEndLine(),
                        ];
                        $flowComponentDescription = [
                            'file_name' => $reflectedMethod->getFileName(),
                            'type' => 'shorthand_implementation',
                            'methods' => [$method],
                        ];
                        $flowComponentDescriptions[] = $flowComponentDescription;
                    }
                } else {
                    $reflectedFlowComponent = new ReflectionClass($flowComponent);
                    $methodDescriptions = [];
                    foreach ($reflectedFlowComponent->getMethods() as $reflectedMethod) {
                        if ($reflectedMethod->getDeclaringClass()->getName() === $reflectedFlowComponent->getName()) {
                            $method = [
                                'method' => $reflectedMethod->getName(),
                                'start_line' => $reflectedMethod->getStartLine(),
                                'end_line' => $reflectedMethod->getEndLine(),
                            ];
                            $methodDescriptions[] = $method;
                        }
                    }
                    $flowComponentDescription = [
                        'file_name' => $reflectedFlowComponent->getFileName(),
                        'type' => 'full_implementation',
                        'methods' => $methodDescriptions,
                    ];
                    $flowComponentDescriptions[] = $flowComponentDescription;
                }
            }
        }
        $description = [
            'portal' => $portalName,
            'job' => $job,
            'flowComponents' => $flowComponentDescriptions,
        ];
        $flags = $input->getOption('pretty') ? \JSON_PRETTY_PRINT : 0;
        $io->writeln(\json_encode($description, $flags));

        return 0;
    }
}
