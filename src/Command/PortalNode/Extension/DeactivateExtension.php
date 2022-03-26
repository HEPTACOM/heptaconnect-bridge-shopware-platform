<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalExtension\Deactivate\PortalExtensionDeactivatePayload;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\PortalExtensionDeactivateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeactivateExtension extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:extensions:deactivate';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalExtensionDeactivateActionInterface $portalExtensionDeactivateAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalExtensionDeactivateActionInterface $portalExtensionDeactivateAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalExtensionDeactivateAction = $portalExtensionDeactivateAction;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('extension-class', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
            }

            $portalNodeKey = $portalNodeKey->withAlias();
        } catch (UnsupportedStorageKeyException $exception) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $extensionClass = (string) $input->getArgument('extension-class');

        if (!\is_a($extensionClass, PortalExtensionContract::class, true)) {
            $io->error('The extension-class is not a class of a portal extension');

            return 3;
        }

        $payload = new PortalExtensionDeactivatePayload($portalNodeKey);
        $payload->addExtension($extensionClass);

        $deactivateResult = $this->portalExtensionDeactivateAction->deactivate($payload);

        if ($deactivateResult->isSuccess()) {
            $io->success(\sprintf(
                'Extension "%s" is now deactivated for portal-node "%s"',
                $extensionClass,
                $this->storageKeyGenerator->serialize($portalNodeKey)
            ));

            return 0;
        }
        $io->error(\sprintf(
            'Could not deactivate extension "%s" for portal-node "%s"',
            $extensionClass,
            $this->storageKeyGenerator->serialize($portalNodeKey)
        ));

        return 2;
    }
}
