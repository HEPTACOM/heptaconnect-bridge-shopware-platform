<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\Set\WebHttpHandlerConfigurationSetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\Set\WebHttpHandlerConfigurationSetPayload;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\Set\WebHttpHandlerConfigurationSetPayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetHandlerConfiguration extends Command
{
    protected static $defaultName = 'heptaconnect:http-handler:set-configuration';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private WebHttpHandlerConfigurationSetActionInterface $webHttpHandlerConfigurationSetAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        WebHttpHandlerConfigurationSetActionInterface $webHttpHandlerConfigurationSetAction
    ) {
        parent::__construct();

        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->webHttpHandlerConfigurationSetAction = $webHttpHandlerConfigurationSetAction;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('path', InputArgument::REQUIRED);
        $this->addArgument('key', InputArgument::REQUIRED);
        $this->addArgument('value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));
        $path = (string) $input->getArgument('path');
        $key = (string) $input->getArgument('key');
        $value = (string) $input->getArgument('value');

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            $io->error('portal-node-key is not a portal node key');

            return 1;
        }

        $parsed = null;

        if (!empty($value)) {
            $jsonDecoded = \json_decode($value);

            if (!\is_array($jsonDecoded)) {
                $jsonDecoded = ['value' => $jsonDecoded];
            }

            $parsed = $jsonDecoded;
        }

        $payload = new WebHttpHandlerConfigurationSetPayload($portalNodeKey, $path, $key, $parsed);
        $this->webHttpHandlerConfigurationSetAction->set(new WebHttpHandlerConfigurationSetPayloads([$payload]));

        return 0;
    }
}
