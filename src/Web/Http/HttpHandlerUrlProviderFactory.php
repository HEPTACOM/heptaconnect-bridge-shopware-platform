<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http;

use Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerUrlProviderFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class HttpHandlerUrlProviderFactory implements HttpHandlerUrlProviderFactoryInterface
{
    private StorageKeyGeneratorContract $storageKeyGenerator;

    private UrlGeneratorInterface $urlGenerator;

    private HttpHostProviderContract $hostProvider;

    private RequestContext $requestContext;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        UrlGeneratorInterface $urlGenerator,
        HttpHostProviderContract $hostProvider,
        RequestContext $requestContext
    ) {
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->hostProvider = $hostProvider;
        $this->requestContext = $requestContext;
    }

    public function factory(PortalNodeKeyInterface $portalNodeKey): HttpHandlerUrlProviderInterface
    {
        return new HttpHandlerUrlProvider(
            $portalNodeKey,
            $this->storageKeyGenerator,
            $this->urlGenerator,
            $this->requestContext,
            $this->hostProvider
        );
    }
}
