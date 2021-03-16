<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Support\Alias;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasEntity;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Set extends Command
{
    protected static $defaultName = 'heptaconnect:support:alias:set';

    private EntityRepositoryInterface $aliasRepository;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        EntityRepositoryInterface $aliasRepository,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->aliasRepository = $aliasRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function configure(): void
    {
        $this->addArgument('original', InputArgument::REQUIRED);
        $this->addArgument('alias', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $original = (string) $input->getArgument('original');
        $alias = (string) $input->getArgument('alias');

        if ($original === '') {
            $io->error('The given original is empty');

            return 1;
        }

        if ($alias === '') {
            $io->error('The given alias is empty');

            return 2;
        }

        // validate that the original is a possible storage key
        $this->storageKeyGenerator->deserialize($original);

        try {
            // validate that the original is NOT a possible storage key
            $this->storageKeyGenerator->deserialize($alias);
            $io->error('The given key is a storage key. Using a key as alias creates more confusion than help and is forbidden');

            return 3;
        } catch (UnsupportedStorageKeyException $exception) {
        }

        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('alias', $alias),
            new EqualsFilter('original', $original),
        ]));
        $search = $this->aliasRepository->search($criteria, $context);
        $deletes = \array_values(\array_map(static fn (string $id): array => ['id' => $id], $search->getIds()));

        if ($deletes !== []) {
            $this->aliasRepository->delete($deletes, $context);
            $io->warning('The old alias has been deleted.');
            $rows = $search->getEntities()->map(static fn (KeyAliasEntity $entity): array => [
                'original' => $entity->getOriginal(),
                'alias' => $entity->getAlias(),
            ]);
            $io->table(\array_keys(\current($rows)), $rows);
        }

        $this->aliasRepository->create([[
            'alias' => $alias,
            'original' => $original,
        ]], $context);

        $io->success('A new alias was created.');

        return 0;
    }
}
