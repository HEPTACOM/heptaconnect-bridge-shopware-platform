<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Webhook;

use Heptacom\HeptaConnect\Core\Component\Webhook\Contract\UrlProviderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlProvider implements UrlProviderInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function provide(): string
    {
        return $this->router->generate('heptaconnect.api.webhook', [
            'version' => 1,
            'id' => Uuid::uuid4()->getHex(),
        ], UrlGeneratorInterface::ABSOLUTE_PATH);
    }
}
