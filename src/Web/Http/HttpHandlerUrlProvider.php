<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\RequestContextHelper;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class HttpHandlerUrlProvider implements HttpHandlerUrlProviderInterface
{
    private UriFactoryInterface $uriFactory;

    private ?string $portalNodeId = null;

    private ?UriInterface $baseUrl = null;

    public function __construct(
        private PortalNodeKeyInterface $portalNodeKey,
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private UrlGeneratorInterface $urlGenerator,
        private RequestContext $requestContext,
        private HttpHostProviderContract $hostProvider,
        private RequestContextHelper $requestContextHelper
    ) {
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
    }

    public function resolve(string $path): UriInterface
    {
        $this->portalNodeId ??= $this->storageKeyGenerator->serialize($this->portalNodeKey->withoutAlias());
        $baseUrl = $this->baseUrl ?? $this->hostProvider->get();
        $this->baseUrl = $baseUrl;

        $url = $this->requestContextHelper->scope(
            $this->requestContext,
            $this->baseUrl,
            function () use ($path): string {
                return $this->urlGenerator->generate('api.heptaconnect.http.handler', [
                    'portalNodeId' => $this->portalNodeId,
                    'path' => $path,
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            }
        );

        return $this->uriFactory->createUri($url);
    }
}
