<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingNodeEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\RouteCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\RouteEntity;
use Heptacom\HeptaConnect\Portal\Base\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\MappingCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Heptacom\HeptaConnect\Storage\Base\Support\StorageFallback;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class Storage extends StorageFallback implements StorageInterface
{
    private SystemConfigService $systemConfigService;

    private EntityRepositoryInterface $mappingNodes;

    private EntityRepositoryInterface $datasetEntityTypes;

    private EntityRepositoryInterface $mappings;

    private EntityRepositoryInterface $routes;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $datasetEntityTypes,
        EntityRepositoryInterface $mappingNodes,
        EntityRepositoryInterface $mappings,
        EntityRepositoryInterface $routes
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->datasetEntityTypes = $datasetEntityTypes;
        $this->mappingNodes = $mappingNodes;
        $this->mappings = $mappings;
        $this->routes = $routes;
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

    public function createMappingNodes(array $datasetEntityClassNames): array
    {
        if (\count($datasetEntityClassNames) === 0) {
            return [];
        }

        $context = Context::createDefaultContext();
        $typesToCheck = \array_unique($datasetEntityClassNames);
        $typeIds = $this->getIdsForDatasetEntityType($typesToCheck, $context);

        $result = [];
        $mappingNodeInsert = [];

        foreach ($datasetEntityClassNames as $datasetEntityClassNameKey => $datasetEntityClassName) {
            $mappingId = Uuid::randomHex();
            $mappingNodeInsert[] = [
                'id' => $mappingId,
                'typeId' => $typeIds[$datasetEntityClassName],
            ];

            $type = new DatasetEntityTypeEntity();
            $type->setCreatedAt(new \DateTime());
            $type->setType($datasetEntityClassName);
            $type->setId($typeIds[$datasetEntityClassName]);
            $mapping = (new MappingNodeEntity())->setType($type);
            $mapping->setCreatedAt(new \DateTime());
            $mapping->setId($mappingId);
            $result[$datasetEntityClassNameKey] = $mapping;
        }

        $this->mappingNodes->create($mappingNodeInsert, $context);

        return $result;
    }

    public function getMapping(string $mappingNodeId, string $portalNodeId): ?MappingInterface
    {
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('mappingNodeId', $mappingNodeId),
            new EqualsFilter('portalNodeId', $portalNodeId)
        );
        $criteria->setLimit(1);

        $mapping = $this->mappings->search($criteria, $context)->first();

        if ($mapping instanceof MappingInterface) {
            return $mapping;
        }

        return null;
    }

    public function createMappings(MappingCollection $mappings): void
    {
        if ($mappings->count() === 0) {
            return;
        }

        $insert = [];

        /** @var MappingInterface $mapping */
        foreach ($mappings as $mapping) {
            $insert[] = [
                'externalId' => $mapping->getExternalId(),
                'id' => Uuid::randomHex(),
                'mappingNode' => [
                    'id' => $mapping->getMappingNodeId(),
                ],
                'portalNodeId' => $mapping->getPortalNodeId(),
            ];
        }

        $this->mappings->create($insert, Context::createDefaultContext());
    }

    public function getRouteTargets(string $sourcePortalNodeId, string $entityClassName): array
    {
        $context = Context::createDefaultContext();
        $result = [];

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('type.type', $entityClassName));
        $iterator = new RepositoryIterator($this->routes, $context, $criteria);

        while (($fetchResult = $iterator->fetch()) instanceof EntitySearchResult) {
            /** @var RouteCollection $entities */
            $entities = $fetchResult->getEntities();
            /** @var RouteEntity $entity */
            foreach ($entities as $entity) {
                $result[] = $entity->getTargetId();
            }
        }

        return $result;
    }

    public function addRouteTarget(string $sourcePortalNodeId, string $targetPortalNodeId, string $entityClassName): void
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('type.type', $entityClassName),
            new EqualsFilter('sourceId', $sourcePortalNodeId),
            new EqualsFilter('targetId', $targetPortalNodeId)
        );

        if ($this->routes->searchIds($criteria, $context)->getTotal() > 0) {
            return;
        }

        $typeId = $this->getIdsForDatasetEntityType([$entityClassName], $context)[$entityClassName];

        $this->routes->create([[
            'id' => Uuid::randomHex(),
            'typeId' => $typeId,
            'sourceId' => $sourcePortalNodeId,
            'targetId' => $targetPortalNodeId,
        ]], $context);
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

    /**
     * @psalm-param array<array-key, class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface>> $types
     * @psalm-return array<class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface>, string>
     */
    private function getIdsForDatasetEntityType(array $types, Context $context): array
    {
        $datasetEntityCriteria = new Criteria();
        $datasetEntityCriteria->addFilter(new EqualsAnyFilter('type', $types));
        /** @var DatasetEntityTypeCollection $datasetTypeEntities */
        $datasetTypeEntities = $this->datasetEntityTypes->search($datasetEntityCriteria, $context)->getEntities();
        $typeIds = $datasetTypeEntities->groupByType();
        $datasetTypeInsert = [];

        foreach ($types as $className) {
            if (!\array_key_exists($className, $typeIds)) {
                $id = Uuid::randomHex();
                $datasetTypeInsert[] = [
                    'id' => $id,
                    'type' => $className,
                ];
                $typeIds[$className] = $id;
            }
        }

        if (\count($datasetTypeInsert) > 0) {
            $this->datasetEntityTypes->create($datasetTypeInsert, $context);
        }

        return $typeIds;
    }
}
