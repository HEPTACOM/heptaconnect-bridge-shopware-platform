<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PortalNodeEntity extends Entity
{
    use EntityIdTrait;

    protected string $className;

    protected ?\DateTimeInterface $deletedAt = null;

    protected ?MappingCollection $mappings = null;

    protected ?MappingNodeCollection $originalMappingNodes = null;

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): self
    {
        $this->className = $className;

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

    public function getMappings(): ?MappingCollection
    {
        return $this->mappings;
    }

    public function setMappings(?MappingCollection $mappings): self
    {
        $this->mappings = $mappings;

        return $this;
    }

    public function getOriginalMappingNodes(): ?MappingNodeCollection
    {
        return $this->originalMappingNodes;
    }

    public function setOriginalMappingNodes(?MappingNodeCollection $originalMappingNodes): self
    {
        $this->originalMappingNodes = $originalMappingNodes;

        return $this;
    }
}
