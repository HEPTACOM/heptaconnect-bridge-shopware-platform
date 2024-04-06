<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Bridge\Portal\PortalLoaderInterface;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Portal\PortalType;
use Heptacom\HeptaConnect\Storage\Base\PreviewPortalNodeKey;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:data-type:list')]
class DataTypeList extends Command
{
    public function __construct(
        private PortalLoaderInterface $portalLoader,
        private PortalStackServiceContainerFactory $portalStackServiceContainerFactory
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $types = [];

        /** @var PortalContract $portal */
        foreach ($this->portalLoader->getPortals() as $portal) {
            $flowComponentRegistry = $this->portalStackServiceContainerFactory
                ->create(new PreviewPortalNodeKey(new PortalType($portal::class)))
                ->getFlowComponentRegistry();

            foreach ($flowComponentRegistry->getOrderedSources() as $source) {
                foreach ($flowComponentRegistry->getExplorers($source) as $explorer) {
                    $types[(string) $explorer->getSupportedEntityType()] = true;
                }

                foreach ($flowComponentRegistry->getEmitters($source) as $emitter) {
                    $types[(string) $emitter->getSupportedEntityType()] = true;
                }

                foreach ($flowComponentRegistry->getReceivers($source) as $receiver) {
                    $types[(string) $receiver->getSupportedEntityType()] = true;
                }
            }
        }

        if (\count($types) === 0) {
            $io->note('There are no supported data types.');

            return 0;
        }

        $types = \array_map(fn (string $type) => ['data-type' => $type], \array_keys($types));
        $io->table(\array_keys(\current($types)), $types);

        return 0;
    }
}
