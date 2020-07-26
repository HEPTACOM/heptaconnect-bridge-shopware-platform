<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Webhook;

use Heptacom\HeptaConnect\Core\Component\Webhook\Contract\UrlProviderInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlProvider implements UrlProviderInterface
{
    private RouterInterface $router;

    private UriFactoryInterface $urlFactory;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->urlFactory = Psr17FactoryDiscovery::findUrlFactory();
    }

    public function provide(): UriInterface
    {
        $webhookUrl = $this->router->generate('heptaconnect.api.webhook', [
            'version' => 1,
            'id' => Uuid::uuid4()->getHex(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->urlFactory->createUri($webhookUrl);
    }
}
