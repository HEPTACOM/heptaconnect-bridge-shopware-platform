<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1596939935CreatePortalNodeKeyValueStorageTable extends MigrationStep
{
    public const UP = <<<'SQL'
CREATE TABLE IF NOT EXISTS `heptaconnect_portal_node_storage` (
    `id` BINARY(16) NOT NULL,
    `portal_node_id` BINARY(16) NOT NULL,
    `key` VARCHAR(1024) NOT NULL,
    `value` LONGTEXT NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.heptaconnect_portal_node_storage.portal_node_id` FOREIGN KEY (`portal_node_id`)
        REFERENCES `heptaconnect_portal_node` (`id`)
            ON DELETE RESTRICT
            ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1596939935;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
