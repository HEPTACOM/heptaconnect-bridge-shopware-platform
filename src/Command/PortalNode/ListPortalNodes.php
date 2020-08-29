<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListPortalNodes extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:list';

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

        $portalClass = (string) $input->getArgument('portal-class');

        if ($portalClass !== '') {
            if (!\is_a($portalClass, PortalContract::class, true)) {
                $io->error('The provided portal class does not implement the PortalContract.');

                return 1;
            }
        } else {
            $portalClass = null;
        }

        $portalNodeKeys = $this->storage->listPortalNodes($portalClass);

        if ($portalNodeKeys->count() === 0) {
            $io->note('There are no portal nodes of the selected portal.');

            return 0;
        }

        $rows = [];

        /** @var PortalNodeStorageKey $portalNodeKey */
        foreach ($portalNodeKeys as $portalNodeKey) {
            $rows[] = [
                'portal-id' => $portalNodeKey->getUuid(),
                'portal-class' => $this->storage->getPortalNode($portalNodeKey),
            ];
        }

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
