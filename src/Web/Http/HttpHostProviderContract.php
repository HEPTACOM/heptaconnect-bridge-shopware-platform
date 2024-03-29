<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class HttpHostProviderContract
{
    private UriFactoryInterface $uriFactory;

    public function __construct(
        private SystemConfigService $systemConfigService,
        private string $appUrl
    ) {
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
    }

    public function get(): UriInterface
    {
        if ($this->appUrl === '') {
            /** @var string|null $baseUrl */
            $baseUrl = $this->systemConfigService->get('heptacom.heptaConnect.globalConfiguration.baseUrl');
        } else {
            $baseUrl = $this->appUrl;
        }

        if ($baseUrl === null) {
            $baseUrl = 'localhost';
        }

        if (!\str_contains($baseUrl, '//')) {
            $baseUrl = '//' . $baseUrl;
        }

        $uri = $this->uriFactory->createUri();

        $urlComponents = \parse_url($baseUrl);

        if (!\is_array($urlComponents)) {
            return $uri;
        }

        $uri = $uri->withScheme($urlComponents['scheme'] ?? 'http');

        if (isset($urlComponents['host'])) {
            $uri = $uri->withHost($urlComponents['host']);
        }

        if (isset($urlComponents['port'])) {
            $uri = $uri->withPort($urlComponents['port']);
        }

        if (isset($urlComponents['path'])) {
            $uri = $uri->withPath($urlComponents['path']);
        }

        return $uri;
    }
}
