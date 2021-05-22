<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection;

use Heptacom\HeptaConnect\Portal\LocalShopwarePlatform\Portal as LocalShopwarePlatformPortal;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AbstractIntegrationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!\class_exists(LocalShopwarePlatformPortal::class)) {
            return;
        }

        $portalSourceLocation = \dirname(
            (new \ReflectionClass(LocalShopwarePlatformPortal::class))->getFileName()
        );

        $serviceDefinitionFile = $portalSourceLocation . '/../config';

        if (!\is_dir($serviceDefinitionFile) || !\is_file($serviceDefinitionFile . '/bridge-services.xml')) {
            return;
        }

        (new XmlFileLoader($container, new FileLocator($serviceDefinitionFile)))->load('bridge-services.xml');
    }
}
