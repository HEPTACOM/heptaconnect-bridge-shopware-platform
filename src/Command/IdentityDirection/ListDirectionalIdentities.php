<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityDirection;

use Heptacom\HeptaConnect\Storage\Base\Action\IdentityDirection\Overview\IdentityDirectionOverviewCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityDirection\IdentityDirectionOverviewActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListDirectionalIdentities extends Command
{
    protected static $defaultName = 'heptaconnect:identity-direction:list';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private IdentityDirectionOverviewActionInterface $identityDirectionOverviewAction;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        IdentityDirectionOverviewActionInterface $identityDirectionOverviewAction
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->identityDirectionOverviewAction = $identityDirectionOverviewAction;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $identities = [];

        $criteria = new IdentityDirectionOverviewCriteria();
        $criteria->setSort([
            IdentityDirectionOverviewCriteria::FIELD_ENTITY_TYPE => IdentityDirectionOverviewCriteria::SORT_ASC,
            IdentityDirectionOverviewCriteria::FIELD_TARGET_PORTAL_NODE => IdentityDirectionOverviewCriteria::SORT_ASC,
            IdentityDirectionOverviewCriteria::FIELD_TARGET_EXTERNAL_ID => IdentityDirectionOverviewCriteria::SORT_ASC,
            IdentityDirectionOverviewCriteria::FIELD_CREATED => IdentityDirectionOverviewCriteria::SORT_DESC,
        ]);

        foreach ($this->identityDirectionOverviewAction->overview($criteria) as $identityDirection) {
            $identities[] = [
                'id' => $this->storageKeyGenerator->serialize($identityDirection->getRouteKey()),
                'type' => $identityDirection->getEntityType(),
                'targetPortalNode' => $this->storageKeyGenerator->serialize($identityDirection->getTargetPortalNodeKey()->withAlias()),
                'targetExternalId' => $identityDirection->getTargetExternalId(),
                'sourcePortalNode' => $this->storageKeyGenerator->serialize($identityDirection->getSourcePortalNodeKey()->withAlias()),
                'sourceExternalId' => $identityDirection->getSourceExternalId(),
            ];
        }

        if (\count($identities) === 0) {
            $io->note('There are no identity directions.');

            return 0;
        }

        $io->table(\array_keys(\current($identities)), $identities);

        return 0;
    }
}
