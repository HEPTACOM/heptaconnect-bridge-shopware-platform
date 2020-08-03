<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1596472471AddCronjobRunTable extends MigrationStep
{
    public const UP = <<<'SQL'
CREATE TABLE `heptaconnect_cronjob_run` (
    `id` BINARY(16) NOT NULL,
    `cronjob_id` BINARY(16) NOT NULL,
    `copy_from_id` BINARY(16) NULL,
    `handler` VARCHAR(255) NOT NULL,
    `payload` JSON NULL,
    `throwable_class` VARCHAR(255) NULL,
    `throwable_message` MEDIUMTEXT NULL,
    `throwable_serialized` LONGTEXT NULL,
    `queued_for` DATETIME(3) NOT NULL,
    `started_at` DATETIME(3) NULL,
    `finished_at` DATETIME(3) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `json.heptaconnect_cronjob_run.payload` CHECK (JSON_VALID(`payload`)),
    FOREIGN KEY `fk.heptaconnect_cronjob_run.cronjob_id` (`cronjob_id`)
        REFERENCES `heptaconnect_cronjob` (`id`)
        ON DELETE NO ACTION ON UPDATE NO ACTION,
    FOREIGN KEY `fk.heptaconnect_cronjob_run.copy_from_id` (`copy_from_id`)
        REFERENCES `heptaconnect_cronjob_run` (`id`)
        ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1596472471;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
