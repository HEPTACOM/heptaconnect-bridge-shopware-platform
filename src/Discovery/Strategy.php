<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Discovery;

use Heptacom\HeptaConnect\Portal\Base\Webhook\Contract\WebhookServiceInterface;
use Http\Discovery\Strategy\DiscoveryStrategy;

class Strategy implements DiscoveryStrategy
{
    private static WebhookServiceInterface $webhookService;

    public static function setWebhookService(WebhookServiceInterface $webhookService): void
    {
        self::$webhookService = $webhookService;
    }

    public static function getCandidates($type)
    {
        return [
            [
                'condition' => fn () => \is_a($type, WebhookServiceInterface::class, true),
                'class' => fn () => self::$webhookService,
            ],
        ];
    }
}
