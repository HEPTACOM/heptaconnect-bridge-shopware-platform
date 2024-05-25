<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Set\PortalNodeAliasSetPayload;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Set\PortalNodeAliasSetPayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasSetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\InvalidCreatePayloadException;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Heptacom\HeptaConnect\Storage\Base\Exception\UpdateException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:portal-node:alias:reset')]
class Reset extends Command
{
    public function __construct(
        private PortalNodeAliasSetActionInterface $aliasSetAction,
        private StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));
            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
            }
        } catch (UnsupportedStorageKeyException) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $aliasSetPayload = new PortalNodeAliasSetPayload($portalNodeKey, null);
        $aliasSetPayloads = new PortalNodeAliasSetPayloads([$aliasSetPayload]);

        try {
            $this->aliasSetAction->set($aliasSetPayloads);
        } catch (InvalidCreatePayloadException) {
            $io->error('Invalid values defined.');

            return 1;
        } catch (UpdateException) {
            $io->error('Database update failed.');

            return 1;
        }

        $io->success('Portal node alias reset.');

        return 0;
    }
}
