<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test\Fixture\ShopwareProject\Custom;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle;
use Shopware\Core\Framework\Parameter\AdditionalBundleParameters;
use Shopware\Core\Framework\Plugin;

class ShopwarePlugin extends Plugin
{
    public function getAdditionalBundles(AdditionalBundleParameters $parameters): array
    {
        return [
            new Bundle(),
        ];
    }
}
