<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http;

use Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandleServiceInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\ServerRequestInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ServerBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class HttpHandlerController
{
    private PsrHttpFactory $psrHttpFactory;

    private HttpFoundationFactory $httpFoundationFactory;

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private HttpHandleServiceInterface $httpHandleService
    ) {
        $this->psrHttpFactory = new PsrHttpFactory(
            Psr17FactoryDiscovery::findServerRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
            Psr17FactoryDiscovery::findUploadedFileFactory(),
            Psr17FactoryDiscovery::findResponseFactory()
        );

        $this->httpFoundationFactory = new HttpFoundationFactory();
    }

    /**
     * @Route(
     *     "/api/heptaconnect/flow/{portalNodeId}/http-handler/{path}",
     *     name="api.heptaconnect.http.handler",
     *     requirements={"path"=".+"},
     *     defaults={"auth_required"=false}
     * )
     */
    public function handle(Request $symfonyRequest, string $portalNodeId, string $path): Response
    {
        $response = $this->httpHandleService->handle(
            $this->getRequest($symfonyRequest, $path),
            $this->getPortalNodeKey($portalNodeId)
        );

        return $this->httpFoundationFactory->createResponse($response);
    }

    protected function getPortalNodeKey(string $portalNodeId): PortalNodeStorageKey
    {
        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeId);
        } catch (UnsupportedStorageKeyException $exception) {
            throw new NotFoundHttpException('Unable to find portal node', $exception);
        }

        if (!$portalNodeKey instanceof PortalNodeStorageKey) {
            throw new NotFoundHttpException('Unable to find portal node');
        }

        return $portalNodeKey;
    }

    protected function getRequest(Request $symfonyRequest, string $path): ServerRequestInterface
    {
        $symfonyRequest->server = new ServerBag();
        $request = $this->psrHttpFactory->createRequest($symfonyRequest);

        $request = $request->withUri(
            $request->getUri()
                ->withScheme('')
                ->withHost('')
                ->withPort(null)
                ->withPath($path)
        );

        $request = $request->withoutHeader('host');

        foreach (\array_keys($request->getAttributes()) as $attributeKey) {
            $request = $request->withoutAttribute($attributeKey);
        }

        return $request;
    }
}
