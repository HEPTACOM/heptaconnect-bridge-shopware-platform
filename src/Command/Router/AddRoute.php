<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\RouteCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\RouteCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Contract\RouteCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Contract\RouteFindByTargetsAndTypeActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\RouteFindByTargetsAndTypeCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\RouteFindByTargetsAndTypeResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Enum\RouteCapability;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddRoute extends Command
{
    protected static $defaultName = 'heptaconnect:router:add-route';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private RouteFindByTargetsAndTypeActionInterface $routeFindByTargetsAndTypeAction;

    private RouteCreateActionInterface $routeCreateAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        RouteFindByTargetsAndTypeActionInterface $routeFindByTargetsAndTypeAction,
        RouteCreateActionInterface $routeCreateAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->routeFindByTargetsAndTypeAction = $routeFindByTargetsAndTypeAction;
        $this->routeCreateAction = $routeCreateAction;
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

        if (!\is_a($type, DatasetEntityContract::class, true)) {
            $io->error('The specified type does not implement the DatasetEntityContract.');

            return 1;
        }

        $existingRoute = $this->routeFindByTargetsAndTypeAction->find(new RouteFindByTargetsAndTypeCriteria($source, $target, $type));

        if ($existingRoute instanceof RouteFindByTargetsAndTypeResult) {
            $io->error('There is already this route configured');

            return 2;
        }

        $results = [];

        $payload = new RouteCreatePayload($source, $target, $type, [RouteCapability::RECEPTION]);
        foreach ($this->routeCreateAction->create(new RouteCreatePayloads([$payload])) as $result) {
            $results[] = [
                'id' => $this->storageKeyGenerator->serialize($result->getRoute()),
            ];
        }

        $io->table(['id' => 'id'], $results);

        return 0;
    }
}
