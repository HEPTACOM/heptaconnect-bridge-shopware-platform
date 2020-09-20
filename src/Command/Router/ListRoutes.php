<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Core\Portal\Contract\PortalRegistryInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
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

    public function __construct(
        RouteRepositoryContract $routeRepository,
        PortalNodeRepositoryContract $portalNodeRepository,
        PortalRegistryInterface $portalRegistry,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->routeRepository = $routeRepository;
        $this->portalNodeRepository = $portalNodeRepository;
        $this->portalRegistry = $portalRegistry;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $types = [];

        foreach ($this->portalNodeRepository->listAll() as $portalNodeKey) {
            $portal = $this->portalRegistry->getPortal($portalNodeKey);

            /** @var ExplorerContract $explorer */
            foreach ($portal->getExplorers() as $explorer) {
                $types[$explorer->supports()] = true;
            }

            /** @var EmitterContract $emitter */
            foreach ($portal->getEmitters() as $emitter) {
                /** @var string $support */
                foreach ($emitter->supports() as $support) {
                    $types[$support] = true;
                }
            }

            /** @var ReceiverContract $receiver */
            foreach ($portal->getReceivers() as $receiver) {
                /** @var string $support */
                foreach ($receiver->supports() as $support) {
                    $types[$support] = true;
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
