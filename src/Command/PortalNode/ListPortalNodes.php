<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\PortalNodeKey;
use Heptacom\HeptaConnect\Portal\Base\Contract\PortalNodeInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListPortalNodes extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:list';

    /**
     * @var StorageInterface
     */
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    protected function configure()
    {
        $this->addArgument('portal-class', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $portalClass = $input->getArgument('portal-class');

        if ($portalClass) {
            if (!is_a($portalClass, PortalNodeInterface::class, true)) {
                $io->error('The provided portal class does not implement the PortalNodeInterface.');

                return 1;
            }
        } else {
            $portalClass = null;
        }

        $portalNodeKeys = $this->storage->listPortalNodes($portalClass);

        $ids = array_map(fn(PortalNodeKey $portalNodeKey) => [$portalNodeKey->getUuid()], iterable_to_array($portalNodeKeys));

        $io->table(['portal-id'], $ids);

        return 0;
    }
}
