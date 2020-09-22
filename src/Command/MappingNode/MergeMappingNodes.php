<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode;

use Heptacom\HeptaConnect\Core\Mapping\Contract\MappingServiceInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\MappingNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MergeMappingNodes extends Command
{
    protected static $defaultName = 'heptaconnect:mapping-node:merge';

    private MappingServiceInterface $mappingService;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        MappingServiceInterface $mappingService,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->mappingService = $mappingService;
    }

    protected function configure()
    {
        $this->addArgument('mapping-node-key-from', InputArgument::REQUIRED)
            ->addArgument('mapping-node-key-into', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $mappingNodeFrom = $this->storageKeyGenerator->deserialize((string) $input->getArgument('mapping-node-key-from'));
        $mappingNodeInto = $this->storageKeyGenerator->deserialize((string) $input->getArgument('mapping-node-key-into'));

        if (!$mappingNodeFrom instanceof MappingNodeKeyInterface) {
            $io->error('The provided mapping-node-key-from is not a MappingNodeKeyInterface.');

            return 1;
        }

        if (!$mappingNodeInto instanceof MappingNodeKeyInterface) {
            $io->error('The provided mapping-node-key-into is not a MappingNodeKeyInterface.');

            return 2;
        }

        $this->mappingService->merge($mappingNodeFrom, $mappingNodeInto);

        return 0;
    }
}
