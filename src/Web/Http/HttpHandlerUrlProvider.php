<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HttpHandlerUrlProvider implements HttpHandlerUrlProviderInterface
{
    private string $portalNodeId;

    private UriFactoryInterface $uriFactory;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        PortalNodeKeyInterface $portalNodeKey,
        StorageKeyGeneratorContract $storageKeyGenerator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->portalNodeId = $storageKeyGenerator->serialize($portalNodeKey);
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $this->urlGenerator = $urlGenerator;
    }

    public function resolve(string $path): UriInterface
    {
        return $this->uriFactory->createUri($this->urlGenerator->generate('heptaconnect.http.handler', [
            'portalNodeId' => $this->portalNodeId,
            'path' => $path,
        ], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
