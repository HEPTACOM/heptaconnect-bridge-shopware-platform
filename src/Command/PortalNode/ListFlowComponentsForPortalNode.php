<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Core\Job\Contract\JobContract;
use Heptacom\HeptaConnect\Core\Job\Type\Emission;
use Heptacom\HeptaConnect\Core\Job\Type\Exploration;
use Heptacom\HeptaConnect\Core\Job\Type\Reception;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\Builder\Component\ShorthandFlowComponent;
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
        $io->title('Listing FlowComponents of '.$portalName.' for '.$job);

        /**
         * @var FlowComponentContract $flowComponent
         */
        foreach ($flowComponents->getIterator() as $flowComponent) {
            if ($flowComponent->supports() === $type) {
                if ($flowComponent instanceof ShorthandFlowComponent) {
                    foreach ($flowComponent->getMethods() as $methodName => $method) {
                        /**
                         * @var ReflectionClosure $reflectedExecutionMethod
                         */
                        $reflectedExecutionMethod = $method->getReflector();
                        $methodDescription = \sprintf(
                            'Shorthand implementation found in file %s for method \'%s\' from line %d to %d',
                            $reflectedExecutionMethod->getFileName(),
                            $methodName,
                            $reflectedExecutionMethod->getStartLine(),
                            $reflectedExecutionMethod->getEndLine()
                        );
                        $io->info($methodDescription);
                    }
                } else {
                    $reflectedFlowComponent = new ReflectionClass($flowComponent);
                    $descriptions = [];
                    $fileDescription = \sprintf('Full implementation found in file %s', $reflectedFlowComponent->getFileName());
                    \array_push($descriptions, $fileDescription);
                    foreach ($reflectedFlowComponent->getMethods() as $method) {
                        if ($method->getDeclaringClass()->getName() === $reflectedFlowComponent->getName()) {
                            $methodDescription = \sprintf(
                                'Overridden method \'%s\' found from line %d to %d',
                                $method->getName(),
                                $method->getStartLine(),
                                $method->getEndLine()
                            );
                            \array_push($descriptions, $methodDescription);
                        }
                    }
                    $io->info($descriptions);
                }
            }
        }

        return 0;
    }
}
