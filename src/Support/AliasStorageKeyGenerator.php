<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Find\PortalNodeAliasFindCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;

class AliasStorageKeyGenerator extends StorageKeyGeneratorContract
{
    private StorageKeyGeneratorContract $decorated;

    private PortalNodeAliasFindActionInterface $aliasFindAction;

    public function __construct(
        StorageKeyGeneratorContract $decorated,
        PortalNodeAliasFindActionInterface $aliasFindAction
    ) {
        $this->decorated = $decorated;
        $this->aliasFindAction = $aliasFindAction;
    }

    public function generateKey(string $keyClassName): StorageKeyInterface
    {
        return $this->decorated->generateKey($keyClassName);
    }

    public function generateKeys(string $keyClassName, int $count): iterable
    {
        return $this->decorated->generateKeys($keyClassName, $count);
    }

    public function serialize(StorageKeyInterface $key): string
    {
        return $this->replaceWithAlias($this->decorated->serialize($key));
    }

    public function deserialize(string $keyData): StorageKeyInterface
    {
        return $this->decorated->deserialize($this->replaceWithOriginal($keyData));
    }

    protected function replaceWithAlias(string $original): string
    {
        $aliasFindCriteria = new PortalNodeAliasFindCriteria([$original]);
        $portalNodeKeys = \iterable_to_array($this->aliasFindAction->find($aliasFindCriteria));
        if (\count($portalNodeKeys) === 1) {
            return $portalNodeKeys[0]->getAlias() ?: $this->decorated->serialize($portalNodeKeys[0]->getKey());
        }

        return $original;
    }

    protected function replaceWithOriginal(string $alias): string
    {
        $aliasFindCriteria = new PortalNodeAliasFindCriteria([$alias]);
        $portalNodeKeys = \iterable_to_array($this->aliasFindAction->find($aliasFindCriteria));

        if (\count($portalNodeKeys) === 1) {
            return $this->decorated->serialize($portalNodeKeys[0]->getKey());
        }

        return $alias;
    }
}
