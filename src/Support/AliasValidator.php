<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Find\PortalNodeAliasFindCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;

class AliasValidator
{
    public function __construct(private StorageKeyGeneratorContract $storageKeyGenerator, private PortalNodeAliasFindActionInterface $portalNodeAliasFindAction)
    {
    }

    public function validate(string $alias): void
    {
        if ($alias === '') {
            throw new \RuntimeException('Alias is empty');
        }

        foreach ($this->portalNodeAliasFindAction->find(new PortalNodeAliasFindCriteria([$alias])) as $_) {
            throw new \RuntimeException('Alias is already taken');
        }

        try {
            $this->storageKeyGenerator->deserialize($alias);

            throw new \RuntimeException('Alias looks like a storage key');
        } catch (UnsupportedStorageKeyException) {
        }
    }
}
