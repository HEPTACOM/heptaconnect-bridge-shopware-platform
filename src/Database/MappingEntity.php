<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Heptacom\HeptaConnect\Portal\Base\Contract\MappingInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

/**
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
class MappingEntity extends Entity implements MappingInterface
{
    use EntityIdTrait;

    protected string $mappingNodeId = '';

    protected string $portalNodeId = '';

    protected ?string $externalId = null;

    protected ?\DateTimeInterface $deletedAt = null;

    protected MappingNodeEntity $mappingNode;

    protected ?PortalNodeEntity $portalNode = null;

    public function getMappingNodeId(): string
    {
        return $this->mappingNodeId;
    }

    public function setMappingNodeId(string $mappingNodeId): self
    {
        $this->mappingNodeId = $mappingNodeId;

        return $this;
    }

    public function getPortalNodeId(): string
    {
        return $this->portalNodeId;
    }

    public function setPortalNodeId(string $portalNodeId): self
    {
        $this->portalNodeId = $portalNodeId;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

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

    public function getMappingNode(): MappingNodeEntity
    {
        return $this->mappingNode;
    }

    public function setMappingNode(MappingNodeEntity $mappingNode): self
    {
        $this->mappingNode = $mappingNode;

        return $this;
    }

    public function getPortalNode(): ?PortalNodeEntity
    {
        return $this->portalNode;
    }

    public function setPortalNode(?PortalNodeEntity $portalNode): self
    {
        $this->portalNode = $portalNode;

        return $this;
    }

    public function getDatasetEntityClassName(): string
    {
        return $this->getMappingNode()->getDatasetEntityClassName();
    }
}
