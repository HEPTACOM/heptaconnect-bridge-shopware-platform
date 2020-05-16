<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1589662318CreateDatasetEntityTypeTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1589662318;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `heptaconnect_dataset_entity_type` (
    `id` BINARY(16) NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    UNIQUE `uniq.heptaconnect_dataset_entity_type.type` (`type`)
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
