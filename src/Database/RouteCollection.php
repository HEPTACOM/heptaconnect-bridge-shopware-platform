<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class RouteCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return RouteEntity::class;
    }
}