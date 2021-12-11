<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Core\Portal\FlowComponentRegistry;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Storage\Base\PreviewPortalNodeKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DataTypeList extends Command
{
    protected static $defaultName = 'heptaconnect:data-type:list';

    private ComposerPortalLoader $portalLoader;

    private PortalStackServiceContainerFactory $portalStackServiceContainerFactory;

    public function __construct(
        ComposerPortalLoader $portalLoader,
        PortalStackServiceContainerFactory $portalStackServiceContainerFactory
    ) {
        parent::__construct();
        $this->portalLoader = $portalLoader;
        $this->portalStackServiceContainerFactory = $portalStackServiceContainerFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $types = [];

        /** @var PortalContract $portal */
        foreach ($this->portalLoader->getPortals() as $portal) {
            $container = $this->portalStackServiceContainerFactory->create(new PreviewPortalNodeKey(\get_class($portal)));
            /** @var FlowComponentRegistry $flowComponentRegistry */
            $flowComponentRegistry = $container->get(FlowComponentRegistry::class);

            foreach ($flowComponentRegistry->getOrderedSources() as $source) {
                foreach ($flowComponentRegistry->getExplorers($source) as $explorer) {
                    $types[$explorer->supports()] = true;
                }

                foreach ($flowComponentRegistry->getEmitters($source) as $emitter) {
                    $types[$emitter->supports()] = true;
                }

                foreach ($flowComponentRegistry->getReceivers($source) as $receiver) {
                    $types[$receiver->supports()] = true;
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
