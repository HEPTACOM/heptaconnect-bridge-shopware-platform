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

final class FileContentsUrlProvider implements FileContentsUrlProviderInterface
{
    private UriFactoryInterface $uriFactory;

    private ?UriInterface $baseUrl = null;

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private UrlGeneratorInterface $urlGenerator,
        private RequestContext $requestContext,
        private HttpHostProviderContract $hostProvider,
        private RequestContextHelper $requestContextHelper
    ) {
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
    }

    public function resolve(
        PortalNodeKeyInterface $portalNodeKey,
        string $normalizedStream,
        string $mimeType
    ): UriInterface {
        $portalNodeId = $this->storageKeyGenerator->serialize($portalNodeKey->withoutAlias());
        $this->baseUrl ??= $this->hostProvider->get();

        $url = $this->requestContextHelper->scope(
            $this->requestContext,
            $this->baseUrl,
            fn (): string => $this->urlGenerator->generate('api.heptaconnect.file.contents', [
                'portalNodeId' => $portalNodeId,
                'normalizedStream' => $normalizedStream,
                'mimeType' => $mimeType,
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        return $this->uriFactory->createUri($url);
    }
}
