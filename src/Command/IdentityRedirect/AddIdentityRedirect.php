<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityRedirect;

use Heptacom\HeptaConnect\Dataset\Base\EntityType;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityRedirect\Create\IdentityRedirectCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityRedirect\Create\IdentityRedirectCreatePayloadCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\IdentityRedirect\Create\IdentityRedirectCreateResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityRedirect\IdentityRedirectCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AddIdentityRedirect extends Command
{
    protected static $defaultName = 'heptaconnect:identity-redirect:add';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private IdentityRedirectCreateActionInterface $identityRedirectCreateAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        IdentityRedirectCreateActionInterface $identityRedirectCreateAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->identityRedirectCreateAction = $identityRedirectCreateAction;
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

        try {
            $type = new EntityType((string) $input->getArgument('type'));
        } catch (\Throwable $exception) {
            $io->error('Given type is invalid: ' . $exception->getMessage());

            return 1;
        }

        $createResults = $this->identityRedirectCreateAction->create(new IdentityRedirectCreatePayloadCollection([
            new IdentityRedirectCreatePayload($sourcePortalNode, $sourceExternalId, $targetPortalNode, $targetExternalId, $type),
        ]));

        /** @var IdentityRedirectCreateResult $createResult */
        foreach ($createResults as $createResult) {
            $id = $this->storageKeyGenerator->serialize($createResult->getIdentityRedirectKey());
            $io->success(\sprintf('A new identity redirect was created. Key: %s', $id));
        }

        return 0;
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
