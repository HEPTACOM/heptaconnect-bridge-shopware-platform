<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\StatusReporterCollection;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListReportTopicsForPortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:list-status-topics';

    private PortalStackServiceContainerFactory $portalStackServiceContainerFactory;
    
    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        PortalStackServiceContainerFactory $portalStackServiceContainerFactory,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->portalStackServiceContainerFactory = $portalStackServiceContainerFactory;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function configure()
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));
            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
            }
        } catch (UnsupportedStorageKeyException $exception) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $container = $this->portalStackServiceContainerFactory->create($portalNodeKey);
        /** @var StatusReporterCollection $statusReporters */
        $statusReporters = $container->get(StatusReporterCollection::class);

        $topics = [];
        foreach ($statusReporters as $statusReporter) {
            $topics[] = $statusReporter->supportsTopic();
        }

        $topics = \array_unique($topics);
        $output->writeln(\json_encode($topics));

        return 0;
    }
}
