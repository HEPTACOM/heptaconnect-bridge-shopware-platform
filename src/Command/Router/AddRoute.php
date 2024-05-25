<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Create\RouteCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Create\RouteCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Find\RouteFindCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Find\RouteFindResult;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Get\RouteGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteGetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Enum\RouteCapability;
use Heptacom\HeptaConnect\Storage\Base\RouteKeyCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:router:add-route')]
class AddRoute extends Command
{
    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private RouteFindActionInterface $routeFindAction,
        private RouteCreateActionInterface $routeCreateAction,
        private RouteGetActionInterface $routeGetAction
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED)
            ->addArgument('target', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::REQUIRED)
            ->addOption('bidirectional', null, InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $source = $this->storageKeyGenerator->deserialize((string) $input->getArgument('source'));
        $target = $this->storageKeyGenerator->deserialize((string) $input->getArgument('target'));
        $type = (string) $input->getArgument('type');
        $isBidirectional = (bool) $input->getOption('bidirectional');

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

        $ids = new RouteGetCriteria(new RouteKeyCollection());
        $create = new RouteCreatePayloads();

        $towards = $this->routeFindAction->find(new RouteFindCriteria($source, $target, $type));

        if ($towards instanceof RouteFindResult) {
            $ids->getRouteKeys()->push([$towards->getRouteKey()]);
        } else {
            $create->push([new RouteCreatePayload($source, $target, $type, [RouteCapability::RECEPTION])]);
        }

        if ($isBidirectional && !$source->equals($target)) {
            $back = $this->routeFindAction->find(new RouteFindCriteria($target, $source, $type));

            if ($back instanceof RouteFindResult) {
                $ids->getRouteKeys()->push([$back->getRouteKey()]);
            } else {
                $create->push([new RouteCreatePayload($target, $source, $type, [RouteCapability::RECEPTION])]);
            }
        }

        foreach ($this->routeCreateAction->create($create) as $result) {
            $ids->getRouteKeys()->push([$result->getRouteKey()]);
        }

        $results = [];

        foreach ($this->routeGetAction->get($ids) as $route) {
            $capabilities = $route->getCapabilities();
            \sort($capabilities);

            $results[] = [
                'id' => $this->storageKeyGenerator->serialize($route->getRouteKey()),
                'type' => $route->getEntityType(),
                'source' => $this->storageKeyGenerator->serialize($route->getSourcePortalNodeKey()->withAlias()),
                'target' => $this->storageKeyGenerator->serialize($route->getTargetPortalNodeKey()->withAlias()),
                'capabilities' => \implode(', ', $capabilities),
            ];
        }

        if (\count($results) === 0) {
            $io->note('There are no routes.');

            return 0;
        }

        $io->table(\array_keys(\current($results)), $results);

        return 0;
    }
}
