<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Core\Bridge\File\PortalNodeFilesystemStreamProtocolProviderInterface;
use Heptacom\HeptaConnect\Core\Storage\Filesystem\FilesystemFactory;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Twistor\FlysystemStreamWrapper;

final class PortalNodeFilesystemStreamProtocolProvider implements PortalNodeFilesystemStreamProtocolProviderInterface
{
    private StorageKeyGeneratorContract $storageKeyGenerator;

    private FilesystemFactory $filesystemFactory;

    public function __construct(StorageKeyGeneratorContract $storageKeyGenerator, FilesystemFactory $filesystemFactory)
    {
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->filesystemFactory = $filesystemFactory;
    }

    public function provide(PortalNodeKeyInterface $portalNodeKey): string
    {
        $key = $this->storageKeyGenerator->serialize($portalNodeKey);
        $streamScheme = \strtolower(\preg_replace('/[^a-zA-Z0-9]/', '-', 'hc-bridge-sw-' . $key));

        FlysystemStreamWrapper::register($streamScheme, $this->filesystemFactory->factory($portalNodeKey));

        return $streamScheme;
    }
}
