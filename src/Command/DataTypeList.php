<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterInterface;
use Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerInterface;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalContract;
use Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DataTypeList extends Command
{
    protected static $defaultName = 'heptaconnect:data-type:list';

    private ComposerPortalLoader $portalLoader;

    public function __construct(ComposerPortalLoader $portalLoader)
    {
        parent::__construct();
        $this->portalLoader = $portalLoader;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $types = [];

        /** @var PortalContract $portal */
        foreach ($this->portalLoader->getPortals() as $portal) {
            /** @var ExplorerInterface $explorer */
            foreach ($portal->getExplorers() as $explorer) {
                $types[$explorer->supports()] = true;
            }

            /** @var EmitterInterface $emitter */
            foreach ($portal->getEmitters() as $emitter) {
                /** @var string $support */
                foreach ($emitter->supports() as $support) {
                    $types[$support] = true;
                }
            }

            /** @var ReceiverInterface $receiver */
            foreach ($portal->getReceivers() as $receiver) {
                /** @var string $support */
                foreach ($receiver->supports() as $support) {
                    $types[$support] = true;
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
