<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Overview\PortalNodeOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\Overview\PortalNodeOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListPortalNodes extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:list';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private PortalNodeOverviewActionInterface $portalNodeOverviewAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        PortalNodeOverviewActionInterface $portalNodeOverviewAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->portalNodeOverviewAction = $portalNodeOverviewAction;
    }

    protected function configure(): void
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
        $criteria = new PortalNodeOverviewCriteria();
        $criteria->setSort([PortalNodeOverviewCriteria::FIELD_CREATED => PortalNodeOverviewCriteria::SORT_DESC]);

        if ($portalClass !== null) {
            $criteria->setClassNameFilter([$portalClass]);
        }

        foreach ($this->portalNodeOverviewAction->overview($criteria) as $result) {
            $portalNodeKey = $result->getPortalNodeKey();
            $rows[] = [
                'portal-node-key' => $this->storageKeyGenerator->serialize($portalNodeKey),
                'portal-class' => $result->getPortalClass(),
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
