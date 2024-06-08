<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Parallelization;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\InMemoryStore;
use Symfony\Component\Lock\Store\PdoStore;

final class LockStoreFactory implements LockStoreFactoryInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function factory(array $options = []): PersistingStoreInterface
    {
        try {
            $wrappedConnection = $this->connection->getWrappedConnection();

            if ($wrappedConnection instanceof DriverConnection) {
                $pdo = $wrappedConnection->getNativeConnection();
            } else {
                $pdo = $wrappedConnection;
            }

            return new PdoStore($pdo, $options);
        } catch (ConnectionException) {
            return new InMemoryStore();
        }
    }
}
