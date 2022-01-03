<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\PortalNodeKeyCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Delete\PortalNodeDeleteCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Delete\PortalNodeDeleteActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemovePortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:remove';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalNodeDeleteActionInterface $portalNodeDeleteAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalNodeDeleteActionInterface $portalNodeDeleteAction
    ) {
        parent::__construct();

        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeDeleteAction = $portalNodeDeleteAction;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $key = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

        if (!$key instanceof PortalNodeKeyInterface) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $this->portalNodeDeleteAction->delete(new PortalNodeDeleteCriteria(new PortalNodeKeyCollection([$key])));

        $io->success('The portal node was successfully removed.');

        return 0;
    }
}
