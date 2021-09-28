# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

* Added dependency `heptacom_heptaconnect.logger` to service definition `\Heptacom\HeptaConnect\Core\Portal\PortalStorageFactory`
* Changed service definition id from class `\Heptacom\HeptaConnect\Core\Emission\EmitContextFactory` to its new interface `\Heptacom\HeptaConnect\Core\Emission\Contract\EmitContextFactoryInterface`
* Changed service definition id from class `\Heptacom\HeptaConnect\Core\Job\Handler\EmissionHandler` to its new interface `\Heptacom\HeptaConnect\Core\Job\Contract\EmissionHandlerInterface`
* Changed service definition id from class `\Heptacom\HeptaConnect\Core\Job\Handler\ExplorationHandler` to its new interface `\Heptacom\HeptaConnect\Core\Job\Contract\ExplorationHandlerInterface`
* Changed service definition id from class `\Heptacom\HeptaConnect\Core\Job\Handler\ReceptionHandler` to its new interface `\Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface`
* Changed service definition id from class `\Heptacom\HeptaConnect\Core\Reception\ReceiveContextFactory` to its new interface `\Heptacom\HeptaConnect\Core\Reception\Contract\ReceiveContextFactoryInterface`
* Changed service definition id from `Heptacom\HeptaConnect\Storage\ShopwareDal\DatasetEntityTypeAccessor` to `Heptacom\HeptaConnect\Storage\ShopwareDal\EntityTypeAccessor` in global refactoring effort and set new id for definitions of services `Heptacom\HeptaConnect\Storage\Base\Contract\EntityMapperContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\JobRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingNodeRepositoryContract`, `Heptacom\HeptaConnect\Storage\Base\Contract\Repository\RouteRepositoryContract`
* Changed function call in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping\PublisherDecorator::publishBatch` to use renamed method of `Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingComponentStructContract`
* Changed a parameter name of `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Core\Mapping\PublisherDecorator::publish` in global refactoring effort
* Changed name of service `heptaconnect_dataset_entity_type.repository.patched` to `heptaconnect_entity_type.repository.patched` in global refactoring effort
* Changed `Heptacom\HeptaConnect\Storage\ShopwareDal\Content\DatasetEntityType\DatasetEntityTypeDefinition` to `Heptacom\HeptaConnect\Storage\ShopwareDal\Content\EntityType\EntityTypeDefinition` in global refactoring effort
* Changed argument and variable names in `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodes::configure`, `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodes::execute` and `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings::configure`, `Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Command\MappingNode\ListMappingNodeSiblings::execute` in global refactoring effort
