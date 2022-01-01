<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\Activate\PortalExtensionActivateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\Activate\PortalExtensionActivatePayload;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ActivateExtension extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:extensions:activate';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalExtensionActivateActionInterface $portalExtensionActivateAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalExtensionActivateActionInterface $portalExtensionActivateAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalExtensionActivateAction = $portalExtensionActivateAction;
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
        } catch (UnsupportedStorageKeyException $exception) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $extensionClass = (string) $input->getArgument('extension-class');

        $payload = new PortalExtensionActivatePayload($portalNodeKey);
        $payload->addExtension($extensionClass);

        $activateResult = $this->portalExtensionActivateAction->activate($payload);

        if ($activateResult->isSuccess()) {
            $io->success(\sprintf(
                'Extension "%s" is now activated for portal-node "%s"',
                $extensionClass,
                $this->storageKeyGenerator->serialize($portalNodeKey)
            ));
        } else {
            $io->error(\sprintf(
                'Could not activate extension "%s" for portal-node "%s"',
                $extensionClass,
                $this->storageKeyGenerator->serialize($portalNodeKey)
            ));
        }

        return 0;
    }
}
