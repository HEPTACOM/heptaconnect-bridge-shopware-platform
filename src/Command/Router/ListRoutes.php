<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Core\Portal\Contract\PortalRegistryInterface;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListRoutes extends Command
{
    protected static $defaultName = 'heptaconnect:router:list-routes';

    private StorageInterface $storage;

    private PortalRegistryInterface $portalRegistry;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        StorageInterface $storage,
        PortalRegistryInterface $portalRegistry,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->storage = $storage;
        $this->portalRegistry = $portalRegistry;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $types = [];
        $portalNodeKeys = $this->storage->listPortalNodes();

        /** @var PortalNodeKeyInterface $portalNodeKey */
        foreach ($portalNodeKeys as $portalNodeKey) {
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

        /** @var PortalNodeKeyInterface $portalNodeKey */
        foreach ($portalNodeKeys as $portalNodeKey) {
            if (!$portalNodeKey instanceof PortalNodeStorageKey) {
                continue;
            }

            foreach ($types as $type) {
                /** @var PortalNodeKeyInterface $target */
                foreach ($this->storage->getRouteTargets($portalNodeKey, $type) as $target) {
                    if (!$target instanceof PortalNodeStorageKey) {
                        continue;
                    }

                    $targets[] = [
                        'type' => $type,
                        'source' => $this->storageKeyGenerator->serialize($portalNodeKey),
                        'target' => $this->storageKeyGenerator->serialize($target),
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
