<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Heptacom\HeptaConnect\Storage\Base\Exception\InvalidMappingNodeKeyException;
use Heptacom\HeptaConnect\Storage\Base\Exception\InvalidPortalNodeKeyException;
use Heptacom\HeptaConnect\Storage\Base\Support\StorageFallback;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\MappingNodeStorageKey;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class Storage extends StorageFallback implements StorageInterface
{
    private EntityRepositoryInterface $mappings;

    private EntityRepositoryInterface $errorMessages;

    public function __construct(EntityRepositoryInterface $mappings, EntityRepositoryInterface $errorMessages)
    {
        $this->mappings = $mappings;
        $this->errorMessages = $errorMessages;
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
            // TODO move creation of mappings
            // $this->createMappings(new MappingCollection([$mapping]));

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
