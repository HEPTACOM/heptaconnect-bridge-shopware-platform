<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1590250578CreateErrorMessageTable extends MigrationStep
{
    public const UP = <<<'SQL'
CREATE TABLE `heptaconnect_error_message` (
    `id` BINARY(16) NOT NULL,
    `mapping_id` BINARY(16) NOT NULL,
    `message` LONGTEXT NULL,
    `stack_trace` LONGTEXT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    KEY `fk.heptaconnect_error_message.mapping_id` (`mapping_id`),
    CONSTRAINT `fk.heptaconnect_error_message.mapping_id` FOREIGN KEY (`mapping_id`) REFERENCES `heptaconnect_mapping` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1590250578;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
