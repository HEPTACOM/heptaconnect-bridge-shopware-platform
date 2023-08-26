<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Parallelization;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\InMemoryStore;
use Symfony\Component\Lock\Store\PdoStore;

final class LockStoreFactory implements LockStoreFactoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function factory(array $options = []): PersistingStoreInterface
    {
        try {
            $pdo = $this->connection->getWrappedConnection();

            return new PdoStore($pdo, $options);
        } catch (ConnectionException $exception) {
            return new InMemoryStore();
        }
    }
}
