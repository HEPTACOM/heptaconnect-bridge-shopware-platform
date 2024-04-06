<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension;

use Heptacom\HeptaConnect\Core\Bridge\Portal\PortalLoaderInterface;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\PortalNodeKeyCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Get\PortalNodeGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Get\PortalNodeGetResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\PortalExtensionFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeGetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListExtensions extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:extensions:list';

    private PortalLoaderInterface $portalLoader;

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        PortalLoaderInterface $portalLoader,
        private PortalNodeGetActionInterface $portalNodeGetAction,
        private PortalExtensionFindActionInterface $portalExtensionFindAction
    ) {
        parent::__construct();
        $this->portalLoader = $portalLoader;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $portalExtensionFindResult = $this->portalExtensionFindAction->find($portalNodeKey);

        $portalNodeGetResults = \iterable_to_array($this->portalNodeGetAction->get(
            new PortalNodeGetCriteria(new PortalNodeKeyCollection([$portalNodeKey]))
        ));

        $portalNodeGetResult = \array_shift($portalNodeGetResults);

        if (!$portalNodeGetResult instanceof PortalNodeGetResult) {
            $io->error('Unable to find portal-node');

            return 2;
        }

        $extensions = $this->portalLoader->getPortalExtensions()->bySupport(
            $portalNodeGetResult->getPortalClass()
        );

        $extensionList = $extensions->map(static fn (PortalExtensionContract $extension): array => [
            'class' => $extension::class,
            'active' => $portalExtensionFindResult->isActive($extension) ? 'yes' : 'no',
        ]);

        $io->table(['class', 'active'], [...$extensionList]);

        return 0;
    }
}
