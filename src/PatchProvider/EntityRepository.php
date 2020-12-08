<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\PatchProvider;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class EntityRepository
{
    /**
     * @psalm-var array<callable(EntityRepositoryInterface): EntityRepositoryInterface>
     */
    private array $patchesToApply = [];

    public function __construct(string $shopwareVersion)
    {
        if (\version_compare($shopwareVersion, '6.3', '<')) {
            $this->patchesToApply[] = [$this, 'patchPullRequest587'];
        }
    }

    public function patch(EntityRepositoryInterface $toPatch): EntityRepositoryInterface
    {
        foreach ($this->patchesToApply as $patch) {
            $toPatch = $patch($toPatch);
        }

        return $toPatch;
    }

    public function patchPullRequest587(EntityRepositoryInterface $repository): EntityRepositoryInterface
    {
        return new EntityRepositoryPatch587($repository);
    }
}
