<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\RequestContextHelper;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHostProviderContract;
use Heptacom\HeptaConnect\Core\Bridge\File\FileRequestUrlProviderInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\FileReferenceRequestKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class FileRequestUrlProvider implements FileRequestUrlProviderInterface
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
        FileReferenceRequestKeyInterface $requestKey
    ): UriInterface {
        $portalNodeId ??= $this->storageKeyGenerator->serialize($portalNodeKey->withoutAlias());
        $this->baseUrl ??= $this->hostProvider->get();
        $requestId = $this->storageKeyGenerator->serialize($requestKey);

        $url = $this->requestContextHelper->scope(
            $this->requestContext,
            $this->baseUrl,
            function () use ($portalNodeId, $requestId): string {
                return $this->urlGenerator->generate('api.heptaconnect.file.request', [
                    'portalNodeId' => $portalNodeId,
                    'requestId' => $requestId,
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            }
        );

        return $this->uriFactory->createUri($url);
    }
}
