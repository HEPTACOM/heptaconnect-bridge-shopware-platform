<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityDirection;

use Heptacom\HeptaConnect\Storage\Base\Action\IdentityDirection\Delete\IdentityDirectionDeleteCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityDirection\IdentityDirectionDeleteActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\IdentityDirectionKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\IdentityDirectionKeyCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveDirectionalIdentity extends Command
{
    protected static $defaultName = 'heptaconnect:identity-direction:remove';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private IdentityDirectionDeleteActionInterface $identityDirectionDeleteAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        IdentityDirectionDeleteActionInterface $identityDirectionDeleteAction
    ) {
        parent::__construct();

        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->identityDirectionDeleteAction = $identityDirectionDeleteAction;
    }

    protected function configure(): void
    {
        $this->addArgument('identity-direction-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $key = $this->storageKeyGenerator->deserialize((string) $input->getArgument('identity-direction-key'));

        if (!$key instanceof IdentityDirectionKeyInterface) {
            $io->error('The identity-direction-key is not a IdentityDirectionKey');

            return 1;
        }

        $this->identityDirectionDeleteAction->delete(new IdentityDirectionDeleteCriteria(new IdentityDirectionKeyCollection([$key])));

        $io->success('The identity direction was successfully removed.');

        return 0;
    }
}
