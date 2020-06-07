<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\WebhookKey;
use Heptacom\HeptaConnect\Portal\Base\Contract\WebhookInterface;
use Heptacom\HeptaConnect\Portal\Base\Contract\WebhookKeyInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class WebhookEntity extends Entity implements WebhookInterface
{
    use EntityIdTrait;

    protected string $url;

    /**
     * @var class-string<\Psr\Http\Client\ClientInterface>
     */
    protected string $handler;

    public function getKey(): WebhookKeyInterface
    {
        return new WebhookKey($this->id);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return class-string<\Psr\Http\Client\ClientInterface>
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * @param class-string<\Psr\Http\Client\ClientInterface> $handler
     */
    public function setHandler($handler): self
    {
        $this->handler = $handler;

        return $this;
    }
}
