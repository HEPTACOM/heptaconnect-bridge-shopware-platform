<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass;

use Shopware\Core\Framework\MessageQueue\MonitoringBusDecorator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveBusMonitoring implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->removeDefinition(MonitoringBusDecorator::class);
    }
}
