<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(PortalNodeStorageEntity $entity)
 * @method void                         set(string $key, PortalNodeStorageEntity $entity)
 * @method PortalNodeStorageEntity[]    getIterator()
 * @method PortalNodeStorageEntity[]    getElements()
 * @method PortalNodeStorageEntity|null get(string $key)
 * @method PortalNodeStorageEntity|null first()
 * @method PortalNodeStorageEntity|null last()
 */
class PortalNodeStorageCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return PortalNodeStorageEntity::class;
    }
}
