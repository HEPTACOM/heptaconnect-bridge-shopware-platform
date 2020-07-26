<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Webhook;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\WebhookHandlerContract;
use Heptacom\HeptaConnect\Portal\Base\Webhook\Contract\WebhookInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class WebhookController
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @Route("/api/v{version}/heptaconnect/webhook/{id}", name="heptaconnect.api.webhook", defaults={"auth_required"=false})
     */
    public function webhook(Request $request): Response
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);

        $webhook = $this->storage->getWebhook($psrRequest->getUri()->getPath());

        if (!$webhook instanceof WebhookInterface) {
            // TODO: log this
            return new Response(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $handlerClass = $webhook->getHandler();

        /** @var WebhookHandlerContract $handler */
        $handler = new $handlerClass();
        $psrResponse = $handler->handle($psrRequest, $webhook);

        $httpFoundationFactory = new HttpFoundationFactory();

        return $httpFoundationFactory->createResponse($psrResponse);
    }
}
