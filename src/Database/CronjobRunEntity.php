<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CronjobRunEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var class-string<\Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobHandlerContract>
     */
    protected string $handler;

    protected ?array $payload = null;

    protected ?string $throwableClass = null;

    protected ?string $throwableMessage = null;

    protected ?string $throwableSerialized = null;

    protected \DateTimeInterface $queuedFor;

    protected ?\DateTimeInterface $startedAt = null;

    protected ?\DateTimeInterface $finishedAt = null;

    protected string $cronjobId;

    protected string $copyFromId;

    protected ?CronjobEntity $cronjob = null;

    protected ?CronjobRunEntity $copyFrom = null;

    public function __construct()
    {
        $this->queuedFor = date_create_from_format('U', '0');
    }

    /**
     * @return class-string<\Heptacom\HeptaConnect\Portal\Base\Webhook\Contract\WebhookHandlerContract>
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * @param class-string<\Heptacom\HeptaConnect\Portal\Base\Webhook\Contract\WebhookHandlerContract> $handler
     */
    public function setHandler($handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function setPayload(?array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getThrowableClass(): ?string
    {
        return $this->throwableClass;
    }

    public function setThrowableClass(?string $throwableClass): self
    {
        $this->throwableClass = $throwableClass;

        return $this;
    }

    public function getThrowableMessage(): ?string
    {
        return $this->throwableMessage;
    }

    public function setThrowableMessage(?string $throwableMessage): self
    {
        $this->throwableMessage = $throwableMessage;

        return $this;
    }

    public function getThrowableSerialized(): ?string
    {
        return $this->throwableSerialized;
    }

    public function setThrowableSerialized(?string $throwableSerialized): self
    {
        $this->throwableSerialized = $throwableSerialized;

        return $this;
    }

    public function getQueuedFor(): \DateTimeInterface
    {
        return $this->queuedFor;
    }

    public function setQueuedFor(\DateTimeInterface $queuedFor): self
    {
        $this->queuedFor = $queuedFor;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeInterface $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getCronjobId(): string
    {
        return $this->cronjobId;
    }

    public function setCronjobId(string $cronjobId): self
    {
        $this->cronjobId = $cronjobId;

        return $this;
    }

    public function getCopyFromId(): string
    {
        return $this->copyFromId;
    }

    public function setCopyFromId(string $copyFromId): self
    {
        $this->copyFromId = $copyFromId;

        return $this;
    }

    public function getCronjob(): ?CronjobEntity
    {
        return $this->cronjob;
    }

    public function setCronjob(?CronjobEntity $cronjob): self
    {
        $this->cronjob = $cronjob;

        return $this;
    }

    public function getCopyFrom(): ?CronjobRunEntity
    {
        return $this->copyFrom;
    }

    public function setCopyFrom(?CronjobRunEntity $copyFrom): self
    {
        $this->copyFrom = $copyFrom;

        return $this;
    }
}
