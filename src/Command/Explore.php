<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Exploration\Contract\ExploreServiceInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Explore extends Command
{
    protected static $defaultName = 'heptaconnect:explore';

    private ExploreServiceInterface $exploreService;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        ExploreServiceInterface $exploreService,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->exploreService = $exploreService;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    public function configure(): void
    {
        $this->addArgument('portal-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-id'));

        if (!\is_a($portalNodeKey, PortalNodeKeyInterface::class, false)) {
            $io->error('The provided portal-node-key is not a PortalNodeKeyInterface.');

            return 1;
        }

        $this->exploreService->explore($portalNodeKey);

        return 0;
    }
}
