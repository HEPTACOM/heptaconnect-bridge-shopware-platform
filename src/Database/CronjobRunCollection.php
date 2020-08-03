<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                  add(CronjobRunEntity $entity)
 * @method void                  set(string $key, CronjobRunEntity $entity)
 * @method CronjobRunEntity[]    getIterator()
 * @method CronjobRunEntity[]    getElements()
 * @method CronjobRunEntity|null get(string $key)
 * @method CronjobRunEntity|null first()
 * @method CronjobRunEntity|null last()
 */
class CronjobRunCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CronjobRunEntity::class;
    }
}
