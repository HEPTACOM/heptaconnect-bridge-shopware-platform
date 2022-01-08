<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Exploration\Contract\ExploreServiceInterface;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
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
            ->addArgument('portal-node-key', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
            ->addOption('external-id', null, InputOption::VALUE_OPTIONAL)
            ->addOption('use-queue', null, InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = \microtime(true);

        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

        if (!\is_a($portalNodeKey, PortalNodeKeyInterface::class, false)) {
            $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

            return 1;
        }

        $inTypes = (array) $input->getArgument('type');
        $types = \array_filter(
            \array_map(fn (string $type) => \trim($type, '\'"'), $inTypes),
            static fn (string $type) => \is_a($type, DatasetEntityContract::class, true)
        );
        $wrongTypes = \array_diff_key($inTypes, $types);

        if ($wrongTypes !== []) {
            $io->error(['The provided types are not a DatasetEntityContract:', ...\array_values($wrongTypes)]);

            return 2;
        }

        $externalId = $input->getOption('external-id');

        if (\is_string($externalId)) {
            foreach ($types as $type) {
                $this->publisher->publish($type, $portalNodeKey, $externalId);
            }
        } else {
            if ($input->getOption('use-queue')) {
                $this->exploreService->dispatchExploreJob($portalNodeKey, empty($types) ? null : $types);
            } else {
                $this->exploreService->explore($portalNodeKey, empty($types) ? null : $types);
            }
        }

        if ($output->isVerbose()) {
            $io->note(\sprintf('Took %s seconds.', \microtime(true) - $startTime));
        }

        return 0;
    }
}
