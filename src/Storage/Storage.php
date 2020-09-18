<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappingCollection;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\MappingNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\MappingNodeStructInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Heptacom\HeptaConnect\Storage\Base\Exception\InvalidMappingNodeKeyException;
use Heptacom\HeptaConnect\Storage\Base\Exception\InvalidPortalNodeKeyException;
use Heptacom\HeptaConnect\Storage\Base\MappingNodeStructCollection;
use Heptacom\HeptaConnect\Storage\Base\Support\StorageFallback;
use Heptacom\HeptaConnect\Storage\ShopwareDal\Content\DatasetEntityType\DatasetEntityTypeCollection;
use Heptacom\HeptaConnect\Storage\ShopwareDal\Content\DatasetEntityType\DatasetEntityTypeEntity;
use Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Mapping\MappingNodeEntity;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\MappingNodeStorageKey;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class Storage extends StorageFallback implements StorageInterface
{
    private EntityRepositoryInterface $mappingNodes;

    private EntityRepositoryInterface $datasetEntityTypes;

    private EntityRepositoryInterface $mappings;

    private EntityRepositoryInterface $errorMessages;

    public function __construct(
        EntityRepositoryInterface $datasetEntityTypes,
        EntityRepositoryInterface $mappingNodes,
        EntityRepositoryInterface $mappings,
        EntityRepositoryInterface $errorMessages
    ) {
        $this->datasetEntityTypes = $datasetEntityTypes;
        $this->mappingNodes = $mappingNodes;
        $this->mappings = $mappings;
        $this->errorMessages = $errorMessages;
    }

    public function getMappingNode(string $datasetEntityClassName, PortalNodeKeyInterface $portalNodeKey, string $externalId): ?MappingNodeStructInterface
    {
        $context = Context::createDefaultContext();

        if (!$portalNodeKey instanceof PortalNodeStorageKey) {
            throw new \Exception();
        }

        $criteria = (new Criteria())
            ->addFilter(
                new EqualsFilter('deletedAt', null),
                new EqualsFilter('type.type', $datasetEntityClassName),
                new EqualsFilter('mappings.deletedAt', null),
                new EqualsFilter('mappings.externalId', $externalId),
                new EqualsFilter('mappings.portalNode.deletedAt', null),
                new EqualsFilter('mappings.portalNode.id', $portalNodeKey->getUuid()),
            )
            ->setLimit(1)
        ;

        $mappingNode = $this->mappingNodes->search($criteria, $context)->first();

        if ($mappingNode instanceof MappingNodeStructInterface) {
            return $mappingNode;
        }

        return null;
    }

    public function createMappingNodes(array $datasetEntityClassNames, PortalNodeKeyInterface $portalNodeKey): MappingNodeStructCollection
    {
        if (\count($datasetEntityClassNames) === 0) {
            return new MappingNodeStructCollection();
        }

        if (!$portalNodeKey instanceof PortalNodeStorageKey) {
            return parent::createMappingNodes($datasetEntityClassNames, $portalNodeKey);
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
                'originPortalNodeId' => $portalNodeKey->getUuid(),
                'typeId' => $typeIds[$datasetEntityClassName],
            ];

            $type = new DatasetEntityTypeEntity();
            $type->setCreatedAt(new \DateTime());
            $type->setType($datasetEntityClassName);
            $type->setId($typeIds[$datasetEntityClassName]);
            $mapping = (new MappingNodeEntity())
                ->setType($type)
                ->setTypeId($type->getId())
                ->setOriginPortalNodeId($portalNodeKey->getUuid())
            ;
            $mapping->setCreatedAt(new \DateTime());
            $mapping->setId($mappingId);
            $result[$datasetEntityClassNameKey] = $mapping;
        }

        $this->mappingNodes->create($mappingNodeInsert, $context);

        return new MappingNodeStructCollection($result);
    }

    public function getMapping(MappingNodeKeyInterface $mappingNodeKey, PortalNodeKeyInterface $portalNodeKey): ?MappingInterface
    {
        if (!$portalNodeKey instanceof PortalNodeStorageKey) {
            return parent::getMapping($mappingNodeKey, $portalNodeKey);
        }

        if (!$mappingNodeKey instanceof MappingNodeStorageKey) {
            return parent::getMapping($mappingNodeKey, $portalNodeKey);
        }

        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria
            ->addFilter(
                new EqualsFilter('mappingNodeId', $mappingNodeKey->getUuid()),
                new EqualsFilter('portalNodeId', $portalNodeKey->getUuid())
            )
            ->setLimit(1)
        ;

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

            if (!$portalNodeKey instanceof PortalNodeStorageKey) {
                continue;
            }

            $mappingNodeKey = $mapping->getMappingNodeKey();

            if (!$mappingNodeKey instanceof MappingNodeStorageKey) {
                continue;
            }

            $insert[] = [
                'id' => Uuid::randomHex(),
                'externalId' => $mapping->getExternalId(),
                'mappingNode' => [
                    /* TODO upsert typeId and origin */
                    'id' => $mappingNodeKey->getUuid(),
                ],
                'portalNodeId' => $portalNodeKey->getUuid(),
            ];
        }

        $this->mappings->create($insert, Context::createDefaultContext());
    }

    public function updateMappings(MappingCollection $mappings): void
    {
        $context = Context::createDefaultContext();
        $update = [];

        /** @var MappingInterface $mapping */
        foreach ($mappings as $mapping) {
            $portalNodeKey = $mapping->getPortalNodeKey();

            if (!$portalNodeKey instanceof PortalNodeStorageKey) {
                continue;
            }

            $mappingNodeKey = $mapping->getMappingNodeKey();

            if (!$mappingNodeKey instanceof MappingNodeStorageKey) {
                continue;
            }

            $criteria = (new Criteria())->addFilter(
                new EqualsFilter('mappingNodeId', $mappingNodeKey->getUuid()),
                new EqualsFilter('portalNodeId', $portalNodeKey->getUuid())
            );

            $id = $this->mappings->searchIds($criteria, $context)->firstId();

            if ($id === null) {
                continue;
            }

            $update[] = [
                'id' => $id,
                'externalId' => $mapping->getExternalId(),
            ];
        }

        if (empty($update)) {
            return;
        }

        $this->mappings->update($update, $context);
    }

    public function deleteMappings(MappingCollection $mappings): void
    {
        $context = Context::createDefaultContext();
        $delete = [];

        /** @var MappingInterface $mapping */
        foreach ($mappings as $mapping) {
            $portalNodeKey = $mapping->getPortalNodeKey();

            if (!$portalNodeKey instanceof PortalNodeStorageKey) {
                continue;
            }

            $mappingNodeKey = $mapping->getMappingNodeKey();

            if (!$mappingNodeKey instanceof MappingNodeStorageKey) {
                continue;
            }

            $criteria = (new Criteria())->addFilter(
                new EqualsFilter('mappingNodeId', $mappingNodeKey->getUuid()),
                new EqualsFilter('portalNodeId', $portalNodeKey->getUuid())
            );

            $id = $this->mappings->searchIds($criteria, $context)->firstId();

            if ($id === null) {
                continue;
            }

            $delete[] = [
                'id' => $id,
                'externalId' => $mapping->getExternalId(),
            ];
        }

        if (empty($delete)) {
            return;
        }

        $this->mappings->delete($delete, $context);
    }

    public function addMappingException(MappingInterface $mapping, \Throwable $throwable): void
    {
        $context = Context::createDefaultContext();

        try {
            $mappingId = $this->getMappingId($mapping, $context);
        } catch (\Throwable $exception) {
            parent::addMappingException($mapping, $throwable);

            return;
        }

        if ($mappingId === null) {
            $this->createMappings(new MappingCollection([$mapping]));

            $throwable = new \Exception('Tried to add an error message to a mapping that did not exist yet. The mapping was created.', 0, $throwable);
        }

        $mappingId = $this->getMappingId($mapping, $context);

        if ($mappingId === null) {
            parent::addMappingException($mapping, $throwable);

            return;
        }

        $insert = \array_map(fn (\Throwable $throwable) => [
            'id' => Uuid::randomHex(),
            'mappingId' => $mappingId,
            'type' => \get_class($throwable),
            'message' => $throwable->getMessage(),
            'stackTrace' => \json_encode($throwable->getTrace()),
        ], self::unwrapException($throwable));

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

        $delete = \array_map(fn (string $id) => ['id' => $id], $this->errorMessages->searchIds($criteria, $context)->getIds());

        $this->errorMessages->delete($delete, $context);
    }

    protected function getMappingId(MappingInterface $mapping, Context $context): ?string
    {
        $portalNodeKey = $mapping->getPortalNodeKey();
        if (!$portalNodeKey instanceof PortalNodeStorageKey) {
            throw new InvalidPortalNodeKeyException($portalNodeKey);
        }

        $mappingNodeKey = $mapping->getMappingNodeKey();
        if (!$mappingNodeKey instanceof MappingNodeStorageKey) {
            throw new InvalidMappingNodeKeyException();
        }

        $criteria = (new Criteria())->setLimit(1)->addFilter(
            new EqualsFilter('mappingNodeId', $mappingNodeKey->getUuid()),
            new EqualsFilter('portalNodeId', $portalNodeKey->getUuid())
        );

        return $this->mappings->searchIds($criteria, $context)->firstId();
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
}
