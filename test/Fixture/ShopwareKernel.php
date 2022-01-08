<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test\Fixture;

use Shopware\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Shopware\Core\Framework\Plugin\PluginEntity;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ShopwareKernel extends Kernel
{
    public function __construct()
    {
        /** @var \Composer\Autoload\ClassLoader $classLoader */
        $classLoader = require __DIR__ . '/../../vendor/autoload.php';
        $plugins = [
            (new PluginEntity())->assign([
                'id' => Uuid::randomHex(),
                'name' => 'ShopwarePlugin',
                'baseClass' => ShopwareProject\Custom\ShopwarePlugin::class,
                'version' => '1.0.0',
                'active' => true,
                'path' => __DIR__ . '/ShopwareProject/Custom',
                'autoload' => [
                    'psr-4' => [
                        'Heptacom\\HeptaConnect\\Bridge\\ShopwarePlatform\\Test\\Fixture\\ShopwareProject\\Custom\\' => '/',
                    ],
                ],
                'createdAt' => new \DateTimeImmutable('2019-01-01'),
                'managedByComposer' => false,
            ])->jsonSerialize(),
        ];

        parent::__construct(
            'test',
            true,
            new StaticKernelPluginLoader($classLoader, __DIR__ . '/ShopwareProject/Custom', $plugins),
            'test',
            self::SHOPWARE_FALLBACK_VERSION,
            null,
            __DIR__ . '/ShopwareProject'
        );
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        parent::configureContainer($container, $loader);
        // Enables CSRF to fix 'The service "Shopware\Storefront\Framework\Csrf\CsrfPlaceholderHandler" has a dependency on a non-existent service "security.csrf.token_manager".'
        $container->prependExtensionConfig('framework', [
            'csrf_protection' => [
                'enabled' => true,
            ],
        ]);
    }
}
