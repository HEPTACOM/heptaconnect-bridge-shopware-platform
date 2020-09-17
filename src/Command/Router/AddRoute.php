<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddRoute extends Command
{
    protected static $defaultName = 'heptaconnect:router:add-route';

    private StorageInterface $storage;

    private PortalNodeRepositoryContract $portalNodeRepository;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        StorageInterface $storage,
        PortalNodeRepositoryContract $portalNodeRepository,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->storage = $storage;
        $this->portalNodeRepository = $portalNodeRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED)
            ->addArgument('target', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $source = $this->storageKeyGenerator->deserialize((string) $input->getArgument('source'));
        $target = $this->storageKeyGenerator->deserialize((string) $input->getArgument('target'));
        $type = (string) $input->getArgument('type');

        if (!$source instanceof PortalNodeKeyInterface) {
            $io->error('The source is not a portalNodeKey');

            return 1;
        }

        if (!$target instanceof PortalNodeKeyInterface) {
            $io->error('The target is not a portalNodeKey');

            return 1;
        }

        $this->portalNodeRepository->read($source);
        $this->portalNodeRepository->read($target);

        if (!\is_a($type, DatasetEntityInterface::class, true)) {
            $io->error('The specified type does not implement the DatasetEntityInterface.');

            return 1;
        }

        $this->storage->createRouteTarget($source, $target, $type);

        return 0;
    }
}
