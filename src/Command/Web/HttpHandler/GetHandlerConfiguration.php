<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\WebHttpHandlerConfiguration\Find\WebHttpHandlerConfigurationFindCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\WebHttpHandlerConfigurationFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetHandlerConfiguration extends Command
{
    protected static $defaultName = 'heptaconnect:http-handler:get-configuration';

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private WebHttpHandlerConfigurationFindActionInterface $webHttpHandlerConfigurationFindAction
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('path', InputArgument::REQUIRED);
        $this->addArgument('key', InputArgument::REQUIRED);
        $this->addOption('pretty', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));
        $path = (string) $input->getArgument('path');
        $key = (string) $input->getArgument('key');
        $isPretty = (bool) $input->getOption('pretty');

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            $io->error('portal-node-key is not a portal node key');

            return 1;
        }

        $criteria = new WebHttpHandlerConfigurationFindCriteria($portalNodeKey, $path, $key);
        $find = $this->webHttpHandlerConfigurationFindAction->find($criteria);
        $flags = $isPretty ? (\JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES) : 0;
        $output->writeln((string) \json_encode($find->getValue(), $flags | \JSON_THROW_ON_ERROR));

        return 0;
    }
}
