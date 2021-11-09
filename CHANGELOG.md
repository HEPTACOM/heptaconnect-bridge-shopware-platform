# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Add command `heptaconnect:job:run` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\Run` to run jobs by key from the commandline
- Add command `heptaconnect:job:cleanup-finished` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\CleanupFinished` to remove finished jobs from the storage
- Add command `heptaconnect:job:cleanup-payloads` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\CleanupPayloads` to remove unused job data from the storage
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Storage\Contract\StreamPathContract`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Support\Query\QueryIterator`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\ReceptionRouteList` as `Heptacom\HeptaConnect\Storage\Base\Contract\ReceptionRouteListActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\RouteOverview` as `Heptacom\HeptaConnect\Storage\Base\Contract\RouteOverviewActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\RouteFind` as `Heptacom\HeptaConnect\Storage\Base\Contract\RouteFindActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\RouteGet` as `Heptacom\HeptaConnect\Storage\Base\Contract\RouteGetActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\RouteCreate` as `Heptacom\HeptaConnect\Storage\Base\Contract\RouteCreateActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\RouteCapabilityOverview` as `Heptacom\HeptaConnect\Storage\Base\Contract\RouteCapabilityOverviewActionInterface`
- Add command `heptaconnect:router:list-capabilities` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\ListRouteCapabilities` to list available route capabilities
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\RouteCapabilityAccessor`
- Add column for route primary key and route capabilities to the output of `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\ListRoutes` named `heptaconnect:router:list-routes`

### Changed

- Change service definition id from `Heptacom\HeptaConnect\Storage\ShopwareDal\DatasetEntityTypeAccessor` to `Heptacom\HeptaConnect\Storage\ShopwareDal\EntityTypeAccessor` in global refactoring effort and set new id for definitions of services `Heptacom\HeptaConnect\Storage\Base\Contract\EntityMapperContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract`
- Change function call in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping\PublisherDecorator::publishBatch` to use renamed method of `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingComponentStructContract`
- Change a parameter name of `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping\PublisherDecorator::publish` in global refactoring effort
- Change name of service `heptaconnect_dataset_entity_type.repository.patched` to `heptaconnect_entity_type.repository.patched` in global refactoring effort
- Change `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\DatasetEntityType\DatasetEntityTypeDefinition` to `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\EntityType\EntityTypeDefinition` in global refactoring effort
- Change argument and variable names in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodes::configure`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodes::execute` and `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings::configure`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings::execute` in global refactoring effort
- Add dependency `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` to the service definition `Heptacom\HeptaConnect\Core\Job\Handler\EmissionHandler`
- Add dependency `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` to the service definition `Heptacom\HeptaConnect\Core\Job\Handler\ExplorationHandler`
- Add dependency `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` to the service definition `Heptacom\HeptaConnect\Core\Job\Handler\ReceptionHandler`
- Add dependency `cache.system` in the service definition `Heptacom\HeptaConnect\Core\Configuration\ConfigurationService`
- Add service definition `Heptacom\HeptaConnect\Core\Reception\PostProcessing\MarkAsFailedPostProcessor` with dependencies on `Heptacom\HeptaConnect\Core\Mapping\MappingService` and `heptacom_heptaconnect.logger`
- Add service definition `Heptacom\HeptaConnect\Core\Reception\PostProcessing\SaveMappingsPostProcessor` with dependencies on `Heptacom\HeptaConnect\Portal\Base\Support\Contract\DeepObjectIteratorContract` and `Heptacom\HeptaConnect\Storage\Base\MappingPersister\Contract\MappingPersisterContract`
- Add dependency to tagged services of tag `heptaconnect.postprocessor` to service definition `Heptacom\HeptaConnect\Core\Reception\Contract\ReceiveContextFactoryInterface`. The service that are tagged like this are `Heptacom\HeptaConnect\Core\Reception\PostProcessing\MarkAsFailedPostProcessor` and `Heptacom\HeptaConnect\Core\Reception\PostProcessing\SaveMappingsPostProcessor`
- Remove argument `Heptacom\HeptaConnect\Core\Mapping\MappingService` from service definition `Heptacom\HeptaConnect\Core\Reception\Contract\ReceiveContextFactoryInterface` 
- Remove argument `Heptacom\HeptaConnect\Storage\Base\MappingPersister\Contract\MappingPersisterContract` from service definition `Heptacom\HeptaConnect\Core\Reception\ReceptionActor`
- Add dependency `Heptacom\HeptaConnect\Core\Storage\Contract\StreamPathContract` in the service definition `Heptacom\HeptaConnect\Core\Storage\Normalizer\StreamDenormalizer`
- Add dependency `Heptacom\HeptaConnect\Core\Storage\Contract\StreamPathContract` in the service definition `Heptacom\HeptaConnect\Core\Storage\Normalizer\StreamNormalizer`
- Add dependency `heptacom_heptaconnect.logger` in the service definition `Heptacom\HeptaConnect\Core\Storage\Normalizer\StreamNormalizer`
- Change dependency in `Heptacom\HeptaConnect\Core\Emission\EmissionActor` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\ReceptionRouteListActionInterface`
- Change service definition id based upon class `Heptacom\HeptaConnect\Core\Emission\EmissionActor` to match its interface `Heptacom\HeptaConnect\Core\Emission\Contract\EmissionActorInterface`
- Change dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\ListRoutes` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` and `Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory` into `Heptacom\HeptaConnect\Storage\Base\Contract\RouteOverviewActionInterface`
- Add dependency `Heptacom\HeptaConnect\Storage\Base\Contract\RouteFindActionInterface` in the service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\AddRoute`
- Change dependency in `Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\RouteGetActionInterface`
- Change dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\AddRoute` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract` and `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\RouteCreateActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\RouteGetActionInterface`
- Change output from `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\AddRoute` named `heptaconnect:router:add-route` to show all route information like `heptaconnect:router:list-routes`

### Fixed

- Change behaviour of command `heptaconnect:router:list-routes` in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\ListRoutes` to also list created routes that do not have supported flow components (anymore)

### Removed

- Remove service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract`

## [0.7.0] - 2021-09-25

### Added

- Add service definition `Heptacom\HeptaConnect\Storage\Base\MappingPersister\Contract\MappingPersisterContract`

### Changed

- Add dependency `heptacom_heptaconnect.logger` to service definition `\Heptacom\HeptaConnect\Core\Portal\PortalStorageFactory`
- Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Emission\EmitContextFactory` to match its interface `\Heptacom\HeptaConnect\Core\Emission\Contract\EmitContextFactoryInterface`
- Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Job\Handler\EmissionHandler` to match its interface `\Heptacom\HeptaConnect\Core\Job\Contract\EmissionHandlerInterface`
- Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Job\Handler\ExplorationHandler` to match its interface `\Heptacom\HeptaConnect\Core\Job\Contract\ExplorationHandlerInterface`
- Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Job\Handler\ReceptionHandler` to match its interface `\Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface`
- Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Reception\ReceiveContextFactory` to match its interface `\Heptacom\HeptaConnect\Core\Reception\Contract\ReceiveContextFactoryInterface`
- Change service definition id based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\MappingRepository` to match its contract `\Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract`
- Remove argument `Heptacom\HeptaConnect\Core\Mapping\MappingService` from service definition `Heptacom\HeptaConnect\Portal\Base\Flow\DirectEmission\DirectEmissionFlowContract` 
