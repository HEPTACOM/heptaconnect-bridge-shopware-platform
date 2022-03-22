<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Core\Storage\Normalizer\StreamDenormalizer;
use Heptacom\HeptaConnect\Core\Storage\RequestStorage;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpClientContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\FileReferenceRequestKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class FileReferenceController
{
    private StorageKeyGeneratorContract $storageKeyGenerator;

    private StreamDenormalizer $streamDenormalizer;

    private RequestStorage $requestStorage;

    private PortalStackServiceContainerFactory $portalContainerFactory;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        StreamDenormalizer $streamDenormalizer,
        RequestStorage $requestStorage,
        PortalStackServiceContainerFactory $portalContainerFactory
    ) {
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->streamDenormalizer = $streamDenormalizer;
        $this->requestStorage = $requestStorage;
        $this->portalContainerFactory = $portalContainerFactory;
    }

    /**
     * @Route(
     *     "/api/heptaconnect/file/{portalNodeId}/request/{requestId}",
     *     name="heptaconnect.file.request",
     *     defaults={"auth_required"=false}
     * )
     */
    public function request(string $portalNodeId, string $requestId): Response
    {
        $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeId);

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            throw new UnsupportedStorageKeyException(\get_class($portalNodeKey));
        }

        $requestKey = $this->storageKeyGenerator->deserialize($requestId);

        if (!$requestKey instanceof FileReferenceRequestKeyInterface) {
            throw new UnsupportedStorageKeyException(\get_class($requestKey));
        }

        // TODO: Read token from request
        // TODO: Use token and portalNodeKey and requestKey to check permissions

        $request = $this->requestStorage->load($portalNodeKey, $requestKey);

        $container = $this->portalContainerFactory->create($portalNodeKey);
        /** @var HttpClientContract $httpClient */
        $httpClient = $container->get(HttpClientContract::class);

        $response = $httpClient->sendRequest($request);
        $sourceStream = $response->getBody()->detach();

        return new StreamedResponse(function () use ($sourceStream): void {
            $outputStream = \fopen('php://output', 'wb');
            \stream_copy_to_stream($sourceStream, $outputStream);
        }, $response->getStatusCode(), $response->getHeaders());
    }

    /**
     * @Route(
     *     "/api/heptaconnect/file/{portalNodeId}/contents/{normalizedStream}/{mimeType}",
     *     name="heptaconnect.file.contents",
     *     requirements={"mimeType"=".+"},
     *     defaults={"auth_required"=false}
     * )
     */
    public function contents(string $portalNodeId, string $normalizedStream, string $mimeType): Response
    {
        $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeId);

        // TODO: Read token from request
        // TODO: Use token and portalNodeKey to check permissions

        $sourceStream = $this->streamDenormalizer->denormalize($normalizedStream, 'stream')->detach();

        $response = new StreamedResponse(function () use ($sourceStream): void {
            $outputStream = \fopen('php://output', 'wb');
            \stream_copy_to_stream($sourceStream, $outputStream);
        });

        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }
}
