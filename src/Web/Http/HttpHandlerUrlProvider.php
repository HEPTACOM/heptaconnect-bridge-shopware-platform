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
    private PortalNodeKeyInterface $portalNodeKey;

    private UriFactoryInterface $uriFactory;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private UrlGeneratorInterface $urlGenerator;

    private RequestContext $requestContext;

    private HttpHostProviderContract $hostProvider;

    private ?string $portalNodeId = null;

    private ?UriInterface $baseUrl = null;

    private RequestContextHelper $requestContextHelper;

    public function __construct(
        PortalNodeKeyInterface $portalNodeKey,
        StorageKeyGeneratorContract $storageKeyGenerator,
        UrlGeneratorInterface $urlGenerator,
        RequestContext $requestContext,
        HttpHostProviderContract $hostProvider,
        RequestContextHelper $requestContextHelper
    ) {
        $this->portalNodeKey = $portalNodeKey;
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->requestContext = $requestContext;
        $this->hostProvider = $hostProvider;
        $this->requestContextHelper = $requestContextHelper;
    }

    public function resolve(string $path): UriInterface
    {
        $this->portalNodeId ??= $this->storageKeyGenerator->serialize($this->portalNodeKey->withAlias());
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
