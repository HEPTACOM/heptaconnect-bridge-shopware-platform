<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode;

use Heptacom\HeptaConnect\Core\StatusReporting\Contract\StatusReportingServiceInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\StorageKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\Exception\UnsupportedStorageKeyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReportPortalNode extends Command
{
    protected static $defaultName = 'heptaconnect:portal-node:status';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private StatusReportingServiceInterface $statusReportingService;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        StatusReportingServiceInterface $statusReportingService
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->statusReportingService = $statusReportingService;
    }

    protected function configure()
    {
        $this->addArgument('portal-id', InputArgument::REQUIRED);
        $this->addArgument('topic', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portalNodeKey = $this->storageKeyGenerator->deserialize((string)$input->getArgument('portal-id'));

            if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
                throw new UnsupportedStorageKeyException(StorageKeyInterface::class);
            }
        } catch (UnsupportedStorageKeyException $exception) {
            $io->error('The portal-id is not a portalNodeKey');

            return 1;
        }

        $topic = (string) $input->getArgument('topic');
        $topic = empty($topic) ? null : $topic;
        $report = $this->statusReportingService->report($portalNodeKey, $topic);

        if (!empty($topic)) {
            $report = $report[$topic] ?? $report;
        }

        $output->writeln(\json_encode($report, JSON_THROW_ON_ERROR));

        return 0;
    }
}
