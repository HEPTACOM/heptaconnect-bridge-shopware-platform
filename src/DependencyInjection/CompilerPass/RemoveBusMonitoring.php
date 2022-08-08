<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass;

use Shopware\Core\Framework\MessageQueue;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveBusMonitoring implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has(MessageQueue\MonitoringBusDecorator::class)) {
            $container->removeDefinition(MessageQueue\MonitoringBusDecorator::class);
        }

        if ($container->has(MessageQueue\Monitoring\MonitoringBusDecorator::class)) {
            $container->removeDefinition(MessageQueue\Monitoring\MonitoringBusDecorator::class);
        }
    }
}
