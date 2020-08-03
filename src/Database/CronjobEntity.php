<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\CronjobKey;
use Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\CronjobKeyInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CronjobEntity extends Entity implements CronjobInterface
{
    use EntityIdTrait;

    protected string $cronExpression;

    /**
     * @var class-string<\Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobHandlerContract>
     */
    protected string $handler;

    protected ?array $payload = null;

    protected \DateTimeInterface $queuedUntil;

    protected ?CronjobRunCollection $copies = null;

    public function __construct()
    {
        $this->queuedUntil = date_create_from_format('U', '0');
    }

    public function getKey(): CronjobKeyInterface
    {
        return new CronjobKey($this->id);
    }

    public function getCronExpression(): string
    {
        return $this->cronExpression;
    }

    public function setCronExpression(string $cronExpression): self
    {
        $this->cronExpression = $cronExpression;

        return $this;
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

    public function getQueuedUntil(): \DateTimeInterface
    {
        return $this->queuedUntil;
    }

    public function setQueuedUntil(\DateTimeInterface $queuedUntil): self
    {
        $this->queuedUntil = $queuedUntil;

        return $this;
    }

    public function getCopies(): ?CronjobRunCollection
    {
        return $this->copies;
    }

    public function setCopies(?CronjobRunCollection $copies): self
    {
        $this->copies = $copies;

        return $this;
    }
}
