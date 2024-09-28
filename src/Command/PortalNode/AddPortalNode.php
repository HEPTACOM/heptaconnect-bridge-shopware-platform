<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\AliasValidator;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\PortalType;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Create\PortalNodeCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Create\PortalNodeCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeySerializerContract;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:portal-node:add')]
class AddPortalNode extends Command
{
    public function __construct(
        private readonly StorageKeySerializerContract $storageKeySerializer,
        private readonly PortalNodeCreateActionInterface $portalNodeCreateAction,
        private readonly AliasValidator $aliasValidator
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('portal-class', InputArgument::REQUIRED);
        $this->addArgument('alias', InputArgument::OPTIONAL);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $portalClass = (string) $input->getArgument('portal-class');
        $alias = (string) $input->getArgument('alias');

        if (!\is_a($portalClass, PortalContract::class, true)) {
            $io->error('The provided portal class does not implement the PortalContract.');

            return 1;
        }

        if ($alias !== '') {
            $this->aliasValidator->validate($alias);

            $result = $this->portalNodeCreateAction->create(new PortalNodeCreatePayloads([new PortalNodeCreatePayload(new PortalType($portalClass), $alias)]));
        } else {
            $result = $this->portalNodeCreateAction->create(new PortalNodeCreatePayloads([new PortalNodeCreatePayload(new PortalType($portalClass), null)]));
        }

        $io->success(\sprintf('A new portal node was created. ID: %s', $this->storageKeySerializer->serialize($result->first()->getPortalNodeKey())));

        return 0;
    }
}
