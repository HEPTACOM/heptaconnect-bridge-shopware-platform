<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\CronjobCollection;
use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database\CronjobEntity;
use Heptacom\HeptaConnect\Portal\Base\Cronjob\Contract\CronjobInterface;
use Heptacom\HeptaConnect\Storage\Base\Exception\NotFoundException;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Uuid\Uuid;

class CronjobStorage
{
    private EntityRepositoryInterface $cronjobs;

    private KeyGenerator $keyGenerator;

    private EntityRepositoryInterface $cronjobRuns;

    public function __construct(
        EntityRepositoryInterface $cronjobs,
        KeyGenerator $keyGenerator,
        EntityRepositoryInterface $cronjobRuns
    ) {
        $this->cronjobs = $cronjobs;
        $this->keyGenerator = $keyGenerator;
        $this->cronjobRuns = $cronjobRuns;
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
    public function getNextToQueue(?\DateTimeInterface $until): iterable
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addSorting(
            new FieldSorting('queuedUntil', FieldSorting::DESCENDING),
            new FieldSorting('createdAt', FieldSorting::ASCENDING)
        );

        if ($until instanceof \DateTimeInterface) {
            $criteria->addFilter(new RangeFilter('queuedUntil', [
                RangeFilter::LTE => $until->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]));
        }

        $iterator = new RepositoryIterator($this->cronjobs, $context, $criteria);

        while (($result = $iterator->fetch()) instanceof EntitySearchResult) {
            /** @var CronjobCollection $cronjobs */
            $cronjobs = $result->getEntities();

            yield from $cronjobs->getElements();
        }
    }

    /**
     * @throws NotFoundException
     */
    public function markAsQueuedUntil(string $cronjobId, \DateTimeInterface $nextQueuing): void
    {
        $context = Context::createDefaultContext();
        $cronjobIds = $this->cronjobs->searchIds(new Criteria([$cronjobId]), $context);

        if ($cronjobIds->getTotal() <= 0) {
            throw new NotFoundException();
        }

        $this->cronjobs->update([[
            'id' => $cronjobId,
            'queuedUntil' => $nextQueuing,
        ]], $context);
    }

    public function remove(string $cronjobId): void
    {
        $context = Context::createDefaultContext();
        $cronjobIds = $this->cronjobs->searchIds(new Criteria([$cronjobId]), $context);

        if ($cronjobIds->getTotal() <= 0) {
            throw new NotFoundException();
        }

        $this->cronjobs->delete([[
            'id' => $cronjobId,
        ]], $context);
    }

    /**
     * @throws NotFoundException
     */
    public function createRun(string $cronjobId, \DateTimeInterface $queuedFor): string
    {
        $context = Context::createDefaultContext();
        $id = Uuid::randomHex();
        /** @var CronjobCollection $cronjobs */
        $cronjobs = $this->cronjobs->search(new Criteria([$cronjobId]), $context)->getEntities();
        $first = $cronjobs->first();

        if (!$first instanceof CronjobEntity) {
            throw new NotFoundException();
        }

        $this->cronjobRuns->create([[
            'id' => $id,
            'cronjobId' => $first->getId(),
            'handler' => $first->getHandler(),
            'payload' => $first->getPayload(),
            'queuedFor' => $queuedFor,
        ]], $context);

        return $id;
    }
}
