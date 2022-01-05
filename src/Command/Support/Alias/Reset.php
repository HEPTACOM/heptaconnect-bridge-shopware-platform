<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Support\Alias;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasEntity;
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

class Reset extends Command
{
    protected static $defaultName = 'heptaconnect:support:alias:reset';

    private EntityRepositoryInterface $aliasRepository;

    public function __construct(EntityRepositoryInterface $aliasRepository)
    {
        parent::__construct();
        $this->aliasRepository = $aliasRepository;
    }

    protected function configure(): void
    {
        $this->addArgument('original-or-alias', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $originalOrAlias = (string) $input->getArgument('original-or-alias');

        if ($originalOrAlias === '') {
            $io->error('The given original-or-alias is empty');

            return 1;
        }

        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('alias', $originalOrAlias),
            new EqualsFilter('original', $originalOrAlias),
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

        $io->success('Any alias matching the query has been deleted.');

        return 0;
    }
}
