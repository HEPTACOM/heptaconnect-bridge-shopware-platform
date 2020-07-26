<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\PortalNodeKey;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemovePortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:remove';

    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $portalNodeKey = new PortalNodeKey((string) $input->getArgument('portal-id'));
        $this->storage->removePortalNode($portalNodeKey);

        $io->success('The portal node was successfully removed.');

        return 0;
    }
}
