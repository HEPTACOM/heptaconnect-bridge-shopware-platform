<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListMappingNodes extends Command
{
    protected static $defaultName = 'heptaconnect:mapping-node:list';

    private MappingRepositoryContract $mappingRepository;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        MappingRepositoryContract $mappingRepository,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->mappingRepository = $mappingRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function configure()
    {
        $this->addArgument('entity-type', InputArgument::REQUIRED)
            ->addArgument('portal-node-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $entityType = (string) $input->getArgument('entity-type');
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

        if (!\is_a($entityType, DatasetEntityContract::class, true)) {
            $io->error('The provided dataset entity class does not implement the DatasetEntityContract.');

            return 1;
        }

        if (!\is_a($portalNodeKey, PortalNodeKeyInterface::class, false)) {
            $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

            return 2;
        }

        $rows = [];
        $iterator = $this->mappingRepository->listByPortalNodeAndType($portalNodeKey, $entityType);

        foreach ($iterator as $mappingKey) {
            $mapping = $this->mappingRepository->read($mappingKey);
            $rows[] = [
                'mapping-node-id' => $this->storageKeyGenerator->serialize($mapping->getMappingNodeKey()),
                'external-id' => $mapping->getExternalId(),
            ];
        }

        if (empty($rows)) {
            $io->note('There are no mapping nodes of the selected portal.');

            return 0;
        }

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
