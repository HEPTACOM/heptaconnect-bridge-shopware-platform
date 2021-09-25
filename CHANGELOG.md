# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.7.0] - 2021-09-25

### Added

* Add service definition `Heptacom\HeptaConnect\Storage\Base\MappingPersister\Contract\MappingPersisterContract`

### Changed

* Add dependency `heptacom_heptaconnect.logger` to service definition `\Heptacom\HeptaConnect\Core\Portal\PortalStorageFactory`
* Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Emission\EmitContextFactory` to match its interface `\Heptacom\HeptaConnect\Core\Emission\Contract\EmitContextFactoryInterface`
* Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Job\Handler\EmissionHandler` to match its interface `\Heptacom\HeptaConnect\Core\Job\Contract\EmissionHandlerInterface`
* Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Job\Handler\ExplorationHandler` to match its interface `\Heptacom\HeptaConnect\Core\Job\Contract\ExplorationHandlerInterface`
* Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Job\Handler\ReceptionHandler` to match its interface `\Heptacom\HeptaConnect\Core\Job\Contract\ReceptionHandlerInterface`
* Change service definition id based upon class `\Heptacom\HeptaConnect\Core\Reception\ReceiveContextFactory` to match its interface `\Heptacom\HeptaConnect\Core\Reception\Contract\ReceiveContextFactoryInterface`
* Change service definition id based upon class `\Heptacom\HeptaConnect\Storage\ShopwareDal\Repository\MappingRepository` to match its contract `\Heptacom\HeptaConnect\Storage\Base\Contract\Repository\MappingRepositoryContract`
* Remove argument `Heptacom\HeptaConnect\Core\Mapping\MappingService` from service definition `Heptacom\HeptaConnect\Portal\Base\Flow\DirectEmission\DirectEmissionFlowContract` 
