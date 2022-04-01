<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\AliasValidator;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Set\PortalNodeAliasSetPayload;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Set\PortalNodeAliasSetPayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasSetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\InvalidCreatePayloadException;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Heptacom\HeptaConnect\Storage\Base\Exception\UpdateException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Set extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:alias:set';

    private PortalNodeAliasSetActionInterface $aliasSetAction;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private AliasValidator $aliasValidator;

    public function __construct(
        PortalNodeAliasSetActionInterface $aliasSetAction,
        StorageKeyGeneratorContract $storageKeyGenerator,
        AliasValidator $aliasValidator
    ) {
        parent::__construct();
        $this->aliasSetAction = $aliasSetAction;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->aliasValidator = $aliasValidator;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('alias', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));
            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
            }
        } catch (UnsupportedStorageKeyException $exception) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $alias = (string) $input->getArgument('alias');

        $this->aliasValidator->validate($alias);

        $aliasSetPayload = new PortalNodeAliasSetPayload($portalNodeKey, $alias);
        $aliasSetPayloads = new PortalNodeAliasSetPayloads([$aliasSetPayload]);

        try {
            $this->aliasSetAction->set($aliasSetPayloads);
        } catch (InvalidCreatePayloadException $invalidCreatePayloadException) {
            $io->error('Invalid values defined.');

            return 1;
        } catch (UpdateException $updateException) {
            $io->error('Database update failed.');

            return 1;
        }

        $io->success('Portal node alias set.');

        return 0;
    }
}
