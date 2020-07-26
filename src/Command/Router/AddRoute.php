<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\PortalNodeKey;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddRoute extends Command
{
    protected static $defaultName = 'heptaconnect:router:add-route';

    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    protected function configure()
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED)
            ->addArgument('target', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $source = new PortalNodeKey($input->getArgument('source'));
        $target = new PortalNodeKey($input->getArgument('target'));
        $type = $input->getArgument('type');

        $this->storage->getPortalNode($source);
        $this->storage->getPortalNode($target);

        if (!\is_a($type, DatasetEntityInterface::class, true)) {
            $io->error('The specified type does not implement the DatasetEntityInterface.');

            return 1;
        }

        $this->storage->createRouteTarget($source, $target, $type);

        return 0;
    }
}
