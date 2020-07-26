<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1595802195AddPayloadToWebhooks extends MigrationStep
{
    public const UP = <<<'SQL'
ALTER TABLE `heptaconnect_webhook` ADD `payload` JSON NULL AFTER `handler`;
ALTER TABLE `heptaconnect_webhook` ADD CONSTRAINT `json.heptaconnect_webhook.payload` CHECK (JSON_VALID(`payload`));
SQL;

    public function getCreationTimestamp(): int
    {
        return 1595802195;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
