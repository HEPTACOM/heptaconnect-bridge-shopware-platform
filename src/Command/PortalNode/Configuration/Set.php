<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Configuration;

use Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Set extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:config:set';

    private ConfigurationServiceInterface $configurationService;

    public function __construct(ConfigurationServiceInterface $configurationService)
    {
        parent::__construct();
        $this->configurationService = $configurationService;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-id', InputArgument::REQUIRED);
        $this->addArgument('name', InputArgument::REQUIRED);
        $this->addArgument('value', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $portalNodeKey = new PortalNodeStorageKey((string) $input->getArgument('portal-id'));
        $name = (string) $input->getArgument('name');
        $value = (string) $input->getArgument('value');
        $jsonValue = null;
        $jsonParsing = false;

        try {
            $jsonValue = \json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
            $jsonParsing = true;
        } catch (\Throwable $exception) {
        }

        if ($name === '') {
            if (!\is_array($jsonValue)) {
                $io->error('No name is given but the value is not an array either');

                return 1;
            }

            $value = $jsonValue;
        } else {
            $value = [$name => $jsonParsing ? $jsonValue : $value];
        }

        $this->configurationService->setPortalNodeConfiguration($portalNodeKey, $value);
        $io->success('The portal node configuration has been set.');

        return 0;
    }
}
