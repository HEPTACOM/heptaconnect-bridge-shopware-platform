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
    private readonly UriFactoryInterface $uriFactory;

    private ?string $portalNodeId = null;

    private ?UriInterface $baseUrl = null;

    public function __construct(
        private readonly PortalNodeKeyInterface $portalNodeKey,
        private readonly StorageKeyGeneratorContract $storageKeyGenerator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RequestContext $requestContext,
        private readonly HttpHostProviderContract $hostProvider,
        private readonly RequestContextHelper $requestContextHelper
    ) {
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
    }

    #[\Override]
    public function resolve(string $path): UriInterface
    {
        $this->portalNodeId ??= $this->storageKeyGenerator->serialize($this->portalNodeKey->withoutAlias());
        $baseUrl = $this->baseUrl ?? $this->hostProvider->get();
        $this->baseUrl = $baseUrl;

        $url = $this->requestContextHelper->scope(
            $this->requestContext,
            $this->baseUrl,
            fn (): string => $this->urlGenerator->generate('api.heptaconnect.http.handler', [
                'portalNodeId' => $this->portalNodeId,
                'path' => $path,
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        return $this->uriFactory->createUri($url);
    }
}
