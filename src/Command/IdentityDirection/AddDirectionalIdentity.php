<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityDirection;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityDirection\Create\IdentityDirectionCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityDirection\Create\IdentityDirectionCreatePayloadCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityDirection\Create\IdentityDirectionCreateResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityDirection\IdentityDirectionCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddDirectionalIdentity extends Command
{
    protected static $defaultName = 'heptaconnect:identity-direction:add';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private IdentityDirectionCreateActionInterface $identityDirectionCreateAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        IdentityDirectionCreateActionInterface $identityDirectionCreateAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->identityDirectionCreateAction = $identityDirectionCreateAction;
    }

    protected function configure(): void
    {
        $this->addArgument('type', InputArgument::REQUIRED);
        $this->addArgument('source-portal-node', InputArgument::REQUIRED);
        $this->addArgument('source-external-id', InputArgument::REQUIRED);
        $this->addArgument('target-portal-node', InputArgument::REQUIRED);
        $this->addArgument('target-external-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sourcePortalNodeId = $input->getArgument('source-portal-node');
        $targetPortalNodeId = $input->getArgument('target-portal-node');

        try {
            $sourcePortalNode = $this->getPortalNodeKey($sourcePortalNodeId);
        } catch (UnsupportedStorageKeyException $_) {
            $io->error(\sprintf('Invalid portal-node "%s"', $sourcePortalNodeId));

            return 1;
        }

        try {
            $targetPortalNode = $this->getPortalNodeKey($targetPortalNodeId);
        } catch (UnsupportedStorageKeyException $_) {
            $io->error(\sprintf('Invalid portal-node "%s"', $targetPortalNodeId));

            return 1;
        }

        $sourceExternalId = (string) $input->getArgument('source-external-id');
        $targetExternalId = (string) $input->getArgument('target-external-id');
        $type = (string) $input->getArgument('type');


        if (!\is_a($type, DatasetEntityContract::class, true)) {
            $io->error('The specified type does not implement the DatasetEntityContract.');

            return 1;
        }

        $createResults = $this->identityDirectionCreateAction->create(new IdentityDirectionCreatePayloadCollection([
            new IdentityDirectionCreatePayload($sourcePortalNode, $sourceExternalId, $targetPortalNode, $targetExternalId, $type),
        ]));

        /** @var IdentityDirectionCreateResult $createResult */
        foreach ($createResults as $createResult) {
            $id = $this->storageKeyGenerator->serialize($createResult->getIdentityDirectionKey());
            $io->success(\sprintf('A new identity direction was created. ID: %s', $id));
        }

        return Command::SUCCESS;
    }

    /**
     * @throws UnsupportedStorageKeyException
     */
    private function getPortalNodeKey(string $portalNodeId): PortalNodeKeyInterface
    {
        $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeId);

        if ($portalNodeKey instanceof PortalNodeKeyInterface) {
            $portalNodeKey = $portalNodeKey->withoutAlias();
        } else {
            throw new UnsupportedStorageKeyException(\get_class($portalNodeKey));
        }

        return $portalNodeKey;
    }
}
