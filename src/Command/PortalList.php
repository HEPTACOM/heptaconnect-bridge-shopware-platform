<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PortalList extends Command
{
    protected static $defaultName = 'heptaconnect:portal:list';

    public function __construct(
        private ComposerPortalLoader $portalLoader
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->section('HEPTAconnect portal list');

        $portals = [];

        /** @var PortalContract $portal */
        foreach ($this->portalLoader->getPortals() as $portal) {
            $portals[] = [
                'class' => $portal::class,
            ];
        }

        $portalExtensions = [];

        foreach ($this->portalLoader->getPortalExtensions() as $portalExtension) {
            $portalExtensions[] = [
                'class' => $portalExtension::class,
            ];
        }

        $io->section('Portals');
        $io->table(['class'], $portals);
        $io->section('Portal extensions');
        $io->table(['class'], $portalExtensions);

        return 0;
    }
}
