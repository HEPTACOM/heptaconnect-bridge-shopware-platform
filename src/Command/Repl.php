<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\StatusReporting\Contract\StatusReportingContextFactoryInterface;
use Heptacom\HeptaConnect\Portal\Base\Builder\Component\StatusReporter;
use Heptacom\HeptaConnect\Portal\Base\Builder\Token\StatusReporterToken;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalNodeContextInterface;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterContract;
use Heptacom\HeptaConnect\Portal\Base\StatusReporting\StatusReporterStack;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\PortalNode\Listing\PortalNodeListResult;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeListActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Psr\Log\NullLogger;
use Psy\Configuration;
use Psy\Shell;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Repl extends Command
{
    public static ?PortalNodeContextInterface $context = null;

    protected static $defaultName = 'heptaconnect:repl';

    public function __construct(
        private string $projectDir,
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private PortalNodeListActionInterface $portalNodeListAction,
        private StatusReportingContextFactoryInterface $statusReportingContextFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('portal-node', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!\class_exists(Shell::class)) {
            $io->error('You need to add "psy/psysh" as a composer dependency');

            return Command::FAILURE;
        }

        $portalNodeId = (string) $input->getOption('portal-node');

        if (!$portalNodeId) {
            $portalNodeKeys = array_map(
                function (PortalNodeListResult $portalNodeListResult) {
                    return $portalNodeListResult->getPortalNodeKey()->withAlias();
                },
                \iterable_to_array($this->portalNodeListAction->list())
            );

            $portalNodeIds = \array_map([$this->storageKeyGenerator, 'serialize'], $portalNodeKeys);
            $portalNodeId = $io->choice('Choose a portal node', $portalNodeIds);
        }

        $portalNodeKey = $this->storageKeyGenerator->deserialize($portalNodeId);

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            $io->error('Not a valid portal-node-key: ' . $portalNodeId);

            return Command::INVALID;
        }

        $context = $this->statusReportingContextFactory->factory($portalNodeKey);
        $statusReporter = $this->getStatusReporter($portalNodeKey);

        (new StatusReporterStack([$statusReporter], new NullLogger()))->next($context);

        return 0;
    }

    private function getStatusReporter(PortalNodeKeyInterface $portalNodeKey): StatusReporterContract
    {
        $portalNodeId = $this->storageKeyGenerator->serialize($portalNodeKey->withoutAlias());
        /** @var string $portalNodeId */
        $portalNodeId = \preg_replace('/[^a-zA-Z0-9]/', '_', $portalNodeId);

        $commands = $this->getApplication()->all('heptaconnect');

        $statusReporterToken = new StatusReporterToken('repl');

        $projectDir = $this->projectDir;

        $statusReporterToken->setRun(static function (
            PortalNodeContextInterface $context
        ) use ($portalNodeId, $commands, $projectDir): void {
            self::registerGlobalFunctions($context);

            $config = new Configuration();
            $config->setHistoryFile($projectDir . '/var/log/repl.' . $portalNodeId . '.log');

            $shell = new Shell($config);
            $shell->addCommands($commands);

            $shell->run();
        });

        return new StatusReporter($statusReporterToken);
    }

    private static function registerGlobalFunctions(PortalNodeContextInterface $context): void
    {
        self::$context = $context;

        $code = <<<'PHP'

use Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Repl as Storage;
use Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalNodeContextInterface;

function context(): PortalNodeContextInterface {
    return Storage::$context;
}

function config(): array {
    return Storage::$context->getConfig() ?? [];
}

function service(string $serviceId) {
    return Storage::$context->getContainer()->get($serviceId);
}

function portalNodeKey() {
    return Storage::$context->getPortalNodeKey();
}

PHP;

        eval($code);
    }
}
