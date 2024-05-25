<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection;

use Heptacom\HeptaConnect\Portal\LocalShopwarePlatform\Portal as LocalShopwarePlatformPortal;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AbstractIntegrationExtension extends Extension
{
    private string $alias;

    public function __construct(string $bundleName)
    {
        $basename = \preg_replace('/Bundle$/', '', $bundleName) ?? $bundleName;
        $this->alias = Container::underscore($basename);
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        if (!\class_exists(LocalShopwarePlatformPortal::class)) {
            return;
        }

        $fileName = (new \ReflectionClass(LocalShopwarePlatformPortal::class))->getFileName();

        if (!\is_string($fileName)) {
            return;
        }

        $portalSourceLocation = \dirname($fileName);
        $serviceDefinitionDir = $portalSourceLocation . '/../config';

        if (!\is_dir($serviceDefinitionDir) || !\is_file($serviceDefinitionDir . '/bridge-services.xml')) {
            return;
        }

        (new XmlFileLoader($container, new FileLocator($serviceDefinitionDir)))->load('bridge-services.xml');
    }
}
