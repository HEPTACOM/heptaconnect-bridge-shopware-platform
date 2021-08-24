<?php
declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Jobs;

use Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader;
use Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract;

class CleanupFinished extends Command
{
    protected static $defaultName = 'heptaconnect:jobs:cleanup-finished';

    private JobRepositoryContract $jobRepository;

    public function __construct(
        JobRepositoryContract $jobRepository
    ) {
        parent::__construct();
        $this->jobRepository = $jobRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->jobRepository->cleanup();
        return COMMAND::SUCCESS;
    }
}
