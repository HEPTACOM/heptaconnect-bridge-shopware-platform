<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Shopware\Core\Framework\Uuid\Uuid;

class KeyGenerator
{
    public function generatePortalNodeKey(): PortalNodeKey
    {
        return new PortalNodeKey(Uuid::randomHex());
    }

    public function generateMappingNodeKey(): MappingNodeKey
    {
        return new MappingNodeKey(Uuid::randomHex());
    }

    public function generateWebhookKey(): WebhookKey
    {
        return new WebhookKey(Uuid::randomHex());
    }

    public function generateCronjobKey(): CronjobKey
    {
        return new CronjobKey(Uuid::randomHex());
    }
}
