<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Discovery\Strategy;
use Heptacom\HeptaConnect\Portal\Base\Support\ServiceDiscovery;
use Shopware\Core\Framework\Bundle as ShopwareBundle;

class Bundle extends ShopwareBundle
{
    public function __construct()
    {
        $this->name = 'HeptaConnectBridgeShopwarePlatform';
    }

    public function boot(): void
    {
        parent::boot();

        $this->container->get(Strategy::class);
        ServiceDiscovery::appendStrategy(Strategy::class);
    }
}
