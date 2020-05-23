<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Portal\Base\Contract\StorageKeyInterface;

abstract class AbstractKey implements StorageKeyInterface
{
    private string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): AbstractKey
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function equals(StorageKeyInterface $other): bool
    {
        if (!\is_a($other, static::class, false)) {
            return false;
        }

        /* @var $other AbstractKey */
        return $other->getUuid() === $this->getUuid();
    }

    /**
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->uuid;
    }
}
