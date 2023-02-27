<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\TestFlow;

use Heptacom\HeptaConnect\Core\Mapping\MappingNodeStruct;
use Heptacom\HeptaConnect\Core\Mapping\MappingStruct;
use Heptacom\HeptaConnect\Core\Reception\Contract\ReceiveServiceInterface;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\TypedDatasetEntityCollection;
use Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\MappingNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Receive extends Command
{
    protected static $defaultName = 'heptaconnect:test-flow:receive';

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private ReceiveServiceInterface $receiveService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('portal-node-key', InputArgument::REQUIRED)
            ->addArgument('data-file', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

            return 1;
        }

        $dataFile = (string) $input->getArgument('data-file');

        if (!\is_file($dataFile)) {
            $io->error('The provided data-file does not exist.');

            return 1;
        }

        $callable = require $dataFile;
        /** @var DatasetEntityContract $entity */
        $entity = $callable();

        $this->receiveService->receive(new TypedDatasetEntityCollection($entity::class(), [$entity]), $portalNodeKey);

        return 0;
    }

    protected function getMapping(
        PortalNodeKeyInterface $portalNodeKey,
        DatasetEntityContract $entity
    ): MappingInterface {
        $mappingNodeKeys = \iterable_to_array(
            $this->storageKeyGenerator->generateKeys(MappingNodeKeyInterface::class, 1)
        );

        $mapping = new MappingStruct($portalNodeKey, new MappingNodeStruct(
            \reset($mappingNodeKeys),
            $entity::class()
        ));

        $mapping->setExternalId($entity->getPrimaryKey());

        return $mapping;
    }
}
