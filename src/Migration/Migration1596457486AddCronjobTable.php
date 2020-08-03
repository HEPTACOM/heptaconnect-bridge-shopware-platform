<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1596457486AddCronjobTable extends MigrationStep
{
    public const UP = <<<'SQL'
CREATE TABLE `heptaconnect_cronjob` (
    `id` BINARY(16) NOT NULL,
    `cron_expression` VARCHAR(255) NOT NULL,
    `handler` VARCHAR(255) NOT NULL,
    `payload` JSON NULL,
    `queued_until` DATETIME(3) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `json.heptaconnect_cronjob.payload` CHECK (JSON_VALID(`payload`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1596457486;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
