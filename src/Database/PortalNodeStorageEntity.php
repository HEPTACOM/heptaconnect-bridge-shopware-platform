<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PortalNodeStorageEntity extends Entity
{
    use EntityIdTrait;

    protected string $key;

    protected string $value;

    protected string $type;

    protected string $portalNodeId = '';

    protected ?PortalNodeEntity $portalNode = null;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getPortalNode(): ?PortalNodeEntity
    {
        return $this->portalNode;
    }

    public function setPortalNode(?PortalNodeEntity $portalNode): self
    {
        $this->portalNode = $portalNode;

        return $this;
    }
}
