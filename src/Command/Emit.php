<?php

declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command;

use Heptacom\HeptaConnect\Core\Job\Contract\EmissionHandlerInterface;
use Heptacom\HeptaConnect\Core\Job\JobData;
use Heptacom\HeptaConnect\Core\Job\JobDataCollection;
use Heptacom\HeptaConnect\Core\Job\Type\Emission;
use Heptacom\HeptaConnect\Dataset\Base\AttachmentCollection;
use Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract;
use Heptacom\HeptaConnect\Dataset\Base\DatasetEntityCollection;
use Heptacom\HeptaConnect\Dataset\Base\DependencyCollection;
use Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentStruct;
use Heptacom\HeptaConnect\Portal\Base\StorageKey\Contract\PortalNodeKeyInterface;
use Heptacom\HeptaConnect\Storage\Base\Action\Identity\Map\IdentityMapPayload;
use Heptacom\HeptaConnect\Storage\Base\Action\Job\Create\JobCreatePayload;
use Heptacom\HeptaConnect\Storage\Base\Action\Job\Create\JobCreatePayloads;
use Heptacom\HeptaConnect\Storage\Base\Action\Job\Get\JobGetCriteria;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityMapActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobCreateActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobGetActionInterface;
use Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract;
use Heptacom\HeptaConnect\Storage\Base\JobKeyCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Emit extends Command
{
    protected static $defaultName = 'heptaconnect:emit';

    private StorageKeyGeneratorContract $storageKeyGenerator;

    private IdentityMapActionInterface $identityMapAction;

    private JobCreateActionInterface $jobCreateAction;

    private JobGetActionInterface $jobGetAction;

    private EmissionHandlerInterface $emissionHandler;

    public function __construct(
        StorageKeyGeneratorContract $storageKeyGenerator,
        IdentityMapActionInterface $identityMapAction,
        JobCreateActionInterface $jobCreateAction,
        JobGetActionInterface $jobGetAction,
        EmissionHandlerInterface $emissionHandler
    ) {
        parent::__construct();
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->identityMapAction = $identityMapAction;
        $this->jobCreateAction = $jobCreateAction;
        $this->jobGetAction = $jobGetAction;
        $this->emissionHandler = $emissionHandler;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('portal-node-key', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::REQUIRED)
            ->addArgument('external-ids', InputArgument::REQUIRED | InputArgument::IS_ARRAY)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $inputPortalNodeKey = (string) $input->getArgument('portal-node-key');
        $portalNodeKey = $this->storageKeyGenerator->deserialize($inputPortalNodeKey);

        if (!$portalNodeKey instanceof PortalNodeKeyInterface) {
            $io->error('The provided portal-node-key is not valid: ' . $inputPortalNodeKey);

            return Command::INVALID;
        }

        $inputType = (string) $input->getArgument('type');
        $type = \trim($inputType, '\'"');

        if (!\is_a($type, DatasetEntityContract::class, true)) {
            $io->error('The provided type is not a DatasetEntityContract: ' . $inputType);

            return Command::INVALID;
        }

        $inputExternalIds = (array) $input->getArgument('external-ids');
        $externalIds = \array_map(fn (string $externalId) => \trim($externalId), $inputExternalIds);

        $this->identityMapAction->map(
            new IdentityMapPayload($portalNodeKey, $this->factorizeEntities($type, $externalIds))
        );

        $jobCreatePayloads = new JobCreatePayloads();

        foreach ($externalIds as $externalId) {
            $jobCreatePayloads->push([
                new JobCreatePayload(
                    Emission::class,
                    new MappingComponentStruct($portalNodeKey, $type, $externalId),
                    null,
                ),
            ]);
        }

        $jobCreateResults = $this->jobCreateAction->create($jobCreatePayloads);

        $jobGetResults = $this->jobGetAction->get(new JobGetCriteria(
            new JobKeyCollection($jobCreateResults->column('getJobKey'))
        ));

        $jobs = new JobDataCollection();

        foreach ($jobGetResults as $jobGetResult) {
            $jobs->push([
                new JobData(
                    $jobGetResult->getMappingComponent(),
                    $jobGetResult->getPayload(),
                    $jobGetResult->getJobKey()
                ),
            ]);
        }

        $this->emissionHandler->triggerEmission($jobs);

        return Command::SUCCESS;
    }

    /**
     * @param class-string<DatasetEntityContract> $entityType
     * @param array<string> $externalIds
     */
    private function factorizeEntities(string $entityType, array $externalIds): DatasetEntityCollection
    {
        $result = new DatasetEntityCollection();
        $entityFactory = new \ReflectionClass($entityType);

        foreach ($externalIds as $externalId) {
            /** @var DatasetEntityContract $entity */
            $entity = $entityFactory->newInstanceWithoutConstructor();
            \Closure::bind(function (DatasetEntityContract $entity): void {
                $entity->attachments = new AttachmentCollection();
                $entity->dependencies = new DependencyCollection();
            }, null, $entity)($entity);
            $entity->setPrimaryKey($externalId);

            $result->push([$entity]);
        }

        return $result;
    }
}
