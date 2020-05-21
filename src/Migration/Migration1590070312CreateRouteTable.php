<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1590070312CreateRouteTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1590070312;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `heptaconnect_route` (
    `id` BINARY(16) NOT NULL,
    `type_id` BINARY(16) NOT NULL,
    `source_id` BINARY(16) NOT NULL,
    `target_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `deleted_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.heptaconnect_route.type_id` FOREIGN KEY (`type_id`)
        REFERENCES `heptaconnect_dataset_entity_type` (`id`)
            ON DELETE RESTRICT
            ON UPDATE CASCADE,
    CONSTRAINT `fk.heptaconnect_route.source_id` FOREIGN KEY (`source_id`)
        REFERENCES `heptaconnect_portal_node` (`id`)
            ON DELETE RESTRICT
            ON UPDATE CASCADE,
    CONSTRAINT `fk.heptaconnect_route.target_id` FOREIGN KEY (`target_id`)
        REFERENCES `heptaconnect_portal_node` (`id`)
            ON DELETE RESTRICT
            ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->exec($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
