<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalExtensionContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\Find\PortalExtensionFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
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

    private PortalNodeRepositoryContract $portalNodeRepository;

    private ComposerPortalLoader $portalLoader;

    private PortalExtensionFindActionInterface $portalExtensionFindAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalNodeRepositoryContract $portalNodeRepository,
        ComposerPortalLoader $portalLoader,
        PortalExtensionFindActionInterface $portalExtensionFindAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeRepository = $portalNodeRepository;
        $this->portalLoader = $portalLoader;
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

        $portalClass = $this->portalNodeRepository->read($portalNodeKey);
        $extensions = $this->portalLoader->getPortalExtensions()->filterSupported($portalClass);

        $extensionList = $extensions->map(static fn (PortalExtensionContract $extension): array => [
            'class' => \get_class($extension),
            'active' => $portalExtensionFindResult->isActive($extension) ? 'yes' : 'no',
        ]);

        $io->table(['class', 'active'], [...$extensionList]);

        return 0;
    }
}
