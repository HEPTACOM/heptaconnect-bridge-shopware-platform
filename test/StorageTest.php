<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test;

use Doctrine\DBAL\Connection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\Storage;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test\Fixture\FooBarDatasetEntity;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\MappingNodeStructInterface;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;

/**
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\AbstractStorageKey
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\Storage
 * @covers \Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey
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

    public function testCreateMappingNodeByType(): void
    {
        /** @var DefinitionInstanceRegistry $definitionRegistry */
        $definitionRegistry = $this->kernel->getContainer()->get(DefinitionInstanceRegistry::class);
        $storage = new Storage(
            $definitionRegistry->getRepository('heptaconnect_dataset_entity_type'),
            $definitionRegistry->getRepository('heptaconnect_mapping_node'),
            $definitionRegistry->getRepository('heptaconnect_mapping'),
            $definitionRegistry->getRepository('heptaconnect_error_message')
        );

        $portalNodes = $definitionRegistry->getRepository('heptaconnect_portal_node');
        $portalNodeId = new PortalNodeStorageKey('bf540cbefe774a88addc8c33b58dae66');
        $portalNodes->create([['id' => $portalNodeId->getUuid()]], Context::createDefaultContext());

        /** @var MappingNodeStructInterface $node */
        $node = $storage->createMappingNodes([FooBarDatasetEntity::class], $portalNodeId)->first();
        static::assertEquals(FooBarDatasetEntity::class, $node->getDatasetEntityClassName());

        $node = $storage->createMappingNodes(['key' => FooBarDatasetEntity::class], $portalNodeId)->offsetGet('key');
        static::assertEquals(FooBarDatasetEntity::class, $node->getDatasetEntityClassName());
    }

    public function testKeyComparison(): void
    {
        $authentic = new PortalNodeStorageKey('4511b131e78b49aba3f850a7af1dc845');
        $fake = $this->createMock(PortalNodeKeyInterface::class);
        static::assertFalse($authentic->equals($fake));
    }
}
