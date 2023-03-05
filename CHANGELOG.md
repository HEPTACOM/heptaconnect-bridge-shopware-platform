# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to a variation of [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
The version numbers are structured like `GENERATION.MAJOR.MINOR.PATCH`:

* `GENERATION` version when concepts and APIs are abandoned, but brand and project name stay the same,
* `MAJOR` version when you make incompatible API changes and provide an upgrade path,
* `MINOR` version when you add functionality in a backwards compatible manner, and
* `PATCH` version when you make backwards compatible bug fixes.

## [Unreleased]

### Added

- Add composer dependency `heptacom/heptaconnect-ui-admin-symfony: ^0.9` to provide CLI commands
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Storage\PrimaryKeyToEntityHydrator`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Emission\EmitterStackProcessor` as `Heptacom\HeptaConnect\Core\Emission\Contract\EmitterStackProcessorInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Emission\EmissionFlowEmittersFactory` as `Heptacom\HeptaConnect\Core\Emission\Contract\EmissionFlowEmittersFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Exploration\ExplorerStackProcessor` as `Heptacom\HeptaConnect\Core\Exploration\Contract\ExplorerStackProcessorInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Exploration\DirectEmissionFlowEmittersFactory` as `Heptacom\HeptaConnect\Core\Exploration\Contract\DirectEmissionFlowEmittersFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Exploration\ExplorationFlowExplorersFactory` as `Heptacom\HeptaConnect\Core\Exploration\Contract\ExplorationFlowExplorersFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Reception\ReceptionFlowReceiversFactory` as `Heptacom\HeptaConnect\Core\Reception\Contract\ReceptionFlowReceiversFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Web\Http\HttpHandleFlowHttpHandlersFactory` as `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandleFlowHttpHandlersFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Job\Transition\EmittedEntitiesToReceiveJobsConverter` as `Heptacom\HeptaConnect\Core\Job\Transition\Contract\EmittedEntitiesToJobsConverterInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Job\Transition\ExploredPrimaryKeysToEmissionJobsConverter` as `Heptacom\HeptaConnect\Core\Job\Transition\Contract\ExploredPrimaryKeysToJobsConverterInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Portal\PackageQueryMatcher` as `Heptacom\HeptaConnect\Core\Portal\Contract\PackageQueryMatcherInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Configuration\PortalNodeConfigurationProcessorService` as `Heptacom\HeptaConnect\Core\Configuration\Contract\PortalNodeConfigurationProcessorServiceInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Audit\AuditableDataSerializer` as `Heptacom\HeptaConnect\Core\Ui\Admin\Audit\Contract\AuditTrailDataSerializerInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Audit\AuditTrailFactory` as `Heptacom\HeptaConnect\Core\Ui\Admin\Audit\Contract\AuditTrailFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Support\PortalNodeAliasResolver` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Support\PortalNodeAliasResolverInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Support\PortalNodeExistenceSeparator` as `Heptacom\HeptaConnect\Core\Ui\Admin\Support\Contract\PortalNodeExistenceSeparatorInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Support\StorageKeyAccessor` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Support\StorageKeyAccessorInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\Context\UiActionContextFactory` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\UiActionContextFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\JobRunUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\Job\JobRunUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\JobScheduleUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\Job\JobScheduleUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalEntityListUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\Portal\PortalEntityListUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeConfigurationGetUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeConfigurationGetUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeEntityListUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeEntityListUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeExtensionBrowseUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeExtensionBrowseUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeStatusReportUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeStatusReportUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeExtensionActivateUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeExtensionActivateUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeExtensionDeactivateUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeExtensionDeactivateUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeAddUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeAddUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeRemoveUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeRemoveUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\PortalNodeStorageGetUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\PortalNodeStorageGetUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\RouteAddUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\PortalNode\RouteAddUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\RouteAddUiDefault` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\Route\RouteAddUiDefaultProviderInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\RouteBrowseUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\Route\RouteBrowseUiActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Ui\Admin\Action\RouteRemoveUi` as `Heptacom\HeptaConnect\Ui\Admin\Base\Contract\Action\Route\RouteRemoveUiActionInterface`
- Add service container parameter `heptacom_heptaconnect.emission_flow.job_dispatch_batch_size` to influence batch size parameter in `\Heptacom\HeptaConnect\Core\Emission\EmissionFlowEmittersFactory`
- Add service container parameter `heptacom_heptaconnect.direct_emission_flow.identity_batch_size` to influence batch size parameter in `\Heptacom\HeptaConnect\Core\Exploration\DirectEmissionFlowEmittersFactory`
- Add service container parameter `heptacom_heptaconnect.exploration.job_batch_size` to influence batch size parameter in `\Heptacom\HeptaConnect\Core\Exploration\ExplorationFlowExplorersFactory`
- Add service container parameter `heptacom_heptaconnect.exploration.identity_batch_size` to influence batch size parameter in `\Heptacom\HeptaConnect\Core\Exploration\ExplorationFlowExplorersFactory`
- Add service container parameter `heptacom_heptaconnect.exploration.direct_emission_batch_size` to influence batch size parameter in `\Heptacom\HeptaConnect\Core\Exploration\ExplorationFlowExplorersFactory`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\UiAuditTrail\UiAuditTrailBeginActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\UiAuditTrail\UiAuditTrailEndActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\UiAuditTrail\UiAuditTrailLogErrorActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\UiAuditTrail\UiAuditTrailLogOutputActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Psr\Http\Message\StreamFactoryInterface.heptaconnect` factorized by `\Http\Discovery\Psr17FactoryDiscovery::findStreamFactory`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Web\Http\RequestDeserializer` as `Heptacom\HeptaConnect\Core\Web\Http\Contract\RequestDeserializerInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Web\Http\RequestSerializer` as `Heptacom\HeptaConnect\Core\Web\Http\Contract\RequestSerializerInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Portal\Storage\PortalNodeStorageItemPacker` as `Heptacom\HeptaConnect\Core\Portal\Storage\Contract\PortalNodeStorageItemPackerInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Portal\Storage\PortalNodeStorageItemUnpacker` as `Heptacom\HeptaConnect\Core\Portal\Storage\Contract\PortalNodeStorageItemUnpackerInterface`
- The base-url can now be controlled via an environment variable `APP_URL`. If set, the environment variable will take precedence over the value from the database.

### Changed

- Switch parameter in `Heptacom\HeptaConnect\Core\Configuration\PortalNodeConfigurationInstructionProcessor` from `Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract` to `Heptacom\HeptaConnect\Core\Portal\Contract\PackageQueryMatcherInterface`
- Switch parameter in `Heptacom\HeptaConnect\Portal\Base\Flow\DirectEmission\DirectEmissionFlowContract` from `Heptacom\HeptaConnect\Core\Emission\Contract\EmissionActorInterface` to `Heptacom\HeptaConnect\Core\Exploration\Contract\DirectEmissionFlowEmittersFactoryInterface` and `Heptacom\HeptaConnect\Core\Emission\Contract\EmitterStackProcessorInterface`
- Switch parameter in `Heptacom\HeptaConnect\Core\Emission\EmitService` from `Heptacom\HeptaConnect\Core\Emission\Contract\EmissionActorInterface` to `Heptacom\HeptaConnect\Core\Emission\Contract\EmissionFlowEmittersFactoryInterface` and `Heptacom\HeptaConnect\Core\Emission\Contract\EmitterStackProcessorInterface`
- Switch parameter in `Heptacom\HeptaConnect\Core\Exploration\ExploreService` from `Heptacom\HeptaConnect\Core\Exploration\Contract\ExplorationActorInterface` to `Heptacom\HeptaConnect\Core\Exploration\Contract\ExplorationFlowExplorersFactoryInterface` and `Heptacom\HeptaConnect\Core\Exploration\Contract\ExplorerStackProcessorInterface`
- Switch parameter in `Heptacom\HeptaConnect\Core\Portal\PortalStorageFactory` from `Heptacom\HeptaConnect\Portal\Base\Serialization\Contract\NormalizationRegistryContract` to `Heptacom\HeptaConnect\Core\Portal\Storage\Contract\PortalNodeStorageItemPackerInterface` and `Heptacom\HeptaConnect\Core\Portal\Storage\Contract\PortalNodeStorageItemUnpackerInterface`
- Rename service `Heptacom\HeptaConnect\Core\Reception\Contract\ReceptionActorInterface` to `Heptacom\HeptaConnect\Core\Reception\Contract\ReceiverStackProcessorInterface` to match class and interface rename
- Rename service `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlingActorInterface` to `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerStackProcessorInterface` to match class and interface rename
- Add parameter of `Heptacom\HeptaConnect\Core\Reception\Contract\ReceptionFlowReceiversFactoryInterface` to service definition `Heptacom\HeptaConnect\Core\Reception\ReceiveService`
- Add parameter of `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandleFlowHttpHandlersFactoryInterface` to service definition `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandleServiceInterface`
- Remove command `heptaconnect:portal-node:extensions:list` from `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension\ListExtensions` in favour of `portal:node:extension:browse` shipped with composer dependency `heptacom/heptaconnect-ui-admin-symfony`
- Remove command `heptaconnect:portal-node:status:report` from `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\ReportPortalNode` in favour of `portal:node:status:report` and `portal:node:healthy` shipped with composer dependency `heptacom/heptaconnect-ui-admin-symfony`
- Remove command `heptaconnect:portal-node:extensions:activate` from `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension\ActivateExtension` in favour of `portal:node:extension:activate` shipped with composer dependency `heptacom/heptaconnect-ui-admin-symfony`
- Remove command `heptaconnect:portal-node:extensions:deactivate` from `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension\DeactivateExtension` in favour of `portal:node:extension:deactivate` shipped with composer dependency `heptacom/heptaconnect-ui-admin-symfony`
- Remove command `heptaconnect:portal-node:add` from `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\AddPortalNode` in favour of `portal:node:add` shipped with composer dependency `heptacom/heptaconnect-ui-admin-symfony`
- Remove command `heptaconnect:router:add-route` from `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\AddRoute` in favour of `route:add` shipped with composer dependency `heptacom/heptaconnect-ui-admin-symfony`
- Remove command `heptaconnect:router:remove-route` from `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\RemoveRoute` in favour of `route:remove` shipped with composer dependency `heptacom/heptaconnect-ui-admin-symfony`
- Remove `heptacom_heptaconnect.logger` dependency from `\Heptacom\HeptaConnect\Core\Reception\PostProcessing\MarkAsFailedPostProcessor`
- Switch parameter in `Heptacom\HeptaConnect\Core\Storage\Contract\RequestStorageContract` from `Heptacom\HeptaConnect\Core\Storage\Normalizer\Psr7RequestNormalizer` and `Heptacom\HeptaConnect\Core\Storage\Normalizer\Psr7RequestDenormalizer` to `Heptacom\HeptaConnect\Core\Web\Http\Contract\RequestSerializerInterface` and `Heptacom\HeptaConnect\Core\Web\Http\Contract\RequestDeserializerInterface`
- Extract dependency `tagged: heptaconnect_core.portal_node_configuration.processor` from `Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface` into own service `Heptacom\HeptaConnect\Core\Configuration\Contract\PortalNodeConfigurationProcessorServiceInterface`

### Deprecated

- Service definition `Heptacom\HeptaConnect\Core\Storage\PrimaryKeyToEntityHydrator` is deprecated as the class itself is also deprecated and will be removed in future major version

### Removed

- Remove support for `php: 7.4` as it will not receive any updates anymore, it is unlikely to be used. By raising the minimum PHP version we also make use of features introduced by PHP 8.0, which mainly have no effect on public API
- Remove service definition `Heptacom\HeptaConnect\Core\Emission\Contract\EmissionActorInterface` as `Heptacom\HeptaConnect\Core\Emission\Contract\EmitterStackProcessorInterface` is preferred
- Remove service definition `Heptacom\HeptaConnect\Core\Exploration\ExplorationActor` as `Heptacom\HeptaConnect\Core\Exploration\Contract\ExplorerStackProcessorInterface` is preferred

### Fixed

### Security

## [0.9.3.0] - 2023-03-04

### Added

- Add option `time-limit` to command `heptaconnect:job:cleanup-finished` to limit the time the command is running measured in seconds
- Add service definition `Heptacom\HeptaConnect\Core\Web\Http\Formatter\Support\Contract\HeaderUtilityInterface` for class `\Heptacom\HeptaConnect\Core\Web\Http\Formatter\Support\HeaderUtility`
- Add service definition `Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\Psr7MessageCurlShellFormatterContract` for class `\Heptacom\HeptaConnect\Core\Web\Http\Formatter\Psr7MessageCurlShellFormatter`
- Add service definition `Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\Psr7MessageRawHttpFormatterContract` for class `\Heptacom\HeptaConnect\Core\Web\Http\Formatter\Psr7MessageRawHttpFormatter`
- Add service definition `Heptacom\HeptaConnect\Core\Web\Http\Dump\Contract\ServerRequestCycleDumpCheckerInterface` for class `\Heptacom\HeptaConnect\Core\Web\Http\Dump\SampleRateServerRequestCycleDumpChecker`
- Add service alias `Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\Psr7MessageFormatterContract` to set `Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\Psr7MessageRawHttpFormatterContract` as default implementation
- Implement `\Heptacom\HeptaConnect\Core\Bridge\File\HttpHandlerDumpPathProviderInterface` in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File\HttpHandlerDumpPathProvider`
- Add service definition `Heptacom\HeptaConnect\Core\Web\Http\Dump\Contract\ServerRequestCycleDumperInterface` for class `\Heptacom\HeptaConnect\Core\Web\Http\Dump\ServerRequestCycleDumper`
- Add dependency `Heptacom\HeptaConnect\Core\Web\Http\Dump\Contract\ServerRequestCycleDumpCheckerInterface` and `Heptacom\HeptaConnect\Core\Web\Http\Dump\Contract\ServerRequestCycleDumperInterface` to service `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandleServiceInterface`
- Add dependency `Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\Psr7MessageCurlShellFormatterContract` and `Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\Psr7MessageRawHttpFormatterContract` to service `Heptacom\HeptaConnect\Core\Portal\Contract\PortalStackServiceContainerBuilderInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityRedirect\IdentityRedirectDeleteActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityRedirect\IdentityRedirectCreateActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityRedirect\IdentityRedirectOverviewActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add command `heptaconnect:identity-redirect:add` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityRedirect\AddIdentityRedirect` to add an identity redirect
- Add command `heptaconnect:identity-redirect:remove` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityRedirect\RemoveIdentityRedirect` to remove an identity redirect
- Add command `heptaconnect:identity-redirect:list` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\IdentityRedirect\ListIdentityRedirects` to list identity redirects
- Add identity redirect into evaluation of `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings`

### Changed

- Use count of deleted jobs as progress indicator in command `heptaconnect:job:cleanup-finished`
- Delete jobs, that have not been finished at the start of the command `heptaconnect:job:cleanup-finished`, but finished during the command run
- Remove Symfony, connection, proxy and transfer related header from requests handled in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerController`
- Raise composer dependency constraint for `heptacom/heptaconnect-core`, `heptacom/heptaconnect-dataset-base`, `heptacom/heptaconnect-portal-base` and `heptacom/heptaconnect-storage-base` from `^0.9.3` to `^0.9.4`
- Raise composer dependency constraint for `heptacom/heptaconnect-storage-shopware-dal` from `^0.9` to `^0.9.1`

### Fixed

- Ensure missing query parameters in the request's URI passed on to the HTTP handler in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerController`
- Interpret `entity-type` option in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings` as filter criteria for identities
- Show an empty result if first search did not find a mapping node to search for its siblings `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings`

## [0.9.2.0] - 2022-11-26

### Added

- Add composer dependency `kor3k/flysystem-stream-wrapper: ^1.0.11` to register flysystem filesystems to a stream wrapper
- Add service definition for implementation `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File\PortalNodeFilesystemStreamProtocolProvider` described by `\Heptacom\HeptaConnect\Core\Bridge\File\PortalNodeFilesystemStreamProtocolProviderInterface` to provide stream wrapper protocol and register flysystem filesystems for portal nodes
- Add service definition `Heptacom\HeptaConnect\Core\Portal\File\Filesystem\Contract\FilesystemFactoryInterface` for class `\Heptacom\HeptaConnect\Core\Portal\File\Filesystem\FilesystemFactory`
- Add dependency `Heptacom\HeptaConnect\Core\Portal\File\Filesystem\Contract\FilesystemFactoryInterface` to service `Heptacom\HeptaConnect\Core\Portal\Contract\PortalStackServiceContainerBuilderInterface`
- Add command `heptaconnect:emit` to emit one or more entities
- Add composer suggestion `psy/psysh` for an interactive read–eval–print loop in the scope of a portal-node
- Add command `heptaconnect:repl` for an interactive read–eval–print loop in the scope of a portal-node

### Fixed

- Change base filesystem for portal nodes in `Heptacom\HeptaConnect\Core\Storage\Filesystem\FilesystemFactory` from the Shopware bundle provided private filesystem to a custom prefixed filesystem based on the Shopware instance private filesystem to keep the same default directory but to support adapter access on the file system 

## [0.9.1.1] - 2022-10-03

### Added

- Show progress-bar in command `heptaconnect:job:cleanup-finished`

### Fixed

- Remove service `Shopware\Core\Framework\MessageQueue\Monitoring\MonitoringBusDecorator` from container as it has been renamed from `Shopware\Core\Framework\MessageQueue\MonitoringBusDecorator`.
- Fix command `heptaconnect:portal-node:status:list-topics` when there are no topics

## [0.9.1.0] - 2022-07-19

### Added

- Add service `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\FrameworkX\XAppFactoryInterface` to initialize a framework-x app. Requires optional dependency `clue/framework-x`.

## [0.9.0.3] - 2022-06-08

### Fixed

- Fix command `heptaconnect:portal-node:status:list-topics` by using the `Heptacom\HeptaConnect\Core\Portal\FlowComponentRegistry` from the portal container
- Fix command `heptaconnect:job:cleanup-finished` by using only the job-keys of the `Heptacom\HeptaConnect\Storage\Base\Action\Job\Listing\JobListFinishedResult` objects

## [0.9.0.2] - 2022-04-27

### Fixed

- Create lock tables `heptaconnect_core_reception_lock` and `heptaconnect_portal_node_resource_lock` manually as `Symfony\Component\Lock\Store\PdoStore` does not create them automatically for MySQL driver

## [0.9.0.1] - 2022-04-19

### Fixed

- Use different locking implementation to follow Shopware master-slave database setup warning in `\Shopware\Core\Profiling\Doctrine\DebugStack`

## [0.9.0.0] - 2022-04-02

### Added

- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobGetActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobCreateActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobListFinishedActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobDeleteActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobStartActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobFinishActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobFailActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobScheduleActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeCreateActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeDeleteActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeListActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeGetActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeOverviewActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add command `heptaconnect:portal-node:extensions:activate` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension\ActivateExtension` to activate a portal extension on a portal node
- Add command `heptaconnect:portal-node:extensions:deactivate` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension\DeactivateExtension` to deactivate a portal extension on a portal node
- Add command `heptaconnect:portal-node:extensions:list` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Extension\ListExtensions` to list activity state of portal extensions on a portal node
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\PortalExtensionFindActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\PortalExtensionActivateActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\PortalExtensionDeactivateActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add option `--bidirectional` and its functionality to `heptaconnect:router:add-route` defined in class `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\AddRoute` to automate creation of the route back
- Add service definition `\Heptacom\HeptaConnect\Core\Component\Logger\FlowComponentCodeOriginFinderLogger` for decorating `heptacom_heptaconnect.logger` to stringify flow component into human readable code origins in log messages
- Add command `heptaconnect:portal-node:list-flow-components` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\ListFlowComponentsForPortalNode` to list all flow components for a given entity type, job type (by base class) and portal node
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Web\Http\HttpHandlerCodeOriginFinder` as `Heptacom\HeptaConnect\Portal\Base\Web\Http\Contract\HttpHandlerCodeOriginFinderInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Emission\EmitterCodeOriginFinder` as `Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitterCodeOriginFinderInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Exploration\ExplorerCodeOriginFinder` as `Heptacom\HeptaConnect\Portal\Base\Exploration\Contract\ExplorerCodeOriginFinderInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Reception\ReceiverCodeOriginFinder` as `Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiverCodeOriginFinderInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\StatusReporting\StatusReporterCodeOriginFinder` as `Heptacom\HeptaConnect\Portal\Base\StatusReporting\Contract\StatusReporterCodeOriginFinderInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Bridge\StorageFacade` as `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface` that is used to create all storage based service
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeConfiguration\PortalNodeConfigurationGetActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeConfiguration\PortalNodeConfigurationSetActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Core\Component\Logger\ExceptionCodeLogger` for decorating `heptacom_heptaconnect.logger` to add exception codes in log messages
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityMapActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityPersistActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityReflectActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteDeleteActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageClearActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageDeleteActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageGetActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageListActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageSetActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityError\IdentityErrorCreateActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add command `heptaconnect:router:remove-route` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\RemoveRoute` to remove a route by id seen on `heptaconnect:router:list-routes`
- Implement `\Heptacom\HeptaConnect\Core\Bridge\File\FileContentsUrlProviderInterface` in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File\FileContentsUrlProvider`
- Implement `\Heptacom\HeptaConnect\Core\Bridge\File\FileRequestUrlProviderInterface` in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File\FileRequestUrlProvider`
- Add HTTP route `heptaconnect.file.request` in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File\FileReferenceController::request` to send a stored request of a file reference and pass the response through to the client
- Add HTTP route `heptaconnect.file.contents` in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File\FileReferenceController::contents` to read a normalized stream of a file reference and respond with its contents and an arbitrary mime type
- Add service definition `Heptacom\HeptaConnect\Portal\Base\File\FileReferenceResolverContract`
- Add service definition `Heptacom\HeptaConnect\Core\Storage\Contract\RequestStorageContract`
- Add service definition `Heptacom\HeptaConnect\Core\Storage\Normalizer\Psr7RequestDenormalizer`
- Add service definition `Heptacom\HeptaConnect\Core\Storage\Normalizer\Psr7RequestNormalizer`
- Add service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\RequestContextHelper`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\FileReference\FileReferenceGetRequestActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Action\FileReference\FileReferencePersistRequestActionInterface` provided by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add service definition `Heptacom\HeptaConnect\Core\Bridge\File\FileContentsUrlProviderInterface`
- Add service definition `Heptacom\HeptaConnect\Core\Bridge\File\FileRequestUrlProviderInterface`
- Add service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\File\FileReferenceController`
- Add class `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\RequestContextHelper` to scope a request context to a base URL
- Add service definition `Heptacom\HeptaConnect\Core\Configuration\PortalNodeConfigurationInstructionProcessor` with dependency onto `heptacom_heptaconnect.logger`, `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\AliasStorageKeyGenerator`, `Heptacom\HeptaConnect\Core\Portal\PortalRegistry` and all tagged services by tag `heptaconnect_core.portal_node_configuration.instruction_file_loader` tagged as `heptaconnect_core.portal_node_configuration.processor`
- Add service definition `Heptacom\HeptaConnect\Core\Configuration\PortalNodeConfigurationCacheProcessor` with dependency onto `cache.system` and `Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract` tagged as `heptaconnect_core.portal_node_configuration.processor`
- Add service and definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\AliasValidator` to validate portal node aliases
- Add command `heptaconnect:portal-node:alias:find` in service definition `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias\Find` to resolve alias to a portal node key
- Add command `heptaconnect:portal-node:alias:get` in service definition `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias\Get` to get an alias by a portal node key

### Changed

- Change dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\CleanupFinished` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobListFinishedActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobDeleteActionInterface`
- Change dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\Run` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` and `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobPayloadRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobGetActionInterface`
- Change dependency in `Heptacom\HeptaConnect\Core\Flow\MessageQueueFlow\MessageHandler` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` and `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobPayloadRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobGetActionInterface`
- Change dependency in `Heptacom\HeptaConnect\Core\Job\Contract\JobDispatcherContract` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobStartActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobFinishActionInterface`
- Change dependency in `Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobStartActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobFinishActionInterface`
- Change dependency in `Heptacom\HeptaConnect\Core\Job\Contract\ExplorationHandlerInterface` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobStartActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Job\JobFinishActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeListActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\AddPortalNode` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeCreateActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\ListPortalNodes` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeOverviewActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\RemovePortalNode` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeDeleteActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler\ListHandlers` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeListActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Core\Portal\PortalRegistry` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeGetActionInterface`
- Remove argument `Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent` from service definition `Heptacom\HeptaConnect\Core\Portal\Contract\PortalStackServiceContainerBuilderInterface`
- Add dependency `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalExtension\PortalExtensionFindActionInterface` to the service definition `Heptacom\HeptaConnect\Core\Portal\PortalRegistry`
- Change service id from `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Listing\ReceptionRouteListActionInterface` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\ReceptionRouteListActionInterface`
- Change service id from `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Overview\RouteOverviewActionInterface` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteOverviewActionInterface`
- Change service id from `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Find\RouteFindActionInterface` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteFindActionInterface`
- Change service id from `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Get\RouteGetActionInterface` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteGetActionInterface`
- Change service id from `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Get\RouteCreateActionInterface` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\RouteCreateActionInterface`
- Change service id from `Heptacom\HeptaConnect\Storage\Base\Contract\Action\RouteCapability\Overview\RouteCapabilityOverviewActionInterface` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\RouteCapability\RouteCapabilityOverviewActionInterface`
- Change service id from `Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\Find\WebHttpHandlerConfigurationFindActionInterface` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\WebHttpHandlerConfigurationFindActionInterface`
- Change service id from `Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\Set\WebHttpHandlerConfigurationSetActionInterface` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\WebHttpHandlerConfigurationSetActionInterface`
- Change behavior of command `heptaconnect:portal-node:config:get` to throw an exception when the output cannot be converted to JSON
- Change output of command `heptaconnect:portal-node:config:get` to not escape slashes in JSON
- Change output of command `heptaconnect:portal-node:status:report` to not escape slashes in JSON
- Change behavior of command `heptaconnect:http-handler:get-configuration` to throw an exception when the output cannot be converted to JSON
- Change output of command `heptaconnect:http-handler:get-configuration` to not escape slashes in JSON
- Change service id from `Heptacom\HeptaConnect\Core\Configuration\ConfigurationService` to `Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface` to prioritize service interface as id
- Switch dependency in `Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface` from `Heptacom\HeptaConnect\Storage\ShopwareDal\ConfigurationStorage` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeConfiguration\PortalNodeConfigurationGetActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeConfiguration\PortalNodeConfigurationSetActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface` from `Heptacom\HeptaConnect\Storage\Base\Contract\EntityMapperContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityMapActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface` from `Heptacom\HeptaConnect\Storage\Base\Contract\EntityReflectorContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityReflectActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Core\Exploration\ExplorationActor` from `Heptacom\HeptaConnect\Core\Mapping\MappingService` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityMapActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodes` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings` from `Heptacom\HeptaConnect\Core\Portal\ComposerPortalLoader`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract`, `Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNode\PortalNodeListActionInterface` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\MergeMappingNodes` from `Heptacom\HeptaConnect\Core\Mapping\Contract\MappingServiceInterface` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Core\Reception\PostProcessing\SaveMappingsPostProcessor` from `Heptacom\HeptaConnect\Storage\Base\MappingPersister\Contract\MappingPersisterContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityPersistActionInterface`
- Switch implementation of `Heptacom\HeptaConnect\Core\Router\Router.lock_factory` from `Symfony\Component\Lock\Store\FlockStore` to `Symfony\Component\Lock\Store\PdoStore` to support horizontally scaled setups out of the box
- Switch implementation of `Heptacom\HeptaConnect\Storage\ShopwareDal\ResourceLockStorage.lock_factory` from `Symfony\Component\Lock\Store\FlockStore` to `Symfony\Component\Lock\Store\PdoStore` to support horizontally scaled setups out of the box
- Change id and implementation of `Heptacom\HeptaConnect\Storage\ShopwareDal\ResourceLockStorage` to `Heptacom\HeptaConnect\Core\Parallelization\Contract\ResourceLockStorageContract` implemented by `Heptacom\HeptaConnect\Core\Parallelization\ResourceLockStorage`
- Switch dependency in `Heptacom\HeptaConnect\Core\Portal\PortalStorageFactory` from `Heptacom\HeptaConnect\Storage\ShopwareDal\PortalStorage` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageClearActionInterface`, `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageDeleteActionInterface`, `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageListActionInterface`, `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageSetActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageGetActionInterface`
- Switch dependency in `Heptacom\HeptaConnect\Core\Emission\EmitContextFactory` from `Heptacom\HeptaConnect\Storage\Core\Mapping\Contract\MappingServiceInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract` to `Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityError\IdentityErrorCreateActionInterface` as previous services are removed
- Switch dependency in `Heptacom\HeptaConnect\Core\Reception\PostProcessing\MarkAsFailedPostProcessor` from `Heptacom\HeptaConnect\Storage\Core\Mapping\Contract\MappingServiceInterface` to `\Heptacom\HeptaConnect\Storage\Base\Contract\Action\IdentityError\IdentityErrorCreateActionInterface` as previous service is removed
- Remove argument `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract` from service definition `Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface`
- Rename route `heptaconnect.http.handler` to `api.heptaconnect.http.handler`
- Change usage of deprecated `Heptacom\HeptaConnect\Portal\Base\Publication\Contract\PublisherInterface::publish` to `Heptacom\HeptaConnect\Portal\Base\Publication\Contract\PublisherInterface::publishBatch` in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Explore::execute` and `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping\PublisherDecorator::flushBuffer`
- Add final modifier to `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping\PublisherDecorator`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass\RemoveBusMonitoring`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\DependencyInjection\CompilerPass\RemoveEntityCache`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Profiling\Profiler`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Profiling\ProfilerFactory`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\CommandsPrintLogsSubscriber`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerUrlProvider` and `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerUrlProviderFactory` to ensure correct usage of implementation. Decoration by their interfaces or base classes is still possible
- Add argument `Heptacom\HeptaConnect\Core\Storage\Contract\RequestStorageContract` to service definition `Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerBuilder`
- Add call to `\Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerBuilder::setFileReferenceResolver` with argument `Heptacom\HeptaConnect\Portal\Base\File\FileReferenceResolverContract` to service definition `Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerBuilder`
- Add argument `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\RequestContextHelper` to service definition `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerUrlProviderFactoryInterface`
- Switch dependency in `Heptacom\HeptaConnect\Core\Configuration\Contract\ConfigurationServiceInterface` from `cache.system`, `Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKeyGenerator` and `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\AliasStorageKeyGenerator` to all tagged services by tag `heptaconnect_core.portal_node_configuration.processor`
- Change service id from `Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKeyGenerator` to `Heptacom\HeptaConnect\Storage\Base\Contract\StorageKeyGeneratorContract` and provide by `Heptacom\HeptaConnect\Storage\Base\Bridge\Contract\StorageFacadeInterface`
- Add argument `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\AliasValidator` to service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\AddPortalNode`
- Replace `heptaconnect:support:alias:list` in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Support\Alias\ListAliases` with new command `heptaconnect:portal-node:alias:overview` in service definition `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias\Overview` to list all portal node keys and their aliases
- Replace `heptaconnect:support:alias:reset` in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Support\Alias\Reset` with new command `heptaconnect:portal-node:alias:reset` in service definition `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias\Reset` to remove an alias from a portal node key
- Replace `heptaconnect:support:alias:set` in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Support\Alias\Set` with new command `heptaconnect:portal-node:alias:set` in service definition `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\Alias\Set` to set an alias to a portal node key
- Change implementation of `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\CommandsPrintLogsSubscriber` to support decoration of the logger. Replace argument `\Psr\Log\LoggerInterface` with `\Monolog\Handler\StreamHandler`.

### Removed

- Remove command `heptaconnect:job:cleanup-payloads` and service `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\CleanupPayloads` in favour of storages removing unused payloads with their jobs
- Remove service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract`
- Remove service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobPayloadRepositoryContract`
- Remove service definition `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\PortalNodeRepository` and its alias `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract`
- Remove unused service `Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent`
- Remove service definition `Heptacom\HeptaConnect\Storage\ShopwareDal\ConfigurationStorage` in favour of `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeConfiguration\PortalNodeConfigurationGetActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeConfiguration\PortalNodeConfigurationSetActionInterface`
- Remove command `heptaconnect:cronjob:ensure-queue` and service `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Cronjob\EnsureQueue` as the feature of cronjobs in its current implementation is removed
- Remove command `heptaconnect:cronjob:queue` and service `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Cronjob\Queue` as the feature of cronjobs in its current implementation is removed
- Remove class and its service `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob\CronjobRunHandler` and `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob\CronjobRunMessageHandler` as the feature of cronjobs in its current implementation is removed
- Remove class `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Messaging\Cronjob\CronjobRunMessage` as the feature of cronjobs in its current implementation is removed
- Remove service `\Heptacom\HeptaConnect\Core\Cronjob\CronjobService`, `Heptacom\HeptaConnect\Core\Cronjob\CronjobContextFactory`, `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\CronjobRepository`, `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\CronjobRunRepository`, `Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Cronjob\CronjobDefinition` and `Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Cronjob\CronjobRunDefinition` as the feature of cronjobs in its current implementation is removed
- Remove service `Heptacom\HeptaConnect\Storage\Base\Contract\EntityMapperContract` in favour of storage action `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityMapActionInterface`
- Remove service `Heptacom\HeptaConnect\Storage\Base\MappingPersister\Contract\MappingPersisterContract` in favour of storage action `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityPersistActionInterface`
- Remove service `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\MappingRepository` and `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract` in favour of storage action `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityPersistActionInterface`, `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityMapActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Identity\IdentityOverviewActionInterface`
- Remove service `Heptacom\HeptaConnect\Core\Mapping\MappingService`
- Remove service `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\MappingExceptionRepository`
- Remove service `Heptacom\HeptaConnect\Storage\ShopwareDal\EntityReflector`
- Remove service `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract`
- Remove service `Heptacom\HeptaConnect\Storage\ShopwareDal\DalAccess`
- Remove service `Heptacom\HeptaConnect\Storage\Base\MappingPersister\Contract\MappingPersisterContract`
- Remove service `Heptacom\HeptaConnect\Storage\ShopwareDal\EntityTypeAccessor`
- Remove composer dependency `dragonmantank/cron-expression`
- Integrate service definition `Heptacom\HeptaConnect\Core\Router\Router.lock_store` as anonymous service parameter directly into the definition of `Heptacom\HeptaConnect\Core\Router\Router.lock_factory`
- Integrate service definition `Heptacom\HeptaConnect\Storage\ShopwareDal\ResourceLockStorage.lock_store` as anonymous service parameter directly into the definition of `Heptacom\HeptaConnect\Core\Parallelization\Contract\ResourceLockStorageContract.lock_factory`
- Remove support for `symfony/lock: >=4 <5.2` so the `Symfony\Component\Lock\Store\PdoStore` will automatically create the lock tables
- Remove support for `shopware/core: 6.3.*`
- Remove service definition `Heptacom\HeptaConnect\Storage\ShopwareDal\PortalStorage` in favour of storage actions `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageClearActionInterface`, `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageDeleteActionInterface`, `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageListActionInterface`, `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageSetActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\PortalNodeStorage\PortalNodeStorageGetActionInterface`
- Remove service definitions for classes `\Heptacom\HeptaConnect\Storage\ShopwareDal\ContextFactory`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\EntityType\EntityTypeDefinition`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Mapping\MappingDefinition`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Mapping\MappingErrorMessageDefinition`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Mapping\MappingNodeDefinition`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\PortalNode\PortalNodeDefinition`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\PortalNode\PortalNodeStorageDefinition`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Job\JobDefinition`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Job\JobPayloadDefinition`, `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Job\JobTypeDefinition` and `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Route\RouteDefinition` as well as their generated services `heptaconnect_entity_type.repository`, `heptaconnect_mapping.repository`, `heptaconnect_mapping_error_message.repository`, `heptaconnect_mapping_node.repository`, `heptaconnect_portal_node.repository`, `heptaconnect_portal_node_storage.repository`, `heptaconnect_job.repository`, `heptaconnect_job_payload.repository`, `heptaconnect_job_type.repository` and `heptaconnect_route.repository` as DAL usage is removed in `heptacom/heptaconnect-storage-shopware-dal`
- Remove deprecated `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping\PublisherDecorator::publish` inherited by `Heptacom\HeptaConnect\Portal\Base\Publication\Contract\PublisherInterface::publish`
- Remove support for `doctrine/dbal: >=2.1 <2.11`
- Remove implementation `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Support\AliasStorageKeyGenerator` as portal node alias support is integrated into `heptacom/heptaconnect-core`
- Remove Shopware entity classes `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasCollection`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasDefinition` and `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Content\KeyAlias\KeyAliasEntity` for table `heptaconnect_bridge_key_alias`

### Fixed

- Change hardcoded `prod` environment in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\AbstractIntegration::getLifecycleContainer` to using the current one
- Add tag `console.command` to service definition of `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\CleanupFinished` to make the command available
- Add tag `console.command` to service definition of `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\Run` to make the command available

## [0.8.1] - 2022-03-04

### Fixed

- Add missing service tag for command `heptaconnect:job:run`
- Add missing service tag for command `heptaconnect:job:cleanup-finished`

## [0.8.0] - 2021-11-22

### Added

- Add command `heptaconnect:job:run` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\Run` to run jobs by key from the commandline
- Add command `heptaconnect:job:cleanup-finished` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\CleanupFinished` to remove finished jobs from the storage
- Add command `heptaconnect:job:cleanup-payloads` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Job\CleanupPayloads` to remove unused job data from the storage
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Storage\Contract\StreamPathContract`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Support\Query\QueryIterator`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\Route\ReceptionRouteList` as `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Listing\ReceptionRouteListActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\Route\RouteOverview` as `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Overview\RouteOverviewActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\Route\RouteFind` as `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Find\RouteFindActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\Route\RouteGet` as `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Get\RouteGetActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\Route\RouteCreate` as `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Create\RouteCreateActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\RouteCapability\RouteCapabilityOverview` as `Heptacom\HeptaConnect\Storage\Base\Contract\Action\RouteCapability\Overview\RouteCapabilityOverviewActionInterface`
- Add command `heptaconnect:router:list-capabilities` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\ListRouteCapabilities` to list available route capabilities
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\RouteCapabilityAccessor`
- Add column for route primary key and route capabilities to the output of `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\ListRoutes` named `heptaconnect:router:list-routes`
- Add command `heptaconnect:http-handler:set-configuration` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler\Set` to set http handler configuration
- Add command `heptaconnect:http-handler:get-configuration` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler\Get` to read http handler configuration
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\WebHttpHandlerAccessor`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\WebHttpHandlerPathAccessor`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\WebHttpHandlerPathIdResolver`
- Add command `heptaconnect:config:base-url:get` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Config\GetBaseUrlCommand` to get base url for http handlers
- Add command `heptaconnect:config:base-url:set` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Config\SetBaseUrlCommand` to set base url for http handlers
- Add service definition `Psr\Http\Message\ResponseFactoryInterface.heptaconnect` factorized by `\Http\Discovery\Psr17FactoryDiscovery::findResponseFactory`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\WebHttpHandlerConfiguration\WebHttpHandlerConfigurationFind` as `Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\Find\WebHttpHandlerConfigurationFindActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Action\WebHttpHandlerConfiguration\WebHttpHandlerConfigurationSet` as `Heptacom\HeptaConnect\Storage\Base\Contract\Action\WebHttpHandlerConfiguration\Set\WebHttpHandlerConfigurationSetActionInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Web\Http\HttpHandleContextFactory` as `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandleContextFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Web\Http\HttpHandlerStackBuilderFactory` as `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerStackBuilderFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Web\Http\HttpHandleService` as `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandleServiceInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Core\Web\Http\HttpHandlingActor` as `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlingActorInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerUrlProviderFactory` as `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerUrlProviderFactoryInterface`
- Add service definition based upon class `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerController` and http handling implementation
- Add service definition based upon class `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHostProviderContract` and implementation to simplify base URL configuration for integrators
- Add command `heptaconnect:http-handler:list-handlers` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Web\HttpHandler\ListHandlers` to list available HTTP handlers
- Add command `heptaconnect:portal-node:status:list-topics` in service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\PortalNode\ListStatusReportTopics` to list all supported status topics

### Changed

- Change service definition id from `Heptacom\HeptaConnect\Storage\ShopwareDal\DatasetEntityTypeAccessor` to `Heptacom\HeptaConnect\Storage\ShopwareDal\EntityTypeAccessor` and set new id for definitions of services `Heptacom\HeptaConnect\Storage\Base\Contract\EntityMapperContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract`
- Change parameter name of `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping\PublisherDecorator::publish` from `$datasetEntityClassName` to `$entityType`
- Change name of service `heptaconnect_dataset_entity_type.repository.patched` to `heptaconnect_entity_type.repository.patched`
- Change `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\DatasetEntityType\DatasetEntityTypeDefinition` to `\Heptacom\HeptaConnect\Storage\ShopwareDal\Content\EntityType\EntityTypeDefinition`
- Change argument and variable names in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodes::configure`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodes::execute` and `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings::configure`, `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings::execute`
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
- Change dependency in `Heptacom\HeptaConnect\Core\Emission\EmissionActor` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Listing\ReceptionRouteListActionInterface`
- Change service definition id based upon class `Heptacom\HeptaConnect\Core\Emission\EmissionActor` to match its interface `Heptacom\HeptaConnect\Core\Emission\Contract\EmissionActorInterface`
- Change dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\ListRoutes` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` and `Heptacom\HeptaConnect\Core\Portal\PortalStackServiceContainerFactory` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Overview\RouteOverviewActionInterface`
- Add dependency `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Find\RouteFindActionInterface` in the service definition `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\AddRoute`
- Change dependency in `Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Get\RouteGetActionInterface`
- Change dependency in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\AddRoute` from `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract` and `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` into `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Create\RouteCreateActionInterface` and `Heptacom\HeptaConnect\Storage\Base\Contract\Action\Route\Get\RouteGetActionInterface`
- Change output from `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\AddRoute` named `heptaconnect:router:add-route` to show all route information like `heptaconnect:router:list-routes`
- Add dependency `Heptacom\HeptaConnect\Core\Web\Http\Contract\HttpHandlerUrlProviderFactoryInterface` in the service definition `Heptacom\HeptaConnect\Core\Portal\Contract\PortalStackServiceContainerBuilderInterface`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\EntityReflector` from `heptaconnect_mapping.repository.patched` to `heptaconnect_mapping.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\PortalStorage` from `heptaconnect_portal_node_storage.repository.patched` to `heptaconnect_portal_node_storage.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\CronjobRepository` from `heptaconnect_cronjob.repository.patched` to `heptaconnect_cronjob.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\CronjobRunRepository` from `heptaconnect_cronjob.repository.patched` to `heptaconnect_cronjob.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\CronjobRunRepository` from `heptaconnect_cronjob_run.repository.patched` to `heptaconnect_cronjob_run.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\MappingExceptionRepository` from `heptaconnect_mapping_error_message.repository.patched` to `heptaconnect_mapping_error_message.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract` from `heptaconnect_mapping.repository.patched` to `heptaconnect_mapping.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\PortalNodeRepositoryContract` from `heptaconnect_portal_node.repository.patched` to `heptaconnect_portal_node.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKeyGenerator` from `heptaconnect_mapping_node.repository.patched` to `heptaconnect_mapping_node.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\StorageKeyGenerator` from `heptaconnect_mapping.repository.patched` to `heptaconnect_mapping.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\Base\MappingPersister\Contract\MappingPersisterContract` from `heptaconnect_mapping.repository.patched` to `heptaconnect_mapping.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` from `heptaconnect_job.repository.patched` to `heptaconnect_job.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract` from `heptaconnect_job_type.repository.patched` to `heptaconnect_job_type.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobPayloadRepositoryContract` from `heptaconnect_job_payload.repository.patched` to `heptaconnect_job_payload.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract` from `heptaconnect_mapping_node.repository.patched` to `heptaconnect_mapping_node.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract` from `heptaconnect_mapping.repository.patched` to `heptaconnect_mapping.repository`
- Change dependency in `Heptacom\HeptaConnect\Storage\ShopwareDal\EntityTypeAccessor` from `heptaconnect_entity_type.repository.patched` to `heptaconnect_entity_type.repository`
- Move route annotation registration from `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Webhook\WebhookController` to `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerController`
- Change command name from `heptaconnect:portal-node:status` to `heptaconnect:portal-node:status:report`
- Change option from `--dataset-entity-class` (`-d`) to `--entity-type` (`-t`) in `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings` (`heptaconnect:mapping-node:siblings-list`)
- Add dependency `heptacom_heptaconnect.logger` in the service definition `Heptacom\HeptaConnect\Core\Reception\PostProcessing\SaveMappingsPostProcessor`
- Add dependency `heptacom_heptaconnect.logger` in the service definition `Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface`

### Removed

- Remove service definition `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract`
- Remove service definition `Heptacom\HeptaConnect\Core\Webhook\WebhookContextFactory`
- Remove service definition `Heptacom\HeptaConnect\Core\Webhook\WebhookService`
- Remove service definition `Heptacom\HeptaConnect\Storage\ShopwareDal\Content\Webhook\WebhookDefinition`
- Remove service definition `heptaconnect_webhook.repository.patched`
- Remove service definition `Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\WebhookRepository`
- Remove class and its service definition `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Webhook\WebhookController` in favour of `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerController`
- Remove class and its service definition `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Webhook\UrlProvider` in favour of `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Web\Http\HttpHandlerUrlProviderFactory`
- Remove patched entity repository services `heptaconnect_mapping_node.repository.patched`, `heptaconnect_mapping.repository.patched`, `heptaconnect_job.repository.patched`, `heptaconnect_job_type.repository.patched`, `heptaconnect_job_payload.repository.patched`, `heptaconnect_entity_type.repository.patched`, `heptaconnect_route.repository.patched`, `heptaconnect_portal_node_storage.repository.patched`, `heptaconnect_portal_node.repository.patched`, `heptaconnect_mapping_error_message.repository.patched`, `heptaconnect_cronjob_run.repository.patched` and `heptaconnect_cronjob.repository.patched` 
- Remove support for `shopware/core: 6.2.*` and therefore the compatibility patching process with `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\PatchProvider\EntityRepository` and `\Heptacom\HeptaConnect\Bridge\ShopwarePlatform\PatchProvider\EntityRepositoryPatch587`

### Fixed

- Change behaviour of command `heptaconnect:router:list-routes` in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\Router\ListRoutes` to also list created routes that do not have supported flow components (anymore)

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
