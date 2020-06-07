<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Webhook;

use Heptacom\HeptaConnect\Portal\Base\Contract\WebhookInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class WebhookController extends AbstractController
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

        $webhook = $this->storage->getWebhook((string) $psrRequest->getUri());

        if (!$webhook instanceof WebhookInterface) {
            return new Response(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $handlerClass = $webhook->getHandler();

        /** @var ClientInterface $handler */
        $handler = new $handlerClass();
        $psrResponse = $handler->sendRequest($psrRequest);

        $httpFoundationFactory = new HttpFoundationFactory();

        return $httpFoundationFactory->createResponse($psrResponse);
    }
}
