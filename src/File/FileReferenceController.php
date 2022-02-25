<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Core\Storage\Normalizer\StreamDenormalizer;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
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

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        StreamDenormalizer $streamDenormalizer
    ) {
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->streamDenormalizer = $streamDenormalizer;
    }

    /**
     * // TODO: Maybe the portalContainer should be for the source portal, so the request can be controlled better
     *
     * @Route(
     *     "/api/heptaconnect/file/{portalNodeId}/request/{requestId}",
     *     name="heptaconnect.file.request",
     *     defaults={"auth_required"=false}
     * )
     */
    public function request(string $portalNodeId, string $requestId): Response
    {
        $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeId);
        $requestKey = $this->storageKeyGenerator->deserialize($requestId);

        // TODO: Read token from request
        // TODO: Use token and portalNodeKey and requestKey to check permissions

        /** @var RequestInterface $request */
        $request = null; // TODO: Use requestKey to fetch request

        // TODO: Spin up a portalContainer to get an httpClient out of it
        /** @var ClientInterface $httpClient */
        $httpClient = null;

        // TODO: Move this operation into a new service from the portal-base, so the portal can influence it by decoration
        $sourceStream = $httpClient->sendRequest($request)->getBody()->detach();

        return new StreamedResponse(function () use ($sourceStream) {
            $outputStream = \fopen('php://output', 'wb');
            \stream_copy_to_stream($sourceStream, $outputStream);
        });
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

        // TODO: Spin up a portalContainer to get a streamDenormalizer out of it
        // TODO: Move this operation into a new service from the portal-base, so the portal can influence it by decoration
        $sourceStream = $this->streamDenormalizer->denormalize($normalizedStream, 'stream')->detach();

        $response = new StreamedResponse(function () use ($sourceStream) {
            $outputStream = \fopen('php://output', 'wb');
            \stream_copy_to_stream($sourceStream, $outputStream);
        });

        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }
}
