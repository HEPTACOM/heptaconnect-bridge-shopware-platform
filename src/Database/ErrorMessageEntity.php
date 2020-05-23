<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ErrorMessageEntity extends Entity
{
    use EntityIdTrait;

    protected string $mappingId;

    protected ?string $message;

    protected ?string $stackTrace;

    protected ?MappingEntity $mapping;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getMappingId(): string
    {
        return $this->mappingId;
    }

    public function setMappingId(string $mappingId): void
    {
        $this->mappingId = $mappingId;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getStackTrace(): ?string
    {
        return $this->stackTrace;
    }

    public function setStackTrace(?string $stackTrace): void
    {
        $this->stackTrace = $stackTrace;
    }

    public function getMapping(): ?MappingEntity
    {
        return $this->mapping;
    }

    public function setMapping(?MappingEntity $mapping): void
    {
        $this->mapping = $mapping;
    }
}
