<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\RouteKeyCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Create\RouteCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Create\RouteCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Create\RouteCreateResult;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Find\RouteFindCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Find\RouteFindResult;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Get\RouteGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Get\RouteGetResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Create\RouteCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Find\RouteFindActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Get\RouteGetActionInterface;
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

    private RouteFindActionInterface $routeFindAction;

    private RouteCreateActionInterface $routeCreateAction;

    private RouteGetActionInterface $routeGetAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        RouteFindActionInterface $routeFindAction,
        RouteCreateActionInterface $routeCreateAction,
        RouteGetActionInterface $routeGetAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->routeFindAction = $routeFindAction;
        $this->routeCreateAction = $routeCreateAction;
        $this->routeGetAction = $routeGetAction;
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

        $existingRoute = $this->routeFindAction->find(new RouteFindCriteria($source, $target, $type));

        if ($existingRoute instanceof RouteFindResult) {
            $io->error('There is already this route configured');

            return 2;
        }

        $payload = new RouteCreatePayload($source, $target, $type, [RouteCapability::RECEPTION]);
        $ids = new RouteGetCriteria(new RouteKeyCollection());

        /** @var \Heptacom\HeptaConnect\Storage\Base\Action\Route\Create\RouteCreateResult $result */
        foreach ($this->routeCreateAction->create(new RouteCreatePayloads([$payload])) as $result) {
            $ids->getRouteKeys()->push([$result->getRouteKey()]);
        }

        $results = [];

        /** @var RouteGetResult $route */
        foreach ($this->routeGetAction->get($ids) as $route) {
            $capabilities = $route->getCapabilities();
            \sort($capabilities);

            $results[] = [
                'id' => $this->storageKeyGenerator->serialize($route->getRouteKey()),
                'type' => $route->getEntityType(),
                'source' => $this->storageKeyGenerator->serialize($route->getSourcePortalNodeKey()),
                'target' => $this->storageKeyGenerator->serialize($route->getTargetPortalNodeKey()),
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
