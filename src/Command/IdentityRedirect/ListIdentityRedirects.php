<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityRedirect;

use Heptacom\HeptaConnect\Storage\Base\Action\IdentityRedirect\Overview\IdentityRedirectOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityRedirect\IdentityRedirectOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListIdentityRedirects extends Command
{
    protected static $defaultName = 'heptaconnect:identity-redirect:list';

    public function __construct(
        private StorageKeyGeneratorContract $storageKeyGenerator,
        private IdentityRedirectOverviewActionInterface $identityRedirectOverviewAction
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $identities = [];

        $criteria = new IdentityRedirectOverviewCriteria();
        $criteria->setSort([
            IdentityRedirectOverviewCriteria::FIELD_ENTITY_TYPE => IdentityRedirectOverviewCriteria::SORT_ASC,
            IdentityRedirectOverviewCriteria::FIELD_TARGET_PORTAL_NODE => IdentityRedirectOverviewCriteria::SORT_ASC,
            IdentityRedirectOverviewCriteria::FIELD_TARGET_EXTERNAL_ID => IdentityRedirectOverviewCriteria::SORT_ASC,
            IdentityRedirectOverviewCriteria::FIELD_CREATED => IdentityRedirectOverviewCriteria::SORT_DESC,
        ]);

        foreach ($this->identityRedirectOverviewAction->overview($criteria) as $identityRedirect) {
            $identities[] = [
                'id' => $this->storageKeyGenerator->serialize($identityRedirect->getRouteKey()),
                'type' => $identityRedirect->getEntityType(),
                'targetPortalNode' => $this->storageKeyGenerator->serialize($identityRedirect->getTargetPortalNodeKey()->withAlias()),
                'targetExternalId' => $identityRedirect->getTargetExternalId(),
                'sourcePortalNode' => $this->storageKeyGenerator->serialize($identityRedirect->getSourcePortalNodeKey()->withAlias()),
                'sourceExternalId' => $identityRedirect->getSourceExternalId(),
            ];
        }

        if (\count($identities) === 0) {
            $io->note('There are no identity redirects.');

            return 0;
        }

        $io->table(\array_keys(\current($identities)), $identities);

        return 0;
    }
}
