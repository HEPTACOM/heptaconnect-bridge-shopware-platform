<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File;

use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Core\Storage\Contract\RequestStorageContract;
use Heptacom\HeptaConnect\Core\Storage\Normalizer\StreamDenormalizer;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\PortalNodeKeyCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Get\PortalNodeGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Get\PortalNodeGetResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeGetActionInterface;
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
    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private StreamDenormalizer $streamDenormalizer,
        private RequestStorageContract $requestStorage,
        private PortalStackServiceContainerFactory $portalContainerFactory,
        private PortalNodeGetActionInterface $portalNodeGetAction
    ) {
    }

    /**
     * @Route(
     *     "/api/heptaconnect/file/{portalNodeId}/request/{requestId}",
     *     name="api.heptaconnect.file.request",
     *     defaults={"auth_required"=false}
     * )
     */
    public function request(string $portalNodeId, string $requestId): Response
    {
        $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeId);

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            throw new UnsupportedStorageKeyException($portalNodeKey::class);
        }

        if (!$this->isPortalNodeValid($portalNodeKey)) {
            return new Response('portal node does not exist', Response::HTTP_NOT_FOUND);
        }

        $requestKey = $this->storageKeyGenerator->deserialize($requestId);

        if (!$requestKey instanceof FileReferenceRequestKeyInterface) {
            throw new UnsupportedStorageKeyException($requestKey::class);
        }

        $request = $this->requestStorage->load($portalNodeKey, $requestKey);
        $httpClient = $this->portalContainerFactory->create($portalNodeKey)->getWebHttpClient();
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
     *     name="api.heptaconnect.file.contents",
     *     requirements={"mimeType"=".+"},
     *     defaults={"auth_required"=false}
     * )
     */
    public function contents(string $portalNodeId, string $normalizedStream, string $mimeType): Response
    {
        $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeId);

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            throw new UnsupportedStorageKeyException($portalNodeKey::class);
        }

        if (!$this->isPortalNodeValid($portalNodeKey)) {
            return new Response('portal node does not exist', Response::HTTP_NOT_FOUND);
        }

        $sourceStream = $this->streamDenormalizer->denormalize($normalizedStream, 'stream')->detach();

        $response = new StreamedResponse(function () use ($sourceStream): void {
            $outputStream = \fopen('php://output', 'wb');
            \stream_copy_to_stream($sourceStream, $outputStream);
        });

        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

    private function isPortalNodeValid(PortalNodeKeyInterface $portalNodeKey): bool
    {
        $portalNodes = \iterable_to_array($this->portalNodeGetAction->get(
            new PortalNodeGetCriteria(new PortalNodeKeyCollection([$portalNodeKey]))
        ));

        $portalNode = \array_shift($portalNodes);

        if ($portalNode instanceof PortalNodeGetResult) {
            return $portalNode->getPortalNodeKey()->equals($portalNodeKey);
        }

        return false;
    }
}
