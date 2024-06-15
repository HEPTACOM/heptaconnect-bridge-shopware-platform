<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass;

use Doctrine\DBAL\Connection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\SystemConfig\SystemConfigService as HeptaConnectSystemConfigService;
use Shopware\Core\System\SystemConfig\SystemConfigService as ShopwareSystemConfigService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class SubstituteSystemConfigService implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(ShopwareSystemConfigService::class)) {
            return;
        }

        $definition = (new Definition(HeptaConnectSystemConfigService::class))
            ->setPublic(true)
            ->setArguments([new Reference(Connection::class)]);

        $container->setDefinition(ShopwareSystemConfigService::class, $definition);
    }
}
