<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Core\Bridge\File\HttpHandlerDumpPathProviderInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;

final class HttpHandlerDumpPathProvider implements HttpHandlerDumpPathProviderInterface
{
    private string $logDirectory;

    public function __construct(private StorageKeyGeneratorContract $storageKeyGenerator, string $logDirectory)
    {
        $this->logDirectory = \rtrim($logDirectory, '/\\');
    }

    public function provide(PortalNodeKeyInterface $portalNodeKey): string
    {
        $now = new \DateTimeImmutable();
        $day = $now->format('Y-m-d');
        $portalNode = $this->storageKeyGenerator->serialize($portalNodeKey->withoutAlias());
        $portalNode = \strtolower(\preg_replace('/[^a-zA-Z0-9]/', '-', $portalNode));
        $directory = \sprintf('%s/%s/%s/', $this->logDirectory, $portalNode, $day);

        if (!\is_dir($directory)) {
            \mkdir($directory, 0777, true);
        }

        return $directory . '/' . $now->format('U') . '-';
    }
}
