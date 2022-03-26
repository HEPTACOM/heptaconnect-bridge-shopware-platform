<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\RequestContextHelper;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHostProviderContract;
use Heptacom\HeptaConnect\Core\Bridge\File\FileContentsUrlProviderInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class FileContentsUrlProvider implements FileContentsUrlProviderInterface
{
    private UriFactoryInterface $uriFactory;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private UrlGeneratorInterface $urlGenerator;

    private RequestContext $requestContext;

    private HttpHostProviderContract $hostProvider;

    private ?UriInterface $baseUrl = null;

    private RequestContextHelper $requestContextHelper;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        UrlGeneratorInterface $urlGenerator,
        RequestContext $requestContext,
        HttpHostProviderContract $hostProvider,
        RequestContextHelper $requestContextHelper
    ) {
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->requestContext = $requestContext;
        $this->hostProvider = $hostProvider;
        $this->requestContextHelper = $requestContextHelper;
    }

    // TODO: Add token for one-time permission
    public function resolve(
        PortalNodeKeyInterface $portalNodeKey,
        string $normalizedStream,
        string $mimeType
    ): UriInterface {
        $portalNodeId = $this->storageKeyGenerator->serialize($portalNodeKey);
        $this->baseUrl ??= $this->hostProvider->get();

        $url = $this->requestContextHelper->scope(
            $this->requestContext,
            $this->baseUrl,
            function () use ($portalNodeId, $normalizedStream, $mimeType): string {
                return $this->urlGenerator->generate('api.heptaconnect.file.contents', [
                    'portalNodeId' => $portalNodeId,
                    'normalizedStream' => $normalizedStream,
                    'mimeType' => $mimeType,
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            }
        );

        return $this->uriFactory->createUri($url);
    }
}
