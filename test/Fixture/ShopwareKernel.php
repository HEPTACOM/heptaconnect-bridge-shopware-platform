<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test\Fixture;

use Doctrine\DBAL\Connection;
use Heptacom\HeptaConnect\Core\Bridge\File\PortalNodeFilesystemStreamProtocolProviderInterface;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Shopware\Core\Framework\Plugin\PluginEntity;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ShopwareKernel extends Kernel
{
    public function __construct(Connection $connection)
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
            $connection,
            __DIR__ . '/ShopwareProject'
        );
    }

    #[\Override]
    protected function buildContainer(): ContainerBuilder
    {
        $result = parent::buildContainer();

        $result->getDefinition(PortalNodeFilesystemStreamProtocolProviderInterface::class)->setPublic(true);

        return $result;
    }
}
