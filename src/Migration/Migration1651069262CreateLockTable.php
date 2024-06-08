<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Symfony\Component\Lock\Store\PdoStore;

class Migration1651069262CreateLockTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1651069262;
    }

    public function update(Connection $connection): void
    {
        $this->createLockTable($connection, 'heptaconnect_core_reception_lock');
        $this->createLockTable($connection, 'heptaconnect_portal_node_resource_lock');
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function createLockTable(Connection $connection, string $tableName): void
    {
        if ($connection->getSchemaManager()->tablesExist($tableName)) {
            return;
        }

        $pdo = $connection->getNativeConnection();

        $pdoStore = new PdoStore($pdo, [
            'db_table' => $tableName,
        ]);

        $pdoStore->createTable();
    }
}
