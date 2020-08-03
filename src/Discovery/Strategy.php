<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Discovery;

use Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobServiceInterface;
use Heptacom\HeptaConnect\Portal\Base\Webhook\Contract\WebhookServiceInterface;
use Http\Discovery\Strategy\DiscoveryStrategy;

class Strategy implements DiscoveryStrategy
{
    private static WebhookServiceInterface $webhookService;

    private static CronjobServiceInterface $cronjobService;

    public static function setWebhookService(WebhookServiceInterface $webhookService): void
    {
        self::$webhookService = $webhookService;
    }

    public static function setCronjobService(CronjobServiceInterface $cronjobService): void
    {
        self::$cronjobService = $cronjobService;
    }

    public static function getCandidates($type)
    {
        return [
            [
                'condition' => fn () => \is_a($type, WebhookServiceInterface::class, true),
                'class' => fn () => self::$webhookService,
            ],
            [
                'condition' => fn () => \is_a($type, CronjobServiceInterface::class, true),
                'class' => fn () => self::$cronjobService,
            ],
        ];
    }
}
