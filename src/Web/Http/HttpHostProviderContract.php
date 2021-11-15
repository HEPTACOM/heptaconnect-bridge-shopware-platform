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

    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $this->systemConfigService = $systemConfigService;
    }

    public function get(): UriInterface
    {
        $baseUrl = (string) $this->systemConfigService->get('heptacom.heptaConnect.globalConfiguration.baseUrl');

        if (!$baseUrl) {
            $baseUrl = 'localhost';
        }

        if (strpos($baseUrl, '//') === false) {
            $baseUrl = '//' . $baseUrl;
        }

        $urlComponents = \parse_url($baseUrl);

        $uri = $this->uriFactory->createUri();

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
