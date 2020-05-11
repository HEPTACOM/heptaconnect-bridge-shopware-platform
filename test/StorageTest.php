<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Test;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\Storage;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @covers \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\Storage
 */
class StorageTest extends TestCase
{
    public function testSetConfiguration(): void
    {
        /** @var SystemConfigService&MockObject $systemConfigService */
        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->expects($this->once())
            ->method('set')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                    $this->logicalNot($this->equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
                ),
                $this->logicalAnd(
                    $this->isType(IsType::TYPE_ARRAY),
                    $this->arrayHasKey('foo')
                )
            );

        $storage = new Storage($systemConfigService);
        $storage->setConfiguration('2281f7b9f4e847d5b0bc084288b871b1', ['foo' => 'bar']);
    }

    public function testSetConfigurationNonArrayStored(): void
    {
        /** @var SystemConfigService&MockObject $systemConfigService */
        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->expects($this->once())
            ->method('get')
            ->with($this->logicalAnd(
                $this->stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                $this->logicalNot($this->equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
            ))
            ->willReturn('party');
        $systemConfigService->expects($this->once())
            ->method('set')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                    $this->logicalNot($this->equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
                ),
                $this->logicalAnd(
                    $this->isType(IsType::TYPE_ARRAY),
                    $this->arrayHasKey('foo'),
                    $this->arrayHasKey('value')
                )
            )
        ;

        $storage = new Storage($systemConfigService);
        $storage->setConfiguration('2281f7b9f4e847d5b0bc084288b871b1', ['foo' => 'bar']);
    }

    public function testGetConfiguration(): void
    {
        /** @var SystemConfigService&MockObject $systemConfigService */
        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->expects($this->once())
            ->method('get')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                    $this->logicalNot($this->equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
                )
            )
            ->willReturn(['foo' => 'bar']);

        $storage = new Storage($systemConfigService);
        $result = $storage->getConfiguration('2281f7b9f4e847d5b0bc084288b871b1');
        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testGetConfigurationNonArray(): void
    {
        /** @var SystemConfigService&MockObject $systemConfigService */
        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->expects($this->once())
            ->method('get')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('2281f7b9f4e847d5b0bc084288b871b1'),
                    $this->logicalNot($this->equalTo('2281f7b9f4e847d5b0bc084288b871b1'))
                )
            )
            ->willReturn('foobar');

        $storage = new Storage($systemConfigService);
        $result = $storage->getConfiguration('2281f7b9f4e847d5b0bc084288b871b1');
        $this->assertEquals(['value' => 'foobar'], $result);
    }
}
