<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityRedirect;

use Heptacom\HeptaConnect\Storage\Base\Action\IdentityRedirect\Delete\IdentityRedirectDeleteCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityRedirect\IdentityRedirectDeleteActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\IdentityRedirectKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeySerializerContract;
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
        private readonly StorageKeySerializerContract $storageKeySerializer,
        private readonly IdentityRedirectDeleteActionInterface $redirectDeleteAction
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('identity-redirect-key', InputArgument::REQUIRED);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $key = $this->storageKeySerializer->deserialize((string) $input->getArgument('identity-redirect-key'));

        if (!$key instanceof IdentityRedirectKeyInterface) {
            $io->error('The identity-redirect-key is not a IdentityRedirectKey');

            return 1;
        }

        $this->redirectDeleteAction->delete(new IdentityRedirectDeleteCriteria(new IdentityRedirectKeyCollection([$key])));

        $io->success('The identity redirect was successfully removed.');

        return 0;
    }
}
