<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeEntity;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\DefinitionNotFoundException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration\Migration1589662318CreateDatasetEntityTypeTable
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle
 */
class ShopwareIntegrationTest extends TestCase
{
    protected Fixture\ShopwareKernel $kernel;

    protected function setUp(): void
    {
        $this->kernel = new Fixture\ShopwareKernel();
        $this->kernel->boot();
    }

    protected function tearDown(): void
    {
        $this->kernel->shutdown();
    }

    public function testConnection(): void
    {
        $connection = $this->kernel::getConnection();
        static::assertTrue($connection->ping());
    }

    /**
     * @depends testConnection
     */
    public function testMigration(): void
    {
        $application = new Application($this->kernel);
        $command = $application->find('database:migrate');
        $result = $command->run(new StringInput('--all'), new NullOutput());
        static::assertEquals(0, $result);
        $result = $command->run(new StringInput('database:migrate --all Heptacom\\\\HeptaConnect'), new NullOutput());
        static::assertEquals(0, $result);
    }

    /**
     * @depends testMigration
     */
    public function testShopwareKernelLoading(): void
    {
        $this->kernel->registerBundles();
        $bundle = $this->kernel->getBundle('HeptaConnectBridgeShopwarePlatform');

        static::assertInstanceOf(Bundle::class, $bundle);
    }

    /**
     * @depends testShopwareKernelLoading
     */
    public function testShopwareLoadingEntityRepositories(): void
    {
        /** @var DefinitionInstanceRegistry $definitionRegistration */
        $definitionRegistration = $this->kernel->getContainer()->get(DefinitionInstanceRegistry::class);

        try {
            $definition = $definitionRegistration->getByEntityName('heptaconnect_dataset_entity_type');
            $this->assertEquals('heptaconnect_dataset_entity_type', $definition->getEntityName());
            $this->assertEquals(DatasetEntityTypeCollection::class, $definition->getCollectionClass());
            $this->assertEquals(DatasetEntityTypeEntity::class, $definition->getEntityClass());
            $this->assertTrue($definition->getFields()->has('id'));
            $this->assertTrue($definition->getFields()->has('type'));
            $this->assertTrue($definition->getFields()->has('createdAt'));
            $this->assertTrue($definition->getFields()->has('updatedAt'));
        } catch (DefinitionNotFoundException $e) {
            $this->fail('Failed on loading heptaconnect_dataset_entity_type: ' . $e->getMessage());
        }
    }
}
