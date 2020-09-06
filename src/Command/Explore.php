<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Exploration\Contract\ExploreServiceInterface;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Explore extends Command
{
    protected static $defaultName = 'heptaconnect:explore';

    private ExploreServiceInterface $exploreService;

    public function __construct(ExploreServiceInterface $exploreService)
    {
        parent::__construct();
        $this->exploreService = $exploreService;
    }

    public function configure(): void
    {
        $this->addArgument('portal-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $portalNodeKey = new PortalNodeStorageKey((string) $input->getArgument('portal-id'));

        $this->exploreService->explore($portalNodeKey);

        return 0;
    }
}
