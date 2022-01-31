<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router;

use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\RouteKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\RouteKeyCollection;
use Heptacom\HeptaConnect\Storage\Base\Action\Route\Delete\RouteDeleteCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteDeleteActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveRoute extends Command
{
    protected static $defaultName = 'heptaconnect:router:remove-route';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private RouteDeleteActionInterface $routeDeleteAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        RouteDeleteActionInterface $routeDeleteAction
    ) {
        parent::__construct();

        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->routeDeleteAction = $routeDeleteAction;
    }

    protected function configure(): void
    {
        $this->addArgument('route-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $key = $this->storageKeyGenerator->deserialize((string) $input->getArgument('route-key'));

        if (!$key instanceof RouteKeyInterface) {
            $io->error('The route-key is not a routeKey');

            return 1;
        }

        $this->routeDeleteAction->delete(new RouteDeleteCriteria(new RouteKeyCollection([$key])));

        $io->success('The route was successfully removed.');

        return 0;
    }
}