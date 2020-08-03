<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\CronjobCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\CronjobEntity;
use Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobInterface;
use Heptacom\HeptaConnect\Storage\Base\Exception\NotFoundException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class CronjobStorage
{
    private EntityRepositoryInterface $cronjobs;

    private KeyGenerator $keyGenerator;

    public function __construct(EntityRepositoryInterface $cronjobs, KeyGenerator $keyGenerator)
    {
        $this->cronjobs = $cronjobs;
        $this->keyGenerator = $keyGenerator;
    }

    public function create(string $cronExpression, string $handler, \DateTimeInterface $nextExecution, ?array $payload = null): CronjobInterface
    {
        $context = Context::createDefaultContext();
        $key = $this->keyGenerator->generateCronjobKey();

        $this->cronjobs->create([[
            'id' => $key->getUuid(),
            'cronExpression' => $cronExpression,
            'handler' => $handler,
            'payload' => $payload,
            'queuedUntil' => $nextExecution,
        ]], $context);

        /** @var CronjobCollection $cronjobs */
        $cronjobs = $this->cronjobs->search(new Criteria([$key->getUuid()]), $context)->getEntities();
        /** @var CronjobEntity $first */
        $first = $cronjobs->first();

        return $first;
    }

    /**
     * @psalm-return iterable<array-key, \Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\CronjobEntity>
     *
     * @return CronjobEntity[]
     */
    public function getNextToQueue(): iterable
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('queuedUntil', [
            RangeFilter::LTE => new \DateTime(),
        ]));
        $criteria->addSorting(
            new FieldSorting('queuedUntil', FieldSorting::DESCENDING),
            new FieldSorting('createdAt', FieldSorting::ASCENDING)
        );
        $iterator = new RepositoryIterator($this->cronjobs, $context, $criteria);

        while (($iterator = $iterator->fetch()) instanceof EntitySearchResult) {
            /** @var CronjobCollection $cronjobs */
            $cronjobs = $iterator->getEntities();

            yield from $cronjobs->getElements();
        }
    }

    /**
     * @throws NotFoundException
     */
    public function markAsQueuedUntil(CronjobKey $cronjobKey, \DateTimeInterface $nextQueuing): void
    {
        $context = Context::createDefaultContext();
        $cronjobIds = $this->cronjobs->searchIds(new Criteria([$cronjobKey->getUuid()]), $context);

        if ($cronjobIds->getTotal() <= 0) {
            throw new NotFoundException();
        }

        $this->cronjobs->update([[
            'id' => $cronjobKey->getUuid(),
            'queuedUntil' => $nextQueuing,
        ]], $context);
    }

    public function remove(CronjobKey $cronjobKey): void
    {
        $context = Context::createDefaultContext();
        $cronjobIds = $this->cronjobs->searchIds(new Criteria([$cronjobKey->getUuid()]), $context);

        if ($cronjobIds->getTotal() <= 0) {
            throw new NotFoundException();
        }

        $this->cronjobs->delete([[
            'id' => $cronjobKey->getUuid(),
        ]], $context);
    }
}
