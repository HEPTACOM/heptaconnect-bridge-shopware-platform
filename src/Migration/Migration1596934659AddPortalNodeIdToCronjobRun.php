<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1596934659AddPortalNodeIdToCronjobRun extends MigrationStep
{
    public const UP = <<<'SQL'
DELETE FROM `heptaconnect_cronjob_run`;
ALTER TABLE `heptaconnect_cronjob_run`
    ADD `portal_node_id` BINARY(16) NOT NULL,
    ADD CONSTRAINT `fk.heptaconnect_cronjob_run.portal_node_id` FOREIGN KEY (`portal_node_id`)
        REFERENCES `heptaconnect_portal_node` (`id`)
            ON DELETE RESTRICT
            ON UPDATE CASCADE;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1596934659;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
