<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Heptacom\HeptaConnect\Storage\Base\Contract\MappingNodeStructInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MappingNodeEntity extends Entity implements MappingNodeStructInterface
{
    use EntityIdTrait;

    protected string $typeId = '';

    protected ?\DateTimeInterface $deletedAt = null;

    protected DatasetEntityTypeEntity $type;

    protected ?MappingCollection $mappings = null;

    public function getTypeId(): string
    {
        return $this->typeId;
    }

    public function setTypeId(string $typeId): self
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getType(): DatasetEntityTypeEntity
    {
        return $this->type;
    }

    public function setType(DatasetEntityTypeEntity $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMappings(): ?MappingCollection
    {
        return $this->mappings;
    }

    public function setMappings(?MappingCollection $mappings): self
    {
        $this->mappings = $mappings;

        return $this;
    }

    public function getDatasetEntityClassName(): string
    {
        return $this->getType()->getType();
    }
}
