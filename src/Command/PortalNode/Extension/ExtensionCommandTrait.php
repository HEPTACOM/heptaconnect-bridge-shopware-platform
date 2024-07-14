<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension;

use Heptacom\HeptaConnect\Portal\Base\Portal\PortalExtensionType;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

trait ExtensionCommandTrait
{
    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('extension-class', InputArgument::REQUIRED);
    }

    private function getAliasedPortalNodeKey(InputInterface $input): PortalNodeKeyInterface
    {
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            throw new UnsupportedStorageKeyException(\get_debug_type($portalNodeKey));
        }

        return $portalNodeKey->withAlias();
    }

    private function getPortalExtensionType(InputInterface $input): PortalExtensionType
    {
        return new PortalExtensionType((string) $input->getArgument('extension-class'));
    }
}
