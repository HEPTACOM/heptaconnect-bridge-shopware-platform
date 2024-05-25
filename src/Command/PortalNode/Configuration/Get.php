<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Configuration;

use Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:portal-node:config:get')]
class Get extends Command
{
    public function __construct(
        private ConfigurationServiceInterface $configurationService,
        private StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('name', InputArgument::OPTIONAL);
        $this->addOption('pretty', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
            }
        } catch (UnsupportedStorageKeyException) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $name = (string) $input->getArgument('name');
        $path = \array_filter(\explode('.', $name), 'strlen');

        $value = $this->configurationService->getPortalNodeConfiguration($portalNodeKey) ?? [];

        while (($subPath = \array_shift($path)) !== null) {
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
            $flags = $input->getOption('pretty') ? (\JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR) : \JSON_THROW_ON_ERROR;
            $output->writeln(\json_encode($value, $flags));
        }

        return 0;
    }
}
