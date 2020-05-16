<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class DatasetEntityTypeCollection extends EntityCollection
{
    /**
     * @psalm-return array<class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface>, string>
     */
    public function groupByType(): array
    {
        $result = [];

        /** @var DatasetEntityTypeEntity $datasetEntity */
        foreach ($this as $datasetEntity) {
            $result[$datasetEntity->getType()] = $datasetEntity->getId();
        }

        return $result;
    }

    protected function getExpectedClass(): string
    {
        return DatasetEntityTypeEntity::class;
    }
}
