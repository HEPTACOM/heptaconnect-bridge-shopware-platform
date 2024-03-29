<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http;

use Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandleServiceInterface;
use Heptacom\HeptaConnect\Core\Web\Http\Psr7MessageMultiPartFormDataBuilder;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        private HttpHandleServiceInterface $httpHandleService,
        private Psr7MessageMultiPartFormDataBuilder $multiPartFormDataBuilder,
        private StreamFactoryInterface $streamFactory,
        private UploadedFileFactoryInterface $uploadedFileFactory,
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
        $portalNodeKey = $this->getPortalNodeKey($portalNodeId);
        $request = $this->getRequest($symfonyRequest, $path);
        $request = $this->prepareMultipartRequest($request, $symfonyRequest);
        $response = $this->httpHandleService->handle($request, $portalNodeKey);

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
        $serverBag = new ServerBag();
        // needed for PSR HTTP Factory to set the query parameters in the URL
        $serverBag->set('QUERY_STRING', $symfonyRequest->server->get('QUERY_STRING'));
        // needed for PSR HTTP Factory to set the protocol in the URL
        $serverBag->set('HTTPS', $symfonyRequest->server->get('HTTPS'));
        $symfonyRequest->server = $serverBag;
        $request = $this->psrHttpFactory->createRequest($symfonyRequest);

        foreach (\array_keys($request->getAttributes()) as $attributeKey) {
            $request = $request->withoutAttribute($attributeKey);
        }

        $request = $this->withoutConnectionAndProxyHeaders($request);
        $request = $this->withoutSymfonyHeaders($request);

        $request = $request->withAttribute(HttpHandleServiceInterface::REQUEST_ATTRIBUTE_ORIGINAL_REQUEST, $request);
        $request = $request->withUri(
            $request->getUri()
                ->withScheme('')
                ->withHost('')
                ->withPort(null)
                ->withPath($path)
        );

        $request = $request->withoutHeader('host');

        return $request;
    }

    private function withoutConnectionAndProxyHeaders(ServerRequestInterface $request): ServerRequestInterface
    {
        $headersToRemove = [
            'connection',
            'forwarded',
            'proxy-connection',
        ];

        foreach (\array_keys($request->getHeaders()) as $headerName) {
            // check every x-forwarded header to also support non "standard" headers from proxies like Traefik and AWS ELB
            if (\preg_match('/^x[-_]forwarded[-_]/i', $headerName) === 1) {
                $headersToRemove[] = $headerName;
            }
        }

        foreach ($headersToRemove as $header) {
            $request = $request->withoutHeader($header);
        }

        return $request;
    }

    private function withoutSymfonyHeaders(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withoutHeader('x-php-ob-level');
    }

    private function prepareMultipartRequest(
        ServerRequestInterface $request,
        Request $symfonyRequest
    ): ServerRequestInterface {
        if ($this->isMultipartRequest($request)) {
            $parameters = \array_merge_recursive(
                $symfonyRequest->request->all(),
                $this->getUploadedFiles($symfonyRequest)
            );

            /** @var ServerRequestInterface $request */
            $request = $this->multiPartFormDataBuilder->build($request, $parameters);
        }

        return $request;
    }

    private function isMultipartRequest(RequestInterface $request): bool
    {
        $directives = \explode(';', $request->getHeaderLine('Content-Type'));
        $directives = \array_map('trim', $directives);
        $mediaType = \array_shift($directives);

        if ($mediaType !== 'multipart/form-data') {
            return false;
        }

        foreach ($directives as $directive) {
            if (\str_starts_with($directive, 'boundary=')) {
                $boundary = \mb_substr($directive, \strlen('boundary='));

                return $boundary !== '';
            }
        }

        return false;
    }

    private function getUploadedFiles(Request $symfonyRequest): array
    {
        $uploadedFiles = $symfonyRequest->files->all();

        \array_walk_recursive($uploadedFiles, function (UploadedFile &$uploadedFile): void {
            $uploadedFile = $this->uploadedFileFactory->createUploadedFile(
                $this->streamFactory->createStream($uploadedFile->getContent()),
                $uploadedFile->getSize(),
                $uploadedFile->getError(),
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType()
            );
        });

        return $uploadedFiles;
    }
}
