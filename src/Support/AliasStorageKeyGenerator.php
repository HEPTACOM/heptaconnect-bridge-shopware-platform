<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasEntity;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class AliasStorageKeyGenerator extends StorageKeyGeneratorContract
{
    private StorageKeyGeneratorContract $decorated;

    private EntityRepositoryInterface $aliasRepository;

    public function __construct(StorageKeyGeneratorContract $decorated, EntityRepositoryInterface $aliasRepository)
    {
        $this->decorated = $decorated;
        $this->aliasRepository = $aliasRepository;
    }

    public function generateKey(string $keyClassName): StorageKeyInterface
    {
        return $this->decorated->generateKey($keyClassName);
    }

    public function generateKeys(string $keyClassName, int $count): iterable
    {
        return $this->decorated->generateKeys($keyClassName, $count);
    }

    public function serialize(StorageKeyInterface $key): string
    {
        return $this->replaceWithAlias($this->decorated->serialize($key));
    }

    public function deserialize(string $keyData): StorageKeyInterface
    {
        return $this->decorated->deserialize($this->replaceWithOriginal($keyData));
    }

    protected function replaceWithAlias(string $original): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('original', $original));
        $criteria->setLimit(1);
        $result = $this->aliasRepository->search($criteria, Context::createDefaultContext())->first();

        if ($result instanceof KeyAliasEntity) {
            return $result->getAlias();
        }

        return $original;
    }

    protected function replaceWithOriginal(string $alias): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('alias', $alias));
        $criteria->setLimit(1);
        $result = $this->aliasRepository->search($criteria, Context::createDefaultContext())->first();

        if ($result instanceof KeyAliasEntity) {
            return $result->getOriginal();
        }

        return $alias;
    }
}
