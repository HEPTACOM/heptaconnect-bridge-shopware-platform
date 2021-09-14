<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Bundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
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
        $result = $command->run(new StringInput('database:migrate --all HeptaConnectBridgeShopwarePlatform'), new NullOutput());
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
}
