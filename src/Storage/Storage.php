<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\DatasetEntityTypeEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\MappingNodeEntity;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\RouteCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\RouteEntity;
use Heptacom\HeptaConnect\Portal\Base\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\StorageMappingNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\StoragePortalNodeKeyInterface;
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

    private EntityRepositoryInterface $errorMessages;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $datasetEntityTypes,
        EntityRepositoryInterface $mappingNodes,
        EntityRepositoryInterface $mappings,
        EntityRepositoryInterface $routes,
        EntityRepositoryInterface $errorMessages
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->datasetEntityTypes = $datasetEntityTypes;
        $this->mappingNodes = $mappingNodes;
        $this->mappings = $mappings;
        $this->routes = $routes;
        $this->errorMessages = $errorMessages;
    }

    public function getConfiguration(StoragePortalNodeKeyInterface $portalNodeKey): array
    {
        if (!$portalNodeKey instanceof PortalNodeKey) {
            return parent::getConfiguration($portalNodeKey);
        }
        /* @var PortalNodeKey $portalNodeKey */
        return $this->getConfigurationInternal($portalNodeKey->getUuid());
    }

    public function setConfiguration(StoragePortalNodeKeyInterface $portalNodeKey, array $data): void
    {
        if (!$portalNodeKey instanceof PortalNodeKey) {
            parent::setConfiguration($portalNodeKey, $data);

            return;
        }
        /** @var PortalNodeKey $portalNodeKey */
        $value = $this->getConfigurationInternal($portalNodeKey->getUuid());
        $config = \array_replace_recursive($value, $data);
        $this->systemConfigService->set($this->buildConfigurationPrefix($portalNodeKey->getUuid()), $config);
    }

    public function createMappingNodes(array $datasetEntityClassNames, StoragePortalNodeKeyInterface $portalNodeKey): array
    {
        if (\count($datasetEntityClassNames) === 0) {
            return [];
        }

        if (!$portalNodeKey instanceof PortalNodeKey) {
            return parent::createMappingNodes($datasetEntityClassNames, $portalNodeKey);
        }
        /** @var PortalNodeKey $portalNodeKey */
        $context = Context::createDefaultContext();
        $typesToCheck = \array_unique($datasetEntityClassNames);
        $typeIds = $this->getIdsForDatasetEntityType($typesToCheck, $context);

        $result = [];
        $mappingNodeInsert = [];

        foreach ($datasetEntityClassNames as $datasetEntityClassNameKey => $datasetEntityClassName) {
            $mappingId = Uuid::randomHex();
            $mappingNodeInsert[] = [
                'id' => $mappingId,
                'originPortalNodeId' => $portalNodeKey->getUuid(),
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

    public function getMapping(StorageMappingNodeKeyInterface $mappingNodeKey, StoragePortalNodeKeyInterface $portalNodeKey): ?MappingInterface
    {
        if (!$portalNodeKey instanceof PortalNodeKey) {
            return parent::getMapping($mappingNodeKey, $portalNodeKey);
        }
        /* @var PortalNodeKey $portalNodeKey */
        if (!$mappingNodeKey instanceof MappingNodeKey) {
            return parent::getMapping($mappingNodeKey, $portalNodeKey);
        }
        /** @var MappingNodeKey $mappingNodeKey */
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('mappingNodeId', $mappingNodeKey->getUuid()),
            new EqualsFilter('portalNodeId', $portalNodeKey->getUuid())
        );
        $criteria->setLimit(1);

        /** @var MappingEntity|null $mapping */
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
            $portalNodeKey = $mapping->getPortalNodeKey();

            if (!$portalNodeKey instanceof PortalNodeKey) {
                continue;
            }

            $mappingNodeKey = $mapping->getMappingNodeKey();

            if (!$mappingNodeKey instanceof MappingNodeKey) {
                continue;
            }

            $insert[] = [
                'externalId' => $mapping->getExternalId(),
                'id' => Uuid::randomHex(),
                'mappingNode' => [
                    /* TODO upsert typeId and origin */
                    'id' => $mappingNodeKey->getUuid(),
                ],
                'portalNodeId' => $portalNodeKey->getUuid(),
            ];
        }

        $this->mappings->create($insert, Context::createDefaultContext());
    }

    public function addMappingException(MappingInterface $mapping, \Throwable $throwable): void
    {
        $context = Context::createDefaultContext();

        try {
            $mappingId = $this->getMappingId($mapping, $context);
        } catch (\Throwable $exception) {
            parent::addMappingException($mapping, $throwable);
        }

        if ($mappingId === null) {
            $this->createMappings(new MappingCollection([$mapping]));

            $throwable = new \Exception('Tried to add an error message to a mapping that did not exist yet. The mapping was created.', 0, $throwable);
        }

        $mappingId = $this->getMappingId($mapping, $context);

        if ($mappingId === null) {
            parent::addMappingException($mapping, $throwable);
        }

        $insert = array_map(function (\Throwable $throwable) use ($mappingId): array {
            return [
                'id' => Uuid::randomHex(),
                'mappingId' => $mappingId,
                'type' => get_class($throwable),
                'message' => $throwable->getMessage(),
                'stackTrace' => \json_encode($throwable->getTrace()),
            ];
        }, self::unwrapException($throwable));

        $this->errorMessages->create($insert, $context);
    }

    public function removeMappingException(MappingInterface $mapping, string $type): void
    {
        $context = Context::createDefaultContext();

        $mappingId = $this->getMappingId($mapping, $context);

        $criteria = (new Criteria())->addFilter(
            new EqualsFilter('mappingId', $mappingId),
            new EqualsFilter('type', $type)
        );

        $delete = array_map(function (string $id) {
            return ['id' => $id];
        }, $this->errorMessages->searchIds($criteria, $context)->getIds());

        $this->errorMessages->delete($delete, $context);
    }

    public function getRouteTargets(StoragePortalNodeKeyInterface $sourcePortalNodeKey, string $entityClassName): array
    {
        if (!$sourcePortalNodeKey instanceof PortalNodeKey) {
            return parent::getRouteTargets($sourcePortalNodeKey, $entityClassName);
        }
        /** @var PortalNodeKey $sourcePortalNodeKey */
        $context = Context::createDefaultContext();
        $result = [];

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('type.type', $entityClassName),
            new EqualsFilter('sourceId', $sourcePortalNodeKey->getUuid())
        );
        $iterator = new RepositoryIterator($this->routes, $context, $criteria);

        while (($fetchResult = $iterator->fetch()) instanceof EntitySearchResult) {
            /** @var RouteCollection $entities */
            $entities = $fetchResult->getEntities();
            /** @var RouteEntity $entity */
            foreach ($entities as $entity) {
                $result[] = new PortalNodeKey($entity->getTargetId());
            }
        }

        return $result;
    }

    public function createRouteTarget(
        StoragePortalNodeKeyInterface $sourcePortalNodeKey,
        StoragePortalNodeKeyInterface $targetPortalNodeKey,
        string $entityClassName
    ): void {
        if (!$sourcePortalNodeKey instanceof PortalNodeKey) {
            parent::createRouteTarget($sourcePortalNodeKey, $targetPortalNodeKey, $entityClassName);

            return;
        }
        /* @var PortalNodeKey $sourcePortalNodeKey */
        if (!$targetPortalNodeKey instanceof PortalNodeKey) {
            parent::createRouteTarget($sourcePortalNodeKey, $targetPortalNodeKey, $entityClassName);

            return;
        }
        /** @var PortalNodeKey $targetPortalNodeKey */
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('type.type', $entityClassName),
            new EqualsFilter('sourceId', $sourcePortalNodeKey->getUuid()),
            new EqualsFilter('targetId', $targetPortalNodeKey->getUuid())
        );

        if ($this->routes->searchIds($criteria, $context)->getTotal() > 0) {
            return;
        }

        $typeId = $this->getIdsForDatasetEntityType([$entityClassName], $context)[$entityClassName];

        $this->routes->create([[
            'id' => Uuid::randomHex(),
            'typeId' => $typeId,
            'sourceId' => $sourcePortalNodeKey->getUuid(),
            'targetId' => $targetPortalNodeKey->getUuid(),
        ]], $context);
    }

    public function generateKey(string $keyClassName): StorageKeyInterface
    {
        if ($keyClassName === StoragePortalNodeKeyInterface::class) {
            return new PortalNodeKey(Uuid::randomHex());
        }

        if ($keyClassName === StorageMappingNodeKeyInterface::class) {
            return new MappingNodeKey(Uuid::randomHex());
        }

        return parent::generateKey($keyClassName);
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

    /**
     * @psalm-return array<array-key, \Throwable>
     */
    private static function unwrapException(\Throwable $exception): array
    {
        $exceptions = [$exception];

        while (($exception = $exception->getPrevious()) instanceof \Throwable) {
            $exceptions[] = $exception;
        }

        return $exceptions;
    }

    protected function getMappingId(MappingInterface $mapping, Context $context): ?string
    {
        $portalNodeKey = $mapping->getPortalNodeKey();
        if (!$portalNodeKey instanceof PortalNodeKey) {
            // TODO: specify exception
            throw new \Exception();
        }

        $mappingNodeKey = $mapping->getMappingNodeKey();
        if (!$mappingNodeKey instanceof MappingNodeKey) {
            // TODO: specify exception
            throw new \Exception();
        }

        $criteria = (new Criteria())->setLimit(1)->addFilter(
            new EqualsFilter('mappingNodeId', $mappingNodeKey->getUuid()),
            new EqualsFilter('portalNodeId', $portalNodeKey->getUuid())
        );

        return $this->mappings->searchIds($criteria, $context)->firstId();
    }
}
