<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http;

use Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerUrlProviderFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerUrlProviderInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HttpHandlerUrlProviderFactory implements HttpHandlerUrlProviderFactoryInterface
{
    private StorageKeyGeneratorContract $storageKeyGenerator;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->urlGenerator = $urlGenerator;
    }

    public function factory(PortalNodeKeyInterface $portalNodeKey): HttpHandlerUrlProviderInterface
    {
        return new HttpHandlerUrlProvider(
            $portalNodeKey,
            $this->storageKeyGenerator,
            $this->urlGenerator
        );
    }
}
