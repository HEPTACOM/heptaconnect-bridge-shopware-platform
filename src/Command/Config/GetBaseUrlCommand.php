<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Config;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHostProviderContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetBaseUrlCommand extends Command
{
    protected static $defaultName = 'heptaconnect:config:base-url:get';

    public function __construct(
        private HttpHostProviderContract $httpHostProvider
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln((string) $this->httpHostProvider->get());

        return 0;
    }
}
