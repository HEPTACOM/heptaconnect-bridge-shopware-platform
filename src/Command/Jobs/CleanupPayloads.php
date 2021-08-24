<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Jobs;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobPayloadRepositoryContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract;

class CleanupPayloads extends Command
{
    protected static $defaultName = 'heptaconnect:jobs:cleanup-payloads';

    private JobPayloadRepositoryContract $jobPayloadRepository;

    public function __construct(
        JobPayloadRepositoryContract $jobPayloadRepository
    ) {
        parent::__construct();
        $this->jobPayloadRepository = $jobPayloadRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->jobPayloadRepository->cleanup();
        return COMMAND::SUCCESS;
    }
}
