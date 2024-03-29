# HEPTAconnect Shopware Platform Bridge
#### This is part of HEPTACOM solutions for medium and large enterprises.

## Description

This is the HEPTAconnect package to provide a runtime in a shopware platform project.
Read more in the [documentation](https://heptaconnect.io/).


## System requirements

* PHP 8.0 or above


## Changelog

See the attached [CHANGELOG.md](./CHANGELOG.md) file for a complete version history and release notes.


## Additional development requirements

* Make
* Any debugging/coverage php extension like xdebug or pcov
* Everything a shopware platform project needs like mysql and several more php extensions

For running tests locally you need a mysql database.
The tests need the connection details in the `.env.test` and an already created database with the default schema.
You can generate the default schema using the following command:
```bash
$ echo "set names 'utf8'; source vendor/shopware/core/schema.sql;" | mysql # your credentials
```

## Contributing

Thank you for considering contributing to this package! Be sure to sign the [CLA](./CLA.md) after creating the pull request. [![CLA assistant](https://cla-assistant.io/readme/badge/HEPTACOM/heptaconnect-bridge-shopware-platform)](https://cla-assistant.io/HEPTACOM/heptaconnect-bridge-shopware-platform)


### Steps to contribute

1. Fork the repository
2. `git clone yourname/heptaconnect-bridge-shopware-platform`
3. Make your changes to master branch
4. Create your Pull-Request


### Check your changes

1. Check and fix code style `make cs-fix && make cs`
2. Check tests `make test`
3. Check whether test code coverage is same or higher `make coverage`
4. Check whether tests can find future obscurities `make infection`


## License

Copyright 2020 HEPTACOM GmbH

Dual licensed under the [GNU Affero General Public License v3.0](./LICENSE.md) (the "License") and proprietary license; you may not use this project except in compliance with the License.
You may obtain a copy of the AGPL License at [https://spdx.org/licenses/AGPL-3.0-or-later.html](https://spdx.org/licenses/AGPL-3.0-or-later.html).
Contact us on [our website](https://www.heptacom.de) for further information about proprietary usage.
