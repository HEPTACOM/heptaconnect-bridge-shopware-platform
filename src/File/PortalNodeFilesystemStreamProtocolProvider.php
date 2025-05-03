<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Core\Bridge\File\PortalNodeFilesystemStreamProtocolProviderInterface;
use Heptacom\HeptaConnect\Core\File\Filesystem\RewritePathStreamWrapper;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeySerializerContract;

final readonly class PortalNodeFilesystemStreamProtocolProvider implements PortalNodeFilesystemStreamProtocolProviderInterface
{
    public function __construct(
        private StorageKeySerializerContract $storageKeySerializer,
        private string $filesystemBasePath,
    ) {
    }

    #[\Override]
    public function provide(PortalNodeKeyInterface $portalNodeKey): string
    {
        $key = $this->storageKeySerializer->serialize($portalNodeKey);
        $streamScheme = \strtolower((string) \preg_replace('/[^a-zA-Z0-9]/', '-', 'hc-bridge-sw-' . $key));
        $portalNodeId = $this->storageKeySerializer->serialize($portalNodeKey->withoutAlias());
        $normalizedId = \preg_replace('/[^a-zA-Z0-9]/', '_', $portalNodeId);
        $portalNodePath = \rtrim($this->filesystemBasePath, '/\\') . \DIRECTORY_SEPARATOR . $normalizedId;

        if (!\is_dir($portalNodePath) && \mkdir($portalNodePath, 0777, true) === false) {
            throw new PortalNodeFilesystemBaseDirectoryCreationException($portalNodePath, 1721493200);
        }

        \stream_wrapper_register($streamScheme, RewritePathStreamWrapper::class);
        \stream_context_set_default([
            $streamScheme => [
                'protocol' => [
                    'set' => 'file',
                ],
                'path' => [
                    'prepend' => $portalNodePath,
                    'prepend_safe_separator' => true,
                ],
            ],
        ]);

        return $streamScheme;
    }
}
