# HEPTAConnect Shopware Platform Bridge
#### This is part of HEPTACOM solutions for medium and large enterprises.

## Description

This is the HEPTAConnect package to provide a runtime in a shopware platform project.
Read more under [Overview](../heptaconnect-docs).


## System requirements

* PHP 7.4 or above


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

## License

Copyright 2020 HEPTACOM GmbH

Dual licensed under the [Apache License, Version 2.0](./LICENSE.md) (the "License") and proprietary license; you may not use this project except in compliance with the License.
You may obtain a copy of the Apache License at [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0).
Contact us on [our website](https://www.heptacom.de) for further information about proprietary usage.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and limitations under the License.
