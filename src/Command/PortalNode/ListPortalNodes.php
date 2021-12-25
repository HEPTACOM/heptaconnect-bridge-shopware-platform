<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Listing\PortalNodeListActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListPortalNodes extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:list';

    private PortalNodeRepositoryContract $portalNodeRepository;

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalNodeListActionInterface $portalNodeListAction;

    public function __construct(
        PortalNodeRepositoryContract $portalNodeRepository,
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalNodeListActionInterface $portalNodeListAction
    ) {
        parent::__construct();
        $this->portalNodeRepository = $portalNodeRepository;
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeListAction = $portalNodeListAction;
    }

    protected function configure()
    {
        $this->addArgument('portal-class', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $portalClass = (string) $input->getArgument('portal-class');

        if ($portalClass !== '') {
            if (!\is_a($portalClass, PortalContract::class, true)) {
                $io->error('The provided portal class does not implement the PortalContract.');

                return 1;
            }
        } else {
            $portalClass = null;
        }

        $rows = [];
        $iterator = \is_null($portalClass) ?
            $this->portalNodeListAction->list() :
            $this->portalNodeRepository->listByClass($portalClass);

        foreach ($iterator as $portalNodeKey) {
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($portalNodeKey),
                'portal-class' => $this->portalNodeRepository->read($portalNodeKey),
            ];
        }

        if (empty($rows)) {
            $io->note('There are no portal nodes of the selected portal.');

            return 0;
        }

        $io->table(\array_keys(\current($rows)), $rows);

        return 0;
    }
}
