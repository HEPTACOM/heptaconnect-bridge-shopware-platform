<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1595524134AddClassNameToPortalNodeEntity extends MigrationStep
{
    public const UP = <<<'SQL'
alter table heptaconnect_portal_node add class_name varchar(255) not null;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1595524134;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
