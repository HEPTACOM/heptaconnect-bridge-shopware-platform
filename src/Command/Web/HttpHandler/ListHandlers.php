<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler;

use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerUrlProviderFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Listing\PortalNodeListResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeListActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:http-handler:list-handlers')]
class ListHandlers extends Command
{
    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private PortalStackServiceContainerFactory $portalStackServiceContainerFactory,
        private HttpHandlerUrlProviderFactoryInterface $httpHandlerUrlProviderFactory,
        private PortalNodeListActionInterface $portalNodeListAction
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('portal-node-key', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $portalNode = (string) $input->getArgument('portal-node-key');

        if ($portalNode !== '') {
            $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNode);

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                $io->error('portal-node-key is not a portal node key');

                return 1;
            }

            /** @var PortalNodeKeyInterface[] $portalNodeKeys */
            $portalNodeKeys = [$portalNodeKey];
        } else {
            /** @var PortalNodeKeyInterface[] $portalNodeKeys */
            $portalNodeKeys = \iterable_map(
                $this->portalNodeListAction->list(),
                static fn (PortalNodeListResult $r) => $r->getPortalNodeKey()
            );
        }

        $result = [];

        foreach ($portalNodeKeys as $portalNodeKey) {
            $flowComponentRegistry = $this->portalStackServiceContainerFactory
                ->create($portalNodeKey)
                ->getFlowComponentRegistry();
            $handlers = new HttpHandlerCollection();

            foreach ($flowComponentRegistry->getOrderedSources() as $source) {
                $handlers->push($flowComponentRegistry->getWebHttpHandlers($source));
            }

            /** @var string[] $paths */
            $paths = \array_unique(\iterable_to_array($handlers->column('getPath')));
            \sort($paths);
            $urlFactory = null;

            foreach ($paths as $path) {
                $urlFactory ??= $this->httpHandlerUrlProviderFactory->factory($portalNodeKey);

                $result[] = [
                    'portal-node' => $this->storageKeyGenerator->serialize($portalNodeKey->withAlias()),
                    'path' => $path,
                    'url' => $urlFactory->resolve($path),
                ];
            }
        }

        if ($result === []) {
            $io->note('There are no supported HTTP handlers.');

            return 0;
        }

        $io->table(\array_keys(\current($result)), $result);

        return 0;
    }
}
