<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\PortalNodeKey;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddPortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:add';

    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-class', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $portalClass = (string) $input->getArgument('portal-class');

        if (!\is_a($portalClass, PortalContract::class, true)) {
            $io->error('The provided portal class does not implement the PortalContract.');

            return 1;
        }

        $portalNodeKey = $this->storage->addPortalNode($portalClass);

        if (!$portalNodeKey instanceof PortalNodeKey) {
            $io->error('An error occurred while creating a new portal node. The key does not match the expected instance.');

            return 1;
        }

        $io->success(\sprintf('A new portal node was created. ID: %s', $portalNodeKey->getUuid()));

        return 0;
    }
}
