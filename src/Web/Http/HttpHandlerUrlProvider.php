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

    public function __construct(
        PortalNodeKeyInterface $portalNodeKey,
        StorageKeyGeneratorContract $storageKeyGenerator,
        UrlGeneratorInterface $urlGenerator,
        RequestContext $requestContext,
        HttpHostProviderContract $hostProvider
    ) {
        $this->portalNodeKey = $portalNodeKey;
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->requestContext = $requestContext;
        $this->hostProvider = $hostProvider;
    }

    public function resolve(string $path): UriInterface
    {
        $this->portalNodeId ??= $this->storageKeyGenerator->serialize($this->portalNodeKey);
        $this->baseUrl ??= $this->hostProvider->get();

        $clonedRequestContext = clone $this->requestContext;

        try {
            $this->prepareRouteContext();

            return $this->uriFactory->createUri($this->urlGenerator->generate('heptaconnect.http.handler', [
                'portalNodeId' => $this->portalNodeId,
                'path' => $path,
            ], UrlGeneratorInterface::ABSOLUTE_URL));
        } finally {
            $this->resetRequestContext($clonedRequestContext);
        }
    }

    protected function prepareRouteContext(): void
    {
        if (\is_string($this->baseUrl->getScheme())) {
            $this->requestContext->setScheme($this->baseUrl->getScheme());
        }

        if (\is_string($this->baseUrl->getHost())) {
            $this->requestContext->setHost($this->baseUrl->getHost());
        }

        if (\is_int($this->baseUrl->getPort())) {
            $this->requestContext->setHttpPort($this->baseUrl->getPort());
            $this->requestContext->setHttpsPort($this->baseUrl->getPort());
        }

        if (\is_string($this->baseUrl->getPath())) {
            $this->requestContext->setBaseUrl(\ltrim($this->baseUrl->getPath(), '/'));
        }
    }

    protected function resetRequestContext(RequestContext $clonedRequestContext): void
    {
        $this->requestContext->setScheme($clonedRequestContext->getScheme());
        $this->requestContext->setHost($clonedRequestContext->getHost());
        $this->requestContext->setHttpPort($clonedRequestContext->getHttpPort());
        $this->requestContext->setHttpsPort($clonedRequestContext->getHttpsPort());
        $this->requestContext->setBaseUrl($clonedRequestContext->getBaseUrl());
    }
}
