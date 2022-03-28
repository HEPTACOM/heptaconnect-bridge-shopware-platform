<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1612206762CreateKeyAlias extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1612206762;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `heptaconnect_bridge_key_alias` (
    `id` BINARY(16) NOT NULL,
    `alias` VARCHAR(255) NOT NULL,
    `original` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `u.heptaconnect_bridge_key_alias.alias`(`alias`),
    UNIQUE INDEX `u.heptaconnect_bridge_key_alias.original`(`original`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
