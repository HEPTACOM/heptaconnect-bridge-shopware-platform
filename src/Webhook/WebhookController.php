<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Webhook;

use Heptacom\HeptaConnect\Core\Webhook\WebhookContextFactory;
use Heptacom\HeptaConnect\Portal\Base\Webhook\Contract\WebhookHandlerContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\WebhookRepositoryContract;
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
    private WebhookRepositoryContract $webhookRepository;

    private WebhookContextFactory $webhookContextFactory;

    public function __construct(
        WebhookRepositoryContract $webhookRepository,
        WebhookContextFactory $webhookContextFactory
    ) {
        $this->webhookRepository = $webhookRepository;
        $this->webhookContextFactory = $webhookContextFactory;
    }

    /**
     * @Route("/api/v{version}/heptaconnect/webhook/{id}", name="heptaconnect.api.webhook", defaults={"auth_required"=false})
     */
    public function webhook(Request $request): Response
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);

        $webhookIds = $this->webhookRepository->listByUrl($psrRequest->getUri()->getPath());

        try {
            foreach ($webhookIds as $webhookId) {
                $webhook = $this->webhookRepository->read($webhookId);
                $handlerClass = $webhook->getHandler();
                /** @var WebhookHandlerContract $handler */
                $handler = new $handlerClass();
                $psrResponse = $handler->handle($psrRequest, $this->webhookContextFactory->createContext($webhook));

                return (new HttpFoundationFactory())->createResponse($psrResponse);
            }
        } catch (\Throwable $exception) {
            // TODO: log this
        }

        // TODO: log this
        return new Response(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
    }
}
