<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\PortalNodeKeyCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNodeAlias\Get\PortalNodeAliasGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeAlias\PortalNodeAliasGetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Get extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:alias:get';

    private PortalNodeAliasGetActionInterface $aliasGetAction;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        PortalNodeAliasGetActionInterface $aliasGetAction,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->aliasGetAction = $aliasGetAction;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-keys', InputArgument::IS_ARRAY);
        $this->addOption('pretty', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKeys = [];

        foreach ($input->getArgument('portal-node-keys') as $keyData) {
            try {
                $portalNodeKey = $this->storageKeyGenerator->deserialize($keyData);

                if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                    throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
                }

                $portalNodeKeys[] = $portalNodeKey;
            } catch (UnsupportedStorageKeyException $exception) {
                $io->error('The portal-node-key is not a portalNodeKey');

                return 1;
            }
        }

        $criteria = new PortalNodeAliasGetCriteria(new PortalNodeKeyCollection($portalNodeKeys));
        $results = $this->aliasGetAction->get($criteria) ?? [];
        $alias = [];

        foreach ($results as $result) {
            $alias[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($result->getPortalNodeKey()),
                'alias' => $result->getAlias(),
            ];
        }

        $isPretty = $input->getOption('pretty');
        $flags = $isPretty ? (\JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES) : 0;
        $output->writeln((string) \json_encode($alias, $flags | \JSON_THROW_ON_ERROR));

        return 0;
    }
}
