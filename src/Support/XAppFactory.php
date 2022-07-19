<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use FrameworkX\App;
use FrameworkX\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class XAppFactory implements XAppFactoryInterface
{
    private ContainerInterface $container;

    private RouterInterface $router;

    private KernelInterface $kernel;

    private HttpMessageFactoryInterface $psrHttpFactory;

    private HttpFoundationFactoryInterface $httpFoundationFactory;

    public function __construct(
        ContainerInterface $container,
        RouterInterface $router,
        KernelInterface $kernel,
        HttpMessageFactoryInterface $psrHttpFactory,
        HttpFoundationFactoryInterface $httpFoundationFactory
    ) {
        $this->container = $container;
        $this->router = $router;
        $this->kernel = $kernel;
        $this->psrHttpFactory = $psrHttpFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
    }

    public function factory(): App
    {
        if (!\class_exists(App::class)) {
            throw new \LogicException('Framework X must be installed to use this factory. Try running "composer require clue/framework-x:dev-main".');
        }

        $app = new App(new Container($this->container));
        $handler = $this->getHandler();

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

    private function getHandler(): \Closure
    {
        return function (ServerRequestInterface $psrRequest): ResponseInterface {
            $symfonyRequest = $this->httpFoundationFactory->createRequest($psrRequest);

            $symfonyResponse = $this->kernel->handle($symfonyRequest, HttpKernelInterface::SUB_REQUEST);

            return $this->psrHttpFactory->createResponse($symfonyResponse);
        };
    }
}
