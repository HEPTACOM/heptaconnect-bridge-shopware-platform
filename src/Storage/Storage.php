<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Heptacom\HeptaConnect\Storage\Base\Support\StorageFallback;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class Storage extends StorageFallback implements StorageInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getConfiguration(string $portalNodeId): array
    {
        return $this->getConfigurationInternal($portalNodeId);
    }

    public function setConfiguration(string $portalNodeId, array $data): void
    {
        $value = $this->getConfigurationInternal($portalNodeId);
        $config = \array_replace_recursive($value, $data);
        $this->systemConfigService->set($this->buildConfigurationPrefix($portalNodeId), $config);
    }

    private function buildConfigurationPrefix(string $portalNodeId): string
    {
        return \sprintf('heptacom.heptaConnect.portalNodeConfiguration.%s', $portalNodeId);
    }

    private function getConfigurationInternal(string $portalNodeId): array
    {
        /** @var mixed|array|null $value */
        $value = $this->systemConfigService->get($this->buildConfigurationPrefix($portalNodeId));

        if (\is_null($value)) {
            return [];
        }

        if (\is_array($value)) {
            return $value;
        }

        return ['value' => $value];
    }
}
