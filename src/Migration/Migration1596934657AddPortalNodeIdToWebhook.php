<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1596934657AddPortalNodeIdToWebhook extends MigrationStep
{
    public const UP = <<<'SQL'
DELETE FROM `heptaconnect_webhook`;
ALTER TABLE `heptaconnect_webhook`
    ADD `portal_node_id` BINARY(16) NOT NULL,
    ADD CONSTRAINT `fk.heptaconnect_webhook.portal_node_id` FOREIGN KEY (`portal_node_id`)
        REFERENCES `heptaconnect_portal_node` (`id`)
            ON DELETE RESTRICT
            ON UPDATE CASCADE;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1596934657;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
