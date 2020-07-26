<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\PortalNodeKey;
use Heptacom\HeptaConnect\Core\Portal\Contract\PortalNodeRegistryInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\EmitterInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\ExplorerInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\ReceiverInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListRoutes extends Command
{
    protected static $defaultName = 'heptaconnect:router:list-routes';

    private StorageInterface $storage;

    private PortalNodeRegistryInterface $portalNodeRegistry;

    public function __construct(StorageInterface $storage, PortalNodeRegistryInterface $portalNodeRegistry)
    {
        parent::__construct();
        $this->storage = $storage;
        $this->portalNodeRegistry = $portalNodeRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $types = [];
        $portalNodeKeys = $this->storage->listPortalNodes();

        /** @var PortalNodeKeyInterface $portalNodeKey */
        foreach ($portalNodeKeys as $portalNodeKey) {
            $portalNode = $this->portalNodeRegistry->getPortalNode($portalNodeKey);

            /** @var ExplorerInterface $explorer */
            foreach ($portalNode->getExplorers() as $explorer) {
                $types[$explorer->supports()] = true;
            }

            /** @var EmitterInterface $emitter */
            foreach ($portalNode->getEmitters() as $emitter) {
                /** @var string $support */
                foreach ($emitter->supports() as $support) {
                    $types[$support] = true;
                }
            }

            /** @var ReceiverInterface $receiver */
            foreach ($portalNode->getReceivers() as $receiver) {
                /** @var string $support */
                foreach ($receiver->supports() as $support) {
                    $types[$support] = true;
                }
            }
        }

        $types = array_keys($types);
        $targets = [];

        /** @var PortalNodeKeyInterface $portalNodeKey */
        foreach ($portalNodeKeys as $portalNodeKey) {
            if (!$portalNodeKey instanceof PortalNodeKey) {
                continue;
            }

            foreach ($types as $type) {
                /** @var PortalNodeKeyInterface $target */
                foreach ($this->storage->getRouteTargets($portalNodeKey, $type) as $target) {
                    if (!$target instanceof PortalNodeKey) {
                        continue;
                    }

                    $targets[] = [
                        'type' => $type,
                        'source' => $portalNodeKey->getUuid(),
                        'target' => $target->getUuid(),
                    ];
                }
            }
        }

        if (empty($targets)) {
            $io->note('There are no routes.');

            return 0;
        }


        $io->table(array_keys(current($targets)), $targets);

        return 0;
    }
}
