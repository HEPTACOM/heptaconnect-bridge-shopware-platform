<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(MappingEntity $entity)
 * @method void               set(string $key, MappingEntity $entity)
 * @method MappingEntity[]    getIterator()
 * @method MappingEntity[]    getElements()
 * @method MappingEntity|null get(string $key)
 * @method MappingEntity|null first()
 * @method MappingEntity|null last()
 */
class MappingCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MappingEntity::class;
    }
}
