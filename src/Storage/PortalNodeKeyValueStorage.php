<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Storage\ShopwareDal\Content\PortalNode\PortalNodeStorageCollection;
use Heptacom\HeptaConnect\Storage\ShopwareDal\Content\PortalNode\PortalNodeStorageEntity;
use Ramsey\Uuid\Uuid;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class PortalNodeKeyValueStorage
{
    private EntityRepositoryInterface $portalNodeStorages;

    public function __construct(EntityRepositoryInterface $portalNodeStorages)
    {
        $this->portalNodeStorages = $portalNodeStorages;
    }

    public function set(string $portalNodeId, string $key, string $value, string $type): void
    {
        $context = Context::createDefaultContext();
        $storageId = Uuid::uuid5($portalNodeId, $key)->getHex();

        $this->portalNodeStorages->upsert([[
            'id' => $storageId,
            'portalNodeId' => $portalNodeId,
            'key' => $key,
            'value' => $value,
            'type' => $type,
        ]], $context);
    }

    public function get(string $portalNodeId, string $key): ?PortalNodeStorageEntity
    {
        $context = Context::createDefaultContext();
        $storageId = Uuid::uuid5($portalNodeId, $key)->getHex();
        $criteria = new Criteria([$storageId]);
        $criteria->setLimit(1);

        /** @var PortalNodeStorageCollection $entites */
        $entites = $this->portalNodeStorages->search($criteria, $context)->getEntities();

        return $entites->first();
    }

    public function delete(string $portalNodeId, string $key): void
    {
        $context = Context::createDefaultContext();
        $storageId = Uuid::uuid5($portalNodeId, $key)->getHex();
        $criteria = new Criteria([$storageId]);
        $criteria->setLimit(1);
        $searchResult = $this->portalNodeStorages->searchIds($criteria, $context);
        $storageId = $searchResult->firstId();

        if (\is_null($storageId)) {
            return;
        }

        $this->portalNodeStorages->delete([[
            'id' => $storageId,
        ]], $context);
    }

    public function has(string $portalNodeId, string $key): bool
    {
        $context = Context::createDefaultContext();
        $storageId = Uuid::uuid5($portalNodeId, $key)->getHex();
        $criteria = new Criteria([$storageId]);
        $criteria->setLimit(1);
        $searchResult = $this->portalNodeStorages->searchIds($criteria, $context);

        return $searchResult->getTotal() > 0;
    }
}
