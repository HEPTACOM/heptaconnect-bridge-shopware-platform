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
use Sourceability\Instrumentation\Bundle\SourceabilityInstrumentationBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class AbstractIntegration extends Plugin
{
    private ?Bridge $bridge = null;

    private ?SourceabilityInstrumentationBundle $instrumentationBundle = null;

    public function getAdditionalBundles(AdditionalBundleParameters $parameters): array
    {
        return [
            $this->getBridge(),
            $this->getInstrumentationBundle(),
        ];
    }

    public function getBridge(): Bridge
    {
        if (!$this->bridge instanceof Bridge) {
            $this->bridge = new Bridge();
        }

        return $this->bridge;
    }

    public function getInstrumentationBundle(): SourceabilityInstrumentationBundle
    {
        if (!$this->instrumentationBundle instanceof SourceabilityInstrumentationBundle) {
            $this->instrumentationBundle = new SourceabilityInstrumentationBundle();
        }

        return $this->instrumentationBundle;
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

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RemoveBusMonitoring());
        $container->addCompilerPass(new RemoveEntityCache());
        $container->setParameter('shopware.admin_worker.enable_admin_worker', false);
    }

    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new AbstractIntegrationExtension($this->getName());
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

        $container->register(MigrationSource::class . '_' . $this->getName(), MigrationSource::class)
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

        $reflectionProperty = new \ReflectionProperty(InstallContext::class, 'migrationCollection');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($installContext, $collection);
    }

    protected function getLifecycleContainer(): ContainerInterface
    {
        $projectDir = (string) $this->container->getParameter('kernel.project_dir');
        $currentEnv = (string) $this->container->getParameter('kernel.runtime_environment');

        if ($this->container->hasParameter('kernel.vendor_dir')) {
            $vendorDir = (string) $this->container->getParameter('kernel.vendor_dir');
        } else {
            $vendorDir = $projectDir . '/vendor/';
        }

        $pluginLoader = new DbalKernelPluginLoader(
            require $vendorDir . '/autoload.php',
            null,
            Kernel::getConnection()
        );

        $kernel = new class($projectDir, $pluginLoader, $this, $currentEnv) extends Kernel {
            public function __construct(
                string $projectDir,
                KernelPluginLoader $pluginLoader,
                private AbstractIntegration $plugin,
                string $currentEnv
            ) {
                parent::__construct($currentEnv, false, $pluginLoader, \uniqid(), Kernel::SHOPWARE_FALLBACK_VERSION, null, $projectDir);
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

                if (!\in_array($this->plugin->getInstrumentationBundle()->getName(), $bundles, true)) {
                    yield $this->plugin->getInstrumentationBundle();
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
