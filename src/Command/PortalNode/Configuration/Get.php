<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Configuration;

use Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface;
use Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKey\PortalNodeStorageKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Get extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:config:get';

    private ConfigurationServiceInterface $configurationService;

    public function __construct(ConfigurationServiceInterface $configurationService)
    {
        parent::__construct();
        $this->configurationService = $configurationService;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-id', InputArgument::REQUIRED);
        $this->addArgument('name', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = new PortalNodeStorageKey((string) $input->getArgument('portal-id'));
        $name = (string) $input->getArgument('name');
        $path = \array_filter(\explode('.', $name), 'strlen');

        $value = $this->configurationService->getPortalNodeConfiguration($portalNodeKey) ?? [];

        while (!\is_null($subPath = \array_shift($path))) {
            if (!\is_array($value)) {
                $io->error(\sprintf('Could not get value for path %s as %s is not an array', $name, $subPath));

                return 1;
            }

            if (!\array_key_exists($subPath, $value)) {
                $io->error(\sprintf('Could not get value for path %s as %s is not present in the array', $name, $subPath));

                return 2;
            }

            $value = $value[$subPath];
        }

        if (\is_string($value)) {
            $output->writeln($value);
        } else {
            $output->writeln(\json_encode($value));
        }

        return 0;
    }
}
