<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1595776348AddWebhookTable extends MigrationStep
{
    public const UP = <<<'SQL'
CREATE TABLE `heptaconnect_webhook` (
    `id` BINARY(16) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `handler` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1595776348;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
