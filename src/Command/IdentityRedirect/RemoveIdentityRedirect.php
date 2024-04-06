<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityRedirect;

use Heptacom\HeptaConnect\Storage\Base\Action\IdentityRedirect\Delete\IdentityRedirectDeleteCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityRedirect\IdentityRedirectDeleteActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\IdentityRedirectKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\IdentityRedirectKeyCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'heptaconnect:identity-redirect:remove')]
class RemoveIdentityRedirect extends Command
{
    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private IdentityRedirectDeleteActionInterface $identityRedirectDeleteAction
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('identity-redirect-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $key = $this->storageKeyGenerator->deserialize((string) $input->getArgument('identity-redirect-key'));

        if (!$key instanceof IdentityRedirectKeyInterface) {
            $io->error('The identity-redirect-key is not a IdentityRedirectKey');

            return 1;
        }

        $this->identityRedirectDeleteAction->delete(new IdentityRedirectDeleteCriteria(new IdentityRedirectKeyCollection([$key])));

        $io->success('The identity redirect was successfully removed.');

        return 0;
    }
}
