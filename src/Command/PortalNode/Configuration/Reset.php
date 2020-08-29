<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Configuration;

use Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Reset extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:config:reset';

    private ConfigurationServiceInterface $configurationService;

    public function __construct(ConfigurationServiceInterface $configurationService)
    {
        parent::__construct();
        $this->configurationService = $configurationService;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = new PortalNodeStorageKey((string) $input->getArgument('portal-id'));

        $this->configurationService->setPortalNodeConfiguration($portalNodeKey, null);
        $io->success('The portal node configuration has been reset.');

        return 0;
    }
}
