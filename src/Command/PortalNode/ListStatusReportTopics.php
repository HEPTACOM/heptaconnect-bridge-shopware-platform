<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Core\Portal\FlowComponentRegistry;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListStatusReportTopics extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:status:list-topics';

    public function __construct(
        private PortalStackServiceContainerFactory $portalStackServiceContainerFactory,
        private StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
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
        /** @var FlowComponentRegistry $flowComponentRegistry */
        $flowComponentRegistry = $container->get(FlowComponentRegistry::class);

        $topics = [];

        foreach ($flowComponentRegistry->getOrderedSources() as $source) {
            /** @var StatusReporterContract $statusReporter */
            foreach ($flowComponentRegistry->getStatusReporters($source) as $statusReporter) {
                $topics[] = $statusReporter->supportsTopic();
            }
        }

        $topics = \array_keys(\array_flip($topics));
        $rows = \array_map(static fn (string $topic): array => ['topic' => $topic], $topics);

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
