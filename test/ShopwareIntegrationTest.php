<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingNodeCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingNodeEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\PortalNodeCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\PortalNodeEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\RouteCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\RouteEntity;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\DefinitionNotFoundException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingNodeDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\PortalNodeDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\RouteDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration\Migration1589662318CreateDatasetEntityTypeTable
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration\Migration1589662426CreateMappingNodeTable
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration\Migration1589673188CreatePortalNodeTable
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration\Migration1589674916CreateMappingTable
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Migration\Migration1590070312CreateRouteTable
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
        $this->kernel->registerBundles();
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
            $this->fail('Failed on loading heptaconnect_dataset_entity_type: '.$e->getMessage());
        }

        try {
            $definition = $definitionRegistration->getByEntityName('heptaconnect_mapping');
            $this->assertEquals('heptaconnect_mapping', $definition->getEntityName());
            $this->assertEquals(MappingCollection::class, $definition->getCollectionClass());
            $this->assertEquals(MappingEntity::class, $definition->getEntityClass());
            $this->assertTrue($definition->getFields()->has('id'));
            $this->assertTrue($definition->getFields()->has('externalId'));
            $this->assertTrue($definition->getFields()->has('portalNode'));
            $this->assertTrue($definition->getFields()->has('portalNodeId'));
            $this->assertTrue($definition->getFields()->has('mappingNode'));
            $this->assertTrue($definition->getFields()->has('mappingNodeId'));
            $this->assertTrue($definition->getFields()->has('createdAt'));
            $this->assertTrue($definition->getFields()->has('updatedAt'));
            $this->assertTrue($definition->getFields()->has('deletedAt'));
        } catch (DefinitionNotFoundException $e) {
            $this->fail('Failed on loading heptaconnect_mappinge: '.$e->getMessage());
        }

        try {
            $definition = $definitionRegistration->getByEntityName('heptaconnect_mapping_node');
            $this->assertEquals('heptaconnect_mapping_node', $definition->getEntityName());
            $this->assertEquals(MappingNodeCollection::class, $definition->getCollectionClass());
            $this->assertEquals(MappingNodeEntity::class, $definition->getEntityClass());
            $this->assertTrue($definition->getFields()->has('id'));
            $this->assertTrue($definition->getFields()->has('type'));
            $this->assertTrue($definition->getFields()->has('typeId'));
            $this->assertTrue($definition->getFields()->has('createdAt'));
            $this->assertTrue($definition->getFields()->has('updatedAt'));
            $this->assertTrue($definition->getFields()->has('deletedAt'));
        } catch (DefinitionNotFoundException $e) {
            $this->fail('Failed on loading heptaconnect_mapping_node: '.$e->getMessage());
        }

        try {
            $definition = $definitionRegistration->getByEntityName('heptaconnect_portal_node');
            $this->assertEquals('heptaconnect_portal_node', $definition->getEntityName());
            $this->assertEquals(PortalNodeCollection::class, $definition->getCollectionClass());
            $this->assertEquals(PortalNodeEntity::class, $definition->getEntityClass());
            $this->assertTrue($definition->getFields()->has('id'));
            $this->assertTrue($definition->getFields()->has('createdAt'));
            $this->assertTrue($definition->getFields()->has('updatedAt'));
            $this->assertTrue($definition->getFields()->has('deletedAt'));
        } catch (DefinitionNotFoundException $e) {
            $this->fail('Failed on loading heptaconnect_portal_node: '.$e->getMessage());
        }

        try {
            $definition = $definitionRegistration->getByEntityName('heptaconnect_route');
            $this->assertEquals('heptaconnect_route', $definition->getEntityName());
            $this->assertEquals(RouteCollection::class, $definition->getCollectionClass());
            $this->assertEquals(RouteEntity::class, $definition->getEntityClass());
            $this->assertTrue($definition->getFields()->has('id'));
            $this->assertTrue($definition->getFields()->has('typeId'));
            $this->assertTrue($definition->getFields()->has('sourceId'));
            $this->assertTrue($definition->getFields()->has('targetId'));
            $this->assertTrue($definition->getFields()->has('createdAt'));
            $this->assertTrue($definition->getFields()->has('updatedAt'));
            $this->assertTrue($definition->getFields()->has('deletedAt'));
            $this->assertTrue($definition->getFields()->has('type'));
            $this->assertTrue($definition->getFields()->has('source'));
            $this->assertTrue($definition->getFields()->has('target'));
        } catch (DefinitionNotFoundException $e) {
            $this->fail('Failed on loading heptaconnect_route: '.$e->getMessage());
        }
    }
}
