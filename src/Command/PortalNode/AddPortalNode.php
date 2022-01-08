<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Create\PortalNodeCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Create\PortalNodeCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddPortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:add';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalNodeCreateActionInterface $portalNodeCreateAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalNodeCreateActionInterface $portalNodeCreateAction
    ) {
        parent::__construct();

        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeCreateAction = $portalNodeCreateAction;
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

        $result = $this->portalNodeCreateAction->create(new PortalNodeCreatePayloads([new PortalNodeCreatePayload($portalClass)]));

        $io->success(\sprintf('A new portal node was created. ID: %s', $this->storageKeyGenerator->serialize($result->first()->getPortalNodeKey())));

        return 0;
    }
}
