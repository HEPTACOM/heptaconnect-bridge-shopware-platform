<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform;

use Composer\Autoload\ClassLoader;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle as Bridge;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\AbstractIntegrationExtension;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass\RemoveBusMonitoring;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass\RemoveEntityCache;
use Heptacom\HeptaConnect\Storage\ShopwareDal\MigrationSource as DalStorageMigrationSource;
use Shopware\Core\Framework\Migration\MigrationCollectionLoader;
use Shopware\Core\Framework\Migration\MigrationSource;
use Shopware\Core\Framework\Parameter\AdditionalBundleParameters;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\DbalKernelPluginLoader;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\KernelPluginLoader;
use Shopware\Core\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AbstractIntegration extends Plugin
{
    private ?Bridge $bridge = null;

    public function getAdditionalBundles(AdditionalBundleParameters $parameters): array
    {
        return [
            $this->getBridge(),
        ];
    }

    public function getBridge(): Bridge
    {
        if (!$this->bridge instanceof Bridge) {
            $this->bridge = new Bridge();
        }

        return $this->bridge;
    }

    public function install(InstallContext $installContext): void
    {
        $this->replaceMigrationCollection($installContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        $this->replaceMigrationCollection($updateContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->replaceMigrationCollection($activateContext);
    }

    protected function getContainerExtensionClass()
    {
        return AbstractIntegrationExtension::class;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RemoveBusMonitoring());
        $container->addCompilerPass(new RemoveEntityCache());
        $container->setParameter('shopware.admin_worker.enable_admin_worker', false);
    }

    protected function registerMigrationPath(ContainerBuilder $container): void
    {
        $migrationPaths = [];

        foreach ((new DalStorageMigrationSource())->getSourceDirectories() as $dalStorageMigrationPath => $migrationNamespace) {
            $migrationPaths[$dalStorageMigrationPath] = $migrationNamespace;
        }

        $bridgeMigrationPath = $this->getBridge()->getMigrationPath();
        $integrationMigrationPath = $this->getMigrationPath();

        if (\is_dir($bridgeMigrationPath)) {
            $migrationPaths[$bridgeMigrationPath] = $this->getBridge()->getMigrationNamespace();
        }

        if (\is_dir($integrationMigrationPath)) {
            $migrationPaths[$integrationMigrationPath] = $this->getMigrationNamespace();
        }

        $container->register(MigrationSource::class.'_'.$this->getName(), MigrationSource::class)
            ->addArgument($this->getName())
            ->addArgument($migrationPaths)
            ->addTag('shopware.migration_source')
        ;
    }

    protected function replaceMigrationCollection(InstallContext $installContext): void
    {
        $container = $this->getLifecycleContainer();

        $migrationLoader = $container->get(MigrationCollectionLoader::class);
        $collection = $migrationLoader->collect($this->getName());
        $collection->sync();

        (\Closure::bind(function ($installContext) use ($collection): void {
            $installContext->migrationCollection = $collection;
        }, null, $installContext))($installContext);
    }

    protected function getLifecycleContainer(): ContainerInterface
    {
        $projectDir = $this->container->getParameter('kernel.project_dir');

        if ($this->container->hasParameter('kernel.vendor_dir')) {
            $vendorDir = $this->container->getParameter('kernel.vendor_dir');
        } else {
            $vendorDir = $projectDir.'/vendor/';
        }

        $pluginLoader = new DbalKernelPluginLoader(
            require $vendorDir.'/autoload.php',
            null,
            Kernel::getConnection()
        );

        $kernel = new class($projectDir, $pluginLoader, $this) extends Kernel {
            private AbstractIntegration $plugin;

            public function __construct(
                string $projectDir,
                KernelPluginLoader $pluginLoader,
                AbstractIntegration $plugin
            ) {
                parent::__construct('prod', false, $pluginLoader, \uniqid(), Kernel::SHOPWARE_FALLBACK_VERSION, null, $projectDir);
                $this->plugin = $plugin;
            }

            public function registerBundles()
            {
                $bundles = [];

                foreach (parent::registerBundles() as $bundle) {
                    $bundles[] = $bundle->getName();

                    yield $bundle;
                }

                if (!\in_array($this->plugin->getName(), $bundles, true)) {
                    yield $this->plugin;
                }

                if (!\in_array($this->plugin->getBridge()->getName(), $bundles, true)) {
                    yield $this->plugin->getBridge();
                }
            }

            protected function buildContainer()
            {
                /** @var ContainerBuilder $container */
                $container = parent::buildContainer();

                $definition = $container->getDefinition(MigrationCollectionLoader::class);
                $definition->setPublic(true);
                $container->setDefinition(MigrationCollectionLoader::class, $definition);

                return $container;
            }

            protected function getKernelParameters(): array
            {
                $kernelParameters = parent::getKernelParameters();
                $kernelParameters['kernel.vendor_dir'] = \dirname((new \ReflectionClass(ClassLoader::class))->getFileName(), 2);

                return $kernelParameters;
            }
        };

        $kernel->boot();

        return $kernel->getContainer();
    }
}
