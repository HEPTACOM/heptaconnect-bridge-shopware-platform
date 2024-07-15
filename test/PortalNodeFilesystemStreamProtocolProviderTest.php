<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test;

use Doctrine\DBAL\Connection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File\PortalNodeFilesystemStreamProtocolProvider;
use Heptacom\HeptaConnect\Core\Bridge\File\PortalNodeFilesystemStreamProtocolProviderInterface;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PortalNodeFilesystemStreamProtocolProvider::class)]
class PortalNodeFilesystemStreamProtocolProviderTest extends TestCase
{
    protected Fixture\ShopwareKernel $kernel;

    #[\Override]
    protected function setUp(): void
    {
        $this->kernel = new Fixture\ShopwareKernel(Fixture\ShopwareKernel::getConnection());
        $this->kernel->boot();

        /** @var Connection $connection */
        $connection = $this->kernel->getContainer()->get(Connection::class);
        $connection->beginTransaction();
    }

    #[\Override]
    protected function tearDown(): void
    {
        /** @var Connection $connection */
        $connection = $this->kernel->getContainer()->get(Connection::class);
        $connection->rollBack();
        $this->kernel->shutdown();
    }

    public function testPathStructureLikeIn0_9(): void
    {
        $provider = $this->kernel->getContainer()->get(PortalNodeFilesystemStreamProtocolProviderInterface::class);
        $portalNodeId = '37d16ff75e45467b92105629baa76868';
        $directory = $provider->provide(new PortalNodeStorageKey($portalNodeId));
        $now = \time();

        \file_put_contents($directory . '://testData.txt', (string) $now);

        $expectedDirectory = __DIR__ . '/Fixture/ShopwareProject/files/plugins/hepta_connect_bridge_shopware_platform/';
        $expectedFilename = $expectedDirectory . 'PortalNode_' . $portalNodeId . '/testData.txt';

        static::assertFileExists($expectedFilename);
        static::assertSame((string) $now, \file_get_contents($expectedFilename));
    }
}
