<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform;

use Shopware\Core\Framework\Bundle as ShopwareBundle;

class Bundle extends ShopwareBundle
{
    public function __construct()
    {
        $this->name = 'HeptaConnectBridgeShopwarePlatform';
    }
}
