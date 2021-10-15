<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping;

use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingComponentStructContract;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentCollection;
use Heptacom\HeptaConnect\Portal\Base\Publication\Contract\PublisherInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class PublisherDecorator implements PublisherInterface, EventSubscriberInterface
{
    private PublisherInterface $publisher;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private array $cache = [];

    private bool $active = false;

    public function __construct(PublisherInterface $publisher, StorageKeyGeneratorContract $storageKeyGenerator)
    {
        $this->publisher = $publisher;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'startBuffer',
            KernelEvents::TERMINATE => 'flushBuffer',
        ];
    }

    public function startBuffer(): void
    {
        $this->active = true;
    }

    public function flushBuffer(): void
    {
        try {
            foreach ($this->cache as $portalNodeKey => $mappingsByType) {
                $portalNodeId = $this->storageKeyGenerator->deserialize($portalNodeKey);

                if (!$portalNodeId instanceof PortalNodeKeyInterface) {
                    continue;
                }

                foreach ($mappingsByType as $entityType => $mappings) {
                    foreach (\array_keys($mappings) as $externalId) {
                        $this->publisher->publish($entityType, $portalNodeId, (string) $externalId);
                    }
                }
            }
        } finally {
            $this->cache = [];
            $this->active = false;
        }
    }

    public function publish(
        string $entityType,
        PortalNodeKeyInterface $portalNodeId,
        string $externalId
    ): void {
        if (!$this->active) {
            $this->publisher->publish($entityType, $portalNodeId, $externalId);

            return;
        }

        $portalNodeKey = $this->storageKeyGenerator->serialize($portalNodeId);
        $this->cache[$portalNodeKey][$entityType][$externalId] = true;
    }

    public function publishBatch(MappingComponentCollection $mappings): void
    {
        if (!$this->active) {
            $this->publisher->publishBatch($mappings);

            return;
        }

        /** @var MappingComponentStructContract $mapping */
        foreach ($mappings as $mapping) {
            $portalNodeKey = $this->storageKeyGenerator->serialize($mapping->getPortalNodeKey());
            $this->cache[$portalNodeKey][$mapping->getEntityType()][$mapping->getExternalId()] = true;
        }
    }
}
