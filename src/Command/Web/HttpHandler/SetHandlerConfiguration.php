<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\Web\Http\HttpHandlerStackIdentifier;
use Heptacom\HeptaConnect\Storage\Base\Action\WebHttpHandlerConfiguration\Set\WebHttpHandlerConfigurationSetPayload;
use Heptacom\HeptaConnect\Storage\Base\Action\WebHttpHandlerConfiguration\Set\WebHttpHandlerConfigurationSetPayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\WebHttpHandlerConfigurationSetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:http-handler:set-configuration')]
class SetHandlerConfiguration extends Command
{
    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private WebHttpHandlerConfigurationSetActionInterface $webHttpHandlerConfigurationSetAction
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
        $this->addArgument('path', InputArgument::REQUIRED);
        $this->addArgument('key', InputArgument::REQUIRED);
        $this->addArgument('value');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));
        /** @var string $path */
        $path = $input->getArgument('path');
        /** @var string $key */
        $key = $input->getArgument('key');
        /** @var string|null $value */
        $value = $input->getArgument('value');

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            $io->error('portal-node-key is not a portal node key');

            return 1;
        }

        $parsed = null;

        if (\is_string($value)) {
            $jsonDecoded = \json_decode($value, null, 512, \JSON_THROW_ON_ERROR);

            if (!\is_array($jsonDecoded)) {
                $jsonDecoded = ['value' => $jsonDecoded];
            }

            $parsed = $jsonDecoded;
        }

        $payload = new WebHttpHandlerConfigurationSetPayload(new HttpHandlerStackIdentifier($portalNodeKey, $path), $key, $parsed);
        $this->webHttpHandlerConfigurationSetAction->set(new WebHttpHandlerConfigurationSetPayloads([$payload]));

        return 0;
    }
}
