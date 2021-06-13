<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Core\Portal\Contract\PortalRegistryInterface;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\ExplorerCollection;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListRoutes extends Command
{
    protected static $defaultName = 'heptaconnect:router:list-routes';

    private RouteRepositoryContract $routeRepository;

    private PortalNodeRepositoryContract $portalNodeRepository;

    private PortalRegistryInterface $portalRegistry;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalStackServiceContainerFactory $portalStackServiceContainerFactory;

    public function __construct(
        RouteRepositoryContract $routeRepository,
        PortalNodeRepositoryContract $portalNodeRepository,
        PortalRegistryInterface $portalRegistry,
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalStackServiceContainerFactory $portalStackServiceContainerFactory
    ) {
        parent::__construct();
        $this->routeRepository = $routeRepository;
        $this->portalNodeRepository = $portalNodeRepository;
        $this->portalRegistry = $portalRegistry;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalStackServiceContainerFactory = $portalStackServiceContainerFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $types = [];

        foreach ($this->portalNodeRepository->listAll() as $portalNodeKey) {
            $portal = $this->portalRegistry->getPortal($portalNodeKey);
            $container = $this->portalStackServiceContainerFactory->create($portalNodeKey);

            /** @var EmitterCollection $emitters */
            $emitters = $container->get(EmitterCollection::class);
            /** @var EmitterCollection $emitterDecorators */
            $emitterDecorators = $container->get(EmitterCollection::class.'.decorator');
            $emitters->push($emitterDecorators);

            /** @var ExplorerCollection $explorers */
            $explorers = $container->get(ExplorerCollection::class);
            /** @var ExplorerCollection $explorerDecorators */
            $explorerDecorators = $container->get(ExplorerCollection::class.'.decorator');
            $explorers->push($explorerDecorators);

            /** @var ExplorerContract $explorer */
            foreach ($explorers as $explorer) {
                $types[$explorer->supports()] = true;
            }

            /** @var EmitterContract $emitter */
            foreach ($emitters as $emitter) {
                $types[$emitter->supports()] = true;
            }

            /** @var ReceiverContract $receiver */
            foreach ($portal->getReceivers() as $receiver) {
                $types[$receiver->supports()] = true;
            }

            /** @var PortalExtensionContract $portalExtension */
            foreach ($this->portalRegistry->getPortalExtensions($portalNodeKey) as $portalExtension) {
                /** @var ReceiverContract $receiver */
                foreach ($portalExtension->getReceiverDecorators() as $receiver) {
                    $types[$receiver->supports()] = true;
                }
            }
        }

        $types = \array_keys($types);
        $targets = [];

        foreach ($this->portalNodeRepository->listAll() as $portalNodeKey) {
            if (!$portalNodeKey instanceof PortalNodeStorageKey) {
                continue;
            }

            foreach ($types as $type) {
                foreach ($this->routeRepository->listBySourceAndEntityType($portalNodeKey, $type) as $routeKey) {
                    $route = $this->routeRepository->read($routeKey);

                    $targets[] = [
                        'type' => $type,
                        'source' => $this->storageKeyGenerator->serialize($portalNodeKey),
                        'target' => $this->storageKeyGenerator->serialize($route->getTargetKey()),
                    ];
                }
            }
        }

        if (\count($targets) === 0) {
            $io->note('There are no routes.');

            return 0;
        }

        $io->table(\array_keys(\current($targets)), $targets);

        return 0;
    }
}
