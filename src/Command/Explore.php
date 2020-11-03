<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Exploration\Contract\ExploreServiceInterface;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface;
use Heptacom\HeptaConnect\Portal\Base\Publication\Contract\PublisherInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Explore extends Command
{
    protected static $defaultName = 'heptaconnect:explore';

    private ExploreServiceInterface $exploreService;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PublisherInterface $publisher;

    public function __construct(
        ExploreServiceInterface $exploreService,
        StorageKeyGeneratorContract $storageKeyGenerator,
        PublisherInterface $publisher
    ) {
        parent::__construct();
        $this->exploreService = $exploreService;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->publisher = $publisher;
    }

    public function configure(): void
    {
        $this
            ->addArgument('portal-id', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
            ->addOption('external-id', null, InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-id'));

        if (!\is_a($portalNodeKey, PortalNodeKeyInterface::class, false)) {
            $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

            return 1;
        }

        $types = array_filter(
            array_map(fn (string $type) => trim($type, '\'"'), (array) $input->getArgument('type')),
            fn (string $type) => is_a($type, DatasetEntityInterface::class, true)
        );

        $externalId = $input->getOption('external-id');

        if (\is_string($externalId)) {
            foreach ($types as $type) {
                $this->publisher->publish($type, $portalNodeKey, $externalId);
            }
        } else {
            $this->exploreService->explore($portalNodeKey, empty($types) ? null : $types);
        }

        return 0;
    }
}
