<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass;

use Shopware\Core\Framework\DataAbstractionLayer\Cache\CachedEntityAggregator;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\CachedEntityReader;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\CachedEntitySearcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveEntityCache implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->removeDefinition(CachedEntitySearcher::class);
        $container->removeDefinition(CachedEntityAggregator::class);
        $container->removeDefinition(CachedEntityReader::class);
    }
}
