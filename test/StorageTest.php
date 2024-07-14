<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test;

use Doctrine\DBAL\Connection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bundle::class)]
#[CoversClass(PortalNodeStorageKey::class)]
class StorageTest extends TestCase
{
    protected Fixture\ShopwareKernel $kernel;

    #[\Override]
    protected function setUp(): void
    {
        $this->kernel = new Fixture\ShopwareKernel();
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

    public function testKeyComparison(): void
    {
        $authentic = new PortalNodeStorageKey('4511b131e78b49aba3f850a7af1dc845');
        $fake = $this->createMock(PortalNodeKeyInterface::class);
        static::assertFalse($authentic->equals($fake));
    }
}
