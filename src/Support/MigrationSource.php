<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use Shopware\Core\Framework\Migration\MigrationSource as ShopwareMigrationSource;

class MigrationSource extends ShopwareMigrationSource
{
    public function __construct()
    {
        parent::__construct('HeptaConnectBridge', [
            __DIR__.'/../Migration' => 'Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration',
        ]);
    }
}
