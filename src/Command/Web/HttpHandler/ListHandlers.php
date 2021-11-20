<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler;

use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerUrlProviderFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListHandlers extends Command
{
    protected static $defaultName = 'heptaconnect:http-handler:list-handlers';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalNodeRepositoryContract $portalNodeRepository;

    private PortalStackServiceContainerFactory $portalStackServiceContainerFactory;

    private HttpHandlerUrlProviderFactoryInterface $httpHandlerUrlProviderFactory;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalNodeRepositoryContract $portalNodeRepository,
        PortalStackServiceContainerFactory $portalStackServiceContainerFactory,
        HttpHandlerUrlProviderFactoryInterface $httpHandlerUrlProviderFactory
    ) {
        parent::__construct();

        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeRepository = $portalNodeRepository;
        $this->portalStackServiceContainerFactory = $portalStackServiceContainerFactory;
        $this->httpHandlerUrlProviderFactory = $httpHandlerUrlProviderFactory;
    }

    protected function configure()
    {
        parent::configure();

        $this->addArgument('portal-node-key', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNode = (string) $input->getArgument('portal-node-key');

        if ($portalNode !== '') {
            $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNode);

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                $io->error('portal-node-key is not a portal node key');

                return 1;
            }

            $portalNodeKeys = [$portalNodeKey];
        } else {
            $portalNodeKeys = $this->portalNodeRepository->listAll();
        }

        $result = [];

        foreach ($portalNodeKeys as $portalNodeKey) {
            $container = $this->portalStackServiceContainerFactory->create($portalNodeKey);
            $handlers = new HttpHandlerCollection();
            $handlers->push($container->get(HttpHandlerCollection::class));
            $handlers->push($container->get(HttpHandlerCollection::class.'.decorator'));
            $paths = \array_unique(\iterable_to_array($handlers->column('getPath')));
            \sort($paths);
            $urlFactory = null;

            foreach ($paths as $path) {
                $urlFactory ??= $this->httpHandlerUrlProviderFactory->factory($portalNodeKey);

                $result[] = [
                    'portal-node' => $this->storageKeyGenerator->serialize($portalNodeKey),
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
