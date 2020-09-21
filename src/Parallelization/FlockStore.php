<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Parallelization;

use Symfony\Component\Lock\Store\FlockStore as SymfonyFlockStore;

class FlockStore extends SymfonyFlockStore
{
    public function __construct(string $lockPath = null)
    {
        if (\is_string($lockPath) &&  !is_dir($lockPath) && !mkdir($lockPath, 0777, true) && !is_dir($lockPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $lockPath));
        }

        parent::__construct($lockPath);
    }
}
