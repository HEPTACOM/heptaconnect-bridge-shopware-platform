<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class KeyAliasEntity extends Entity
{
    use EntityIdTrait;

    protected string $alias = '';

    protected string $original = '';

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function getOriginal(): string
    {
        return $this->original;
    }

    public function setOriginal(string $original): void
    {
        $this->original = $original;
    }
}
