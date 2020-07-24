<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Storage\PortalNodeKey;
use Heptacom\HeptaConnect\Core\Explore\Contract\ExploreServiceInterface;
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

    public function configure()
    {
        $this->addArgument('portal-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $portalNodeKey = new PortalNodeKey($input->getArgument('portal-id'));

        $this->exploreService->explore($portalNodeKey);
    }
}