<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\PortalNodeKeyCollection;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\Find\PortalExtensionFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Get\PortalNodeGetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Get\PortalNodeGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Get\PortalNodeGetResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListExtensions extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:extensions:list';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private ComposerPortalLoader $portalLoader;

    private PortalNodeGetActionInterface $portalNodeGetAction;

    private PortalExtensionFindActionInterface $portalExtensionFindAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        ComposerPortalLoader $portalLoader,
        PortalNodeGetActionInterface $portalNodeGetAction,
        PortalExtensionFindActionInterface $portalExtensionFindAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalLoader = $portalLoader;
        $this->portalNodeGetAction = $portalNodeGetAction;
        $this->portalExtensionFindAction = $portalExtensionFindAction;
    }

    protected function configure()
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $portalNodeKey = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            throw new \Exception('Invalid portal-node-key');
        }

        $portalExtensionFindResult = $this->portalExtensionFindAction->find($portalNodeKey);

        $portalNodeGetResults = \iterable_to_array($this->portalNodeGetAction->get(
            new PortalNodeGetCriteria(new PortalNodeKeyCollection([$portalNodeKey]))
        ));

        $portalNodeGetResult = \array_shift($portalNodeGetResults);

        if (!$portalNodeGetResult instanceof PortalNodeGetResult) {
            throw new \Exception('Unable to find portal-node');
        }

        $extensions = $this->portalLoader->getPortalExtensions()->filterSupported(
            $portalNodeGetResult->getPortalClass()
        );

        $extensionList = $extensions->map(static fn (PortalExtensionContract $extension): array => [
            'class' => \get_class($extension),
            'active' => $portalExtensionFindResult->isActive($extension) ? 'yes' : 'no',
        ]);

        $io->table(['class', 'active'], [...$extensionList]);

        return 0;
    }
}
