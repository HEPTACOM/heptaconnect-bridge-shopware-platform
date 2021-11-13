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
    private PortalNodeKeyInterface $portalNodeKey;

    private UriFactoryInterface $uriFactory;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private UrlGeneratorInterface $urlGenerator;

    private ?string $portalNodeId = null;

    public function __construct(
        PortalNodeKeyInterface $portalNodeKey,
        StorageKeyGeneratorContract $storageKeyGenerator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->portalNodeKey = $portalNodeKey;
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->urlGenerator = $urlGenerator;
    }

    public function resolve(string $path): UriInterface
    {
        $this->portalNodeId ??= $this->storageKeyGenerator->serialize($this->portalNodeKey);

        return $this->uriFactory->createUri($this->urlGenerator->generate('heptaconnect.http.handler', [
            'portalNodeId' => $this->portalNodeId,
            'path' => $path,
        ], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
