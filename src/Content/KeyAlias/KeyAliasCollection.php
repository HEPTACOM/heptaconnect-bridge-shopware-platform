<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(KeyAliasEntity $entity)
 * @method void                set(string $key, KeyAliasEntity $entity)
 * @method KeyAliasEntity[]    getIterator()
 * @method KeyAliasEntity[]    getElements()
 * @method KeyAliasEntity|null get(string $key)
 * @method KeyAliasEntity|null first()
 * @method KeyAliasEntity|null last()
 */
class KeyAliasCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return KeyAliasEntity::class;
    }
}
