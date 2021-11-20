<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterContract;
use Heptacom\HeptaConnect\Portal\Base\Emission\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerContract;
use Heptacom\HeptaConnect\Portal\Base\Exploration\ExplorerCollection;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\ReceiverCollection;
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

            /** @var ExplorerCollection $explorers */
            $explorers = $container->get(ExplorerCollection::class);
            /** @var ExplorerCollection $explorerDecorators */
            $explorerDecorators = $container->get(ExplorerCollection::class . '.decorator');
            $explorers->push($explorerDecorators);

            /** @var EmitterCollection $emitters */
            $emitters = $container->get(EmitterCollection::class);
            /** @var EmitterCollection $emitterDecorators */
            $emitterDecorators = $container->get(EmitterCollection::class . '.decorator');
            $emitters->push($emitterDecorators);

            /** @var ReceiverCollection $receivers */
            $receivers = $container->get(ReceiverCollection::class);
            /** @var ReceiverCollection $receiverDecorators */
            $receiverDecorators = $container->get(ReceiverCollection::class . '.decorator');
            $receivers->push($receiverDecorators);

            /** @var ExplorerContract $explorer */
            foreach ($explorers as $explorer) {
                $types[$explorer->supports()] = true;
            }

            /** @var EmitterContract $emitter */
            foreach ($emitters as $emitter) {
                $types[$emitter->supports()] = true;
            }

            /** @var ReceiverContract $receiver */
            foreach ($receivers as $receiver) {
                $types[$receiver->supports()] = true;
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
