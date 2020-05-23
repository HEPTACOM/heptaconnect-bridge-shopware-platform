<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1590255047AddTypeForErrorMessage extends MigrationStep
{
    public const UP = <<<'SQL'
alter table heptaconnect_error_message add type varchar(255) not null after mapping_id;
SQL;

    public function getCreationTimestamp(): int
    {
        return 1590255047;
    }

    public function update(Connection $connection): void
    {
        $connection->exec(self::UP);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
