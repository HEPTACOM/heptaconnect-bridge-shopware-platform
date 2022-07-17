<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use FrameworkX\App;
use FrameworkX\Container;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Shopware\Core\HttpKernel;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class XAppFactory
{
    private ContainerInterface $container;

    private RouterInterface $router;

    private PsrHttpFactory $psrHttpFactory;

    private HttpFoundationFactory $httpFoundationFactory;

    public function __construct(
        ContainerInterface $container,
        RouterInterface $router
    ) {
        $this->container = $container;
        $this->router = $router;
        $this->psrHttpFactory = new PsrHttpFactory(
            Psr17FactoryDiscovery::findServerRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
            Psr17FactoryDiscovery::findUploadedFileFactory(),
            Psr17FactoryDiscovery::findResponseFactory()
        );
        $this->httpFoundationFactory = new HttpFoundationFactory();
    }

    public function factory(HttpKernel $httpKernel): App
    {
        if (!\class_exists(App::class)) {
            throw new \LogicException('Framework X must be installed to use this factory. Try running "composer require clue/framework-x:dev-main".');
        }

        $app = new App(new Container($this->container));
        $handler = $this->getHandler($httpKernel);

        /** @var Route $route */
        foreach ($this->router->getRouteCollection() as $name => $route) {
            if (\strpos((string) $name, 'api.heptaconnect.') !== 0) {
                continue;
            }

            $path = $route->getPath();

            foreach ($route->getRequirements() as $variable => $format) {
                $symfonyPlaceholder = \sprintf('{%s}', $variable);
                $xPlaceholder = \sprintf('{%s:%s}', $variable, $format);
                $path = \str_replace($symfonyPlaceholder, $xPlaceholder, $path);
            }

            $methods = $route->getMethods();

            if ($methods === []) {
                $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
            }

            $app->map($methods, $path, $handler);
        }

        return $app;
    }

    public function getHandler(HttpKernel $httpKernel): \Closure
    {
        return function (ServerRequestInterface $psrRequest) use ($httpKernel): ResponseInterface {
            $symfonyRequest = $this->httpFoundationFactory->createRequest($psrRequest);

            $symfonyResponse = $httpKernel->handle($symfonyRequest, HttpKernelInterface::SUB_REQUEST)
                ->getResponse();

            return $this->psrHttpFactory->createResponse($symfonyResponse);
        };
    }
}
