<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\PortalRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PortalList extends Command
{
    protected static $defaultName = 'heptaconnect:portal:list';

    private PortalRegistry $portalRegistry;

    public function __construct(PortalRegistry $portalRegistry)
    {
        parent::__construct(null);
        $this->portalRegistry = $portalRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->section('HEPTAConnect portal list');

        $portals = [];

        foreach ($this->portalRegistry->getPortals() as $portal) {
            $portals[] = [
                'class' => \get_class($portal),
            ];
        }

        $portalExtensions = [];

        foreach ($this->portalRegistry->getPortalExtensions() as $portalExtension) {
            $portalExtensions[] = [
                'class' => \get_class($portalExtension),
            ];
        }

        $io->section('Portals');
        $io->table(['class'], $portals);
        $io->section('Portal extensions');
        $io->table(['class'], $portalExtensions);

        return 0;
    }
}
