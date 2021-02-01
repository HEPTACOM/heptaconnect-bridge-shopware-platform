<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemovePortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:remove';

    private PortalNodeRepositoryContract $portalNodeRepository;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    public function __construct(
        PortalNodeRepositoryContract $portalNodeRepository,
        StorageKeyGeneratorContract $storageKeyGenerator
    ) {
        parent::__construct();
        $this->portalNodeRepository = $portalNodeRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
    }

    protected function configure(): void
    {
        $this->addArgument('portal-node-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $key = $this->storageKeyGenerator->deserialize((string) $input->getArgument('portal-node-key'));

        if (!$key instanceof PortalNodeKeyInterface) {
            $io->error('The portal-node-key is not a portalNodeKey');

            return 1;
        }

        $this->portalNodeRepository->delete($key);

        $io->success('The portal node was successfully removed.');

        return 0;
    }
}
