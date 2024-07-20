<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

class PortalNodeFilesystemBaseDirectoryCreationException extends \RuntimeException
{
    public function __construct(string $directory, int $code, ?\Throwable $previous = null)
    {
        parent::__construct('Could not create portal node filesystem base directory ' . $directory, $code, $previous);
    }
}
