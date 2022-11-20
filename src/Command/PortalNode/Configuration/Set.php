<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Configuration;

use Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Set extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:config:set';

    public function __construct(
        private ConfigurationServiceInterface $configurationService,
        private StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('name', InputArgument::REQUIRED);
        $this->addArgument('value', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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
        $value = (string) $input->getArgument('value');
        $jsonValue = null;
        $jsonParsing = false;

        try {
            $jsonValue = \json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
            $jsonParsing = true;
        } catch (\Throwable) {
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
