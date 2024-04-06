<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias;

use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Find\PortalNodeAliasFindCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'heptaconnect:portal-node:alias:find')]
class Find extends Command
{
    public function __construct(
        private PortalNodeAliasFindActionInterface $aliasFindAction,
        private StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('alias', InputArgument::IS_ARRAY);
        $this->addOption('pretty', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $aliasFindCriteria = new PortalNodeAliasFindCriteria($input->getArgument('alias'));
        $results = $this->aliasFindAction->find($aliasFindCriteria);
        $alias = [];

        foreach ($results as $result) {
            $alias[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($result->getPortalNodeKey()->withoutAlias()),
                'alias' => $result->getAlias(),
            ];
        }

        $isPretty = $input->getOption('pretty');
        $flags = $isPretty ? (\JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES) : 0;
        $output->writeln((string) \json_encode($alias, $flags | \JSON_THROW_ON_ERROR));

        return 0;
    }
}
