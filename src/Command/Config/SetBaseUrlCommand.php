<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Config;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetBaseUrlCommand extends Command
{
    protected static $defaultName = 'heptaconnect:config:base-url:set';

    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        parent::__construct();
        $this->systemConfigService = $systemConfigService;
    }

    protected function configure(): void
    {
        $this->addArgument('base-url', InputArgument::REQUIRED, 'Base-URL for HTTP interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $baseUrl = $input->getArgument('base-url');

        if (!$baseUrl) {
            throw new \Exception('Missing parameter "base-url"');
        }

        $this->systemConfigService->set(
            'heptacom.heptaConnect.globalConfiguration.baseUrl',
            (string) $baseUrl
        );

        return 0;
    }
}
