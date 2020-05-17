<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test;

use Doctrine\DBAL\Connection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\Storage;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test\Fixture\FooBarDatasetEntity;
use Heptacom\HeptaConnect\Portal\Base\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\MappingCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\MappingNodeStructInterface;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeCollection
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeEntity
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingNodeDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingNodeEntity
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\PortalNodeDefinition
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\Storage
 */
class StorageTest extends TestCase
{
    protected Fixture\ShopwareKernel $kernel;

    protected function setUp(): void
    {
        $this->kernel = new Fixture\ShopwareKernel();
        $this->kernel->boot();

        /** @var Connection $connection */
        $connection = $this->kernel->getContainer()->get(Connection::class);
        $connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        /** @var Connection $connection */
        $connection = $this->kernel->getContainer()->get(Connection::class);
        $connection->rollBack();
        $this->kernel->shutdown();
    }

    public function testSetConfiguration(): void
    {
        /** @var SystemConfigService&MockObject $systemConfigService */
        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->expects(static::once())
            ->method('set')
            ->with(
                static::logicalAnd(
                    static::stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                    static::logicalNot(static::equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
                ),
                static::logicalAnd(
                    static::isType(IsType::TYPE_ARRAY),
                    static::arrayHasKey('foo')
                )
            );

        $storage = new Storage(
            $systemConfigService,
            $this->createMock(EntityRepositoryInterface::class),
            $this->createMock(EntityRepositoryInterface::class),
            $this->createMock(EntityRepositoryInterface::class)
        );
        $storage->setConfiguration('2281f7b9f4e847d5b0bc084288b871b1', ['foo' => 'bar']);
    }

    public function testSetConfigurationNonArrayStored(): void
    {
        /** @var SystemConfigService&MockObject $systemConfigService */
        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->expects(static::once())
            ->method('get')
            ->with(static::logicalAnd(
                static::stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                static::logicalNot(static::equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
            ))
            ->willReturn('party');
        $systemConfigService->expects(static::once())
            ->method('set')
            ->with(
                static::logicalAnd(
                    static::stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                    static::logicalNot(static::equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
                ),
                static::logicalAnd(
                    static::isType(IsType::TYPE_ARRAY),
                    static::arrayHasKey('foo'),
                    static::arrayHasKey('value')
                )
            )
        ;

        $storage = new Storage(
            $systemConfigService,
            $this->createMock(EntityRepositoryInterface::class),
            $this->createMock(EntityRepositoryInterface::class),
            $this->createMock(EntityRepositoryInterface::class)
        );
        $storage->setConfiguration('2281f7b9f4e847d5b0bc084288b871b1', ['foo' => 'bar']);
    }

    public function testGetConfiguration(): void
    {
        /** @var SystemConfigService&MockObject $systemConfigService */
        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->expects(static::once())
            ->method('get')
            ->with(
                static::logicalAnd(
                    static::stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                    static::logicalNot(static::equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
                )
            )
            ->willReturn(['foo' => 'bar']);

        $storage = new Storage(
            $systemConfigService,
            $this->createMock(EntityRepositoryInterface::class),
            $this->createMock(EntityRepositoryInterface::class),
            $this->createMock(EntityRepositoryInterface::class)
        );
        $result = $storage->getConfiguration('2281f7b9f4e847d5b0bc084288b871b1');
        static::assertEquals(['foo' => 'bar'], $result);
    }

    public function testGetConfigurationNonArray(): void
    {
        /** @var SystemConfigService&MockObject $systemConfigService */
        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->expects(static::once())
            ->method('get')
            ->with(
                static::logicalAnd(
                    static::stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                    static::logicalNot(static::equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
                )
            )
            ->willReturn('foobar');

        $storage = new Storage(
            $systemConfigService,
            $this->createMock(EntityRepositoryInterface::class),
            $this->createMock(EntityRepositoryInterface::class),
            $this->createMock(EntityRepositoryInterface::class)
        );
        $result = $storage->getConfiguration('2281f7b9f4e847d5b0bc084288b871b1');
        static::assertEquals(['value' => 'foobar'], $result);
    }

    public function testCreateMappingNodeByType(): void
    {
        /** @var DefinitionInstanceRegistry $definitionRegistry */
        $definitionRegistry = $this->kernel->getContainer()->get(DefinitionInstanceRegistry::class);
        $storage = new Storage(
            $this->createMock(SystemConfigService::class),
            $definitionRegistry->getRepository('heptaconnect_dataset_entity_type'),
            $definitionRegistry->getRepository('heptaconnect_mapping_node'),
            $definitionRegistry->getRepository('heptaconnect_mapping')
        );

        /** @var MappingNodeStructInterface $node */
        [$node] = $storage->createMappingNodes([FooBarDatasetEntity::class]);
        static::assertEquals(FooBarDatasetEntity::class, $node->getDatasetEntityClassName());

        ['key' => $node] = $storage->createMappingNodes(['key' => FooBarDatasetEntity::class]);
        static::assertEquals(FooBarDatasetEntity::class, $node->getDatasetEntityClassName());
    }

    public function testCreateMapping(): void
    {
        /** @var DefinitionInstanceRegistry $definitionRegistry */
        $definitionRegistry = $this->kernel->getContainer()->get(DefinitionInstanceRegistry::class);
        $storage = new Storage(
            $this->createMock(SystemConfigService::class),
            $definitionRegistry->getRepository('heptaconnect_dataset_entity_type'),
            $definitionRegistry->getRepository('heptaconnect_mapping_node'),
            $definitionRegistry->getRepository('heptaconnect_mapping')
        );
        $portalNodeRepo = $definitionRegistry->getRepository('heptaconnect_portal_node');
        $portalNodeRepo->create([[
            'id' => '0b8ebe4959b44bae97b862e6b8b32e18',
        ]], Context::createDefaultContext());

        /**
         * @var MappingNodeStructInterface $mappingNodeNull
         * @var MappingNodeStructInterface $mappingNode
         */
        [$mappingNodeNull, $mappingNode] = $storage->createMappingNodes([
            FooBarDatasetEntity::class,
            FooBarDatasetEntity::class,
        ]);

        $mappingNull = $this->createMock(MappingInterface::class);
        $mappingNull->expects(static::atLeastOnce())
            ->method('getPortalNodeId')
            ->willReturn('0b8ebe4959b44bae97b862e6b8b32e18');
        $mappingNull->expects(static::atLeastOnce())
            ->method('getMappingNodeId')
            ->willReturn($mappingNodeNull->getId());
        $mappingNull->expects(static::atLeastOnce())
            ->method('getExternalId')
            ->willReturn(null);

        $mapping = $this->createMock(MappingInterface::class);
        $mapping->expects(static::atLeastOnce())
            ->method('getPortalNodeId')
            ->willReturn('0b8ebe4959b44bae97b862e6b8b32e18');
        $mapping->expects(static::atLeastOnce())
            ->method('getMappingNodeId')
            ->willReturn($mappingNode->getId());
        $mapping->expects(static::atLeastOnce())
            ->method('getExternalId')
            ->willReturn('This could be your external id');
        $storage->createMappings(new MappingCollection([$mappingNull, $mapping]));
    }
}
