<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\PortalRegistry;
use Heptacom\HeptaConnect\Portal\Base\Contract\EmitterInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\ExplorerInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\ReceiverInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DataTypeList extends Command
{
    protected static $defaultName = 'heptaconnect:data-type:list';

    private StorageInterface $storage;

    private PortalRegistry $portalRegistry;

    public function __construct(StorageInterface $storage, PortalRegistry $portalRegistry)
    {
        parent::__construct();
        $this->storage = $storage;
        $this->portalRegistry = $portalRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $types = [];

        foreach ($this->portalRegistry->getPortals() as $portal) {
            /** @var ExplorerInterface $explorer */
            foreach ($portal->getExplorers() as $explorer) {
                $types[$explorer->supports()] = true;
            }

            /** @var EmitterInterface $emitter */
            foreach ($portal->getEmitters() as $emitter) {
                /** @var string $support */
                foreach ($emitter->supports() as $support) {
                    $types[$support] = true;
                }
            }

            /** @var ReceiverInterface $receiver */
            foreach ($portal->getReceivers() as $receiver) {
                /** @var string $support */
                foreach ($receiver->supports() as $support) {
                    $types[$support] = true;
                }
            }
        }

        if (empty($types)) {
            $io->note('There are no supported data types.');

            return 0;
        }

        $types = \array_map(fn(string $type) => ['data-type' => $type], \array_keys($types));
        $io->table(\array_keys(\current($types)), $types);

        return 0;
    }
}
