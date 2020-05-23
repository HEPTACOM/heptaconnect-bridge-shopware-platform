<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                    add(ErrorMessageEntity $entity)
 * @method void                    set(string $key, ErrorMessageEntity $entity)
 * @method ErrorMessageEntity[]    getIterator()
 * @method ErrorMessageEntity[]    getElements()
 * @method ErrorMessageEntity|null get(string $key)
 * @method ErrorMessageEntity|null first()
 * @method ErrorMessageEntity|null last()
 */
class ErrorMessageCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ErrorMessageEntity::class;
    }
}
