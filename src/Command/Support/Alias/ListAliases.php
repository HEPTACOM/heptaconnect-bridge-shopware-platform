<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Support\Alias;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListAliases extends Command
{
    protected static $defaultName = 'heptaconnect:support:alias:list';

    private EntityRepositoryInterface $aliasRepository;

    public function __construct(EntityRepositoryInterface $aliasRepository)
    {
        parent::__construct();
        $this->aliasRepository = $aliasRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addSorting(new FieldSorting('alias'));
        $search = $this->aliasRepository->search($criteria, $context);
        $rows = $search->getEntities()->map(static fn (KeyAliasEntity $entity): array => [
            'original' => $entity->getOriginal(),
            'alias' => $entity->getAlias(),
        ]);

        if (empty($rows)) {
            $io->note('There are no aliases.');

            return 0;
        }

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
