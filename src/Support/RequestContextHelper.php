<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use Psr\Http\Message\UriInterface;
use Symfony\Component\Routing\RequestContext;

class RequestContextHelper
{
    public function scope(RequestContext $context, UriInterface $baseUrl, callable $callable)
    {
        $clonedContext = clone $context;

        try {
            if ($baseUrl->getScheme() !== '') {
                $context->setScheme($baseUrl->getScheme());
            }

            if ($baseUrl->getHost() !== '') {
                $context->setHost($baseUrl->getHost());
            }

            if (\is_int($baseUrl->getPort())) {
                $context->setHttpPort($baseUrl->getPort());
                $context->setHttpsPort($baseUrl->getPort());
            }

            if ($baseUrl->getPath() !== '') {
                $context->setBaseUrl(\ltrim($baseUrl->getPath(), '/'));
            }

            return $callable($context);
        } finally {
            $context->setScheme($clonedContext->getScheme());
            $context->setHost($clonedContext->getHost());
            $context->setHttpPort($clonedContext->getHttpPort());
            $context->setHttpsPort($clonedContext->getHttpsPort());
            $context->setBaseUrl($clonedContext->getBaseUrl());
        }
    }
}
