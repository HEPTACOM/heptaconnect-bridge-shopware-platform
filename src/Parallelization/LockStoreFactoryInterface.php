<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Parallelization;

use Symfony\Component\Lock\PersistingStoreInterface;

interface LockStoreFactoryInterface
{
    public function factory(array $options = []): PersistingStoreInterface;
}
