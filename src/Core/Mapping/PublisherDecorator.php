<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping;

use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappingCollection;
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

                foreach ($mappingsByType as $datasetEntityClassName => $mappings) {
                    foreach (\array_keys($mappings) as $externalId) {
                        $this->publisher->publish($datasetEntityClassName, $portalNodeId, $externalId);
                    }
                }
            }
        } finally {
            $this->cache = [];
            $this->active = false;
        }
    }

    public function publish(
        string $datasetEntityClassName,
        PortalNodeKeyInterface $portalNodeId,
        string $externalId
    ): void {
        if (!$this->active) {
            $this->publisher->publish($datasetEntityClassName, $portalNodeId, $externalId);

            return;
        }

        $portalNodeKey = $this->storageKeyGenerator->serialize($portalNodeId);
        $this->cache[$portalNodeKey][$datasetEntityClassName][$externalId] = true;
    }

    public function publishBatch(MappingCollection $mappings): void
    {
        if (!$this->active) {
            $this->publisher->publishBatch($mappings);

            return;
        }

        /** @var MappingInterface $mapping */
        foreach ($mappings as $mapping) {
            $portalNodeKey = $this->storageKeyGenerator->serialize($mapping->getPortalNodeKey());
            $this->cache[$portalNodeKey][$mapping->getDatasetEntityClassName()][$mapping->getExternalId()] = true;
        }
    }
}
