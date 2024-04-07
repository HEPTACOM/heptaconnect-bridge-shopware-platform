<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Core\Bridge\File\PortalNodeFilesystemStreamProtocolProviderInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use League\Flysystem\FilesystemOperator;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use Shopware\Core\Framework\Adapter\Filesystem\PrefixFilesystem;

final class PortalNodeFilesystemStreamProtocolProvider implements PortalNodeFilesystemStreamProtocolProviderInterface
{
    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private FilesystemOperator $filesystem
    ) {
    }

    public function provide(PortalNodeKeyInterface $portalNodeKey): string
    {
        $key = $this->storageKeyGenerator->serialize($portalNodeKey);
        $streamScheme = \strtolower(\preg_replace('/[^a-zA-Z0-9]/', '-', 'hc-bridge-sw-' . $key));
        $portalNodeId = $this->storageKeyGenerator->serialize($portalNodeKey->withoutAlias());
        $normalizedPortalNodeId = \preg_replace('/[^a-zA-Z0-9]/', '_', $portalNodeId);
        $filesystem = new PrefixFilesystem($this->filesystem, $normalizedPortalNodeId);

        FlysystemStreamWrapper::register($streamScheme, $filesystem);

        return $streamScheme;
    }
}
