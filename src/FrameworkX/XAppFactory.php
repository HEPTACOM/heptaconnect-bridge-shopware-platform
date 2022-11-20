<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\FrameworkX;

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
    public function __construct(private ContainerInterface $container, private RouterInterface $router, private KernelInterface $kernel, private HttpMessageFactoryInterface $psrHttpFactory, private HttpFoundationFactoryInterface $httpFoundationFactory)
    {
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
            if (!str_starts_with((string) $name, 'api.heptaconnect.')) {
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
                $methods = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
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
