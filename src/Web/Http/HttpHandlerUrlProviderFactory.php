<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\RequestContextHelper;
use Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerUrlProviderFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class HttpHandlerUrlProviderFactory implements HttpHandlerUrlProviderFactoryInterface
{
    public function __construct(private StorageKeyGeneratorContract $storageKeyGenerator, private UrlGeneratorInterface $urlGenerator, private HttpHostProviderContract $hostProvider, private RequestContext $requestContext, private RequestContextHelper $requestContextHelper)
    {
    }

    public function factory(PortalNodeKeyInterface $portalNodeKey): HttpHandlerUrlProviderInterface
    {
        return new HttpHandlerUrlProvider(
            $portalNodeKey,
            $this->storageKeyGenerator,
            $this->urlGenerator,
            $this->requestContext,
            $this->hostProvider,
            $this->requestContextHelper
        );
    }
}
