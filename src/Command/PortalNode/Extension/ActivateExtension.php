<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension;

use Heptacom\HeptaConnect\Storage\Base\Action\PortalExtension\Activate\PortalExtensionActivatePayload;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\PortalExtensionActivateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeySerializerContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:portal-node:extensions:activate')]
class ActivateExtension extends Command
{
    use ExtensionCommandTrait;

    public function __construct(
        private StorageKeySerializerContract $storageKeySerializer,
        private PortalExtensionActivateActionInterface $extensionActivateAction
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->getAliasedPortalNodeKey($input);
        } catch (UnsupportedStorageKeyException) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $extensionClass = $this->getPortalExtensionType($input);

        $payload = new PortalExtensionActivatePayload($portalNodeKey);
        $payload->addExtension($extensionClass);

        $activateResult = $this->extensionActivateAction->activate($payload);

        if ($activateResult->isSuccess()) {
            $io->success(\sprintf(
                'Extension "%s" is now activated for portal-node "%s"',
                $extensionClass,
                $this->storageKeySerializer->serialize($portalNodeKey)
            ));

            return 0;
        }
        $io->error(\sprintf(
            'Could not activate extension "%s" for portal-node "%s"',
            $extensionClass,
            $this->storageKeySerializer->serialize($portalNodeKey)
        ));

        return 2;
    }
}
