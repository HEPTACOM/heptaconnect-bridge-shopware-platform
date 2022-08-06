<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\FrameworkX;

use FrameworkX\App;

interface XAppFactoryInterface
{
    public function factory(): App;
}
