<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass;

use Shopware\Core\Framework\MessageQueue\MonitoringBusDecorator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveBusMonitoring implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (\class_exists(MonitoringBusDecorator::class)) {
            $container->removeDefinition(MonitoringBusDecorator::class);
        }
    }
}
