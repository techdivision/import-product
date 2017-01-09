# Version 1.0.0-alpha13

## Bugfixes

* None

## Features

* Add mapping for bundle_shipment_type => shipment_type attribute

# Version 1.0.0-alpha12

## Bugfixes

* None

## Features

* Update scope to protected for methods in ProductWebsiteObserver

# Version 1.0.0-alpha11

## Bugfixes

* None

## Features

* Refactor + generalize observers

# Version 1.0.0-alpha10

## Bugfixes

* None

## Features

* Add explode() callback to AbstractProductImportObserver
* Invoke callback only, if value to invoke callback on, is NOT empty

# Version 1.0.0-alpha9

## Bugfixes

* None

## Features

* Implement add-update operation
* Refactoring URL rewrite functionality to support add-update operation
* Rename ProductCategory functionality to CategoryProduct to follow Magento 2 naming

# Version 1.0.0-alpha8

## Bugfixes

* None

## Features

* Switch to new create/delete naming convention
* Add basic product update functionality for add-update operation

# Version 1.0.0-alpha7

## Bugfixes

* Fixed some Scrutinizer CI mess detection errors

## Features

* Add methods to handle store view code to AbstractProductImportObserver
* Add AbstractProductSubject for product import specific subject implementations
* Add ProductProcessorInterface and rename ProductProcessor => ProductBunchProcessor/Interface

# Version 1.0.0-alpha6

## Bugfixes

* Fixed invalid handling on empty product website + category relations

## Features

* Add Robo.li composer dependeny + task configuration

# Version 1.0.0-alpha5

## Bugfixes

* None

## Features

* Implement [Operation](https://github.com/techdivision/import-product/issues/8) functionality

# Version 1.0.0-alpha4

## Bugfixes

* None

## Features

* Implement [Clean-Up](https://github.com/techdivision/import-product/issues/9) for products and relations

# Version 1.0.0-alpha3

## Bugfixes

* None

## Features

* Implement Replace import mode für URL rewrites
* Refactoring to allow multiple prepared statements per CRUD processor instance

# Version 1.0.0-alpha2

## Bugfixes

* None

## Features

* Let AbstractProductImportCallback extend AbstractCallback + Typo fixes

# Version 1.0.0-alpha1

## Bugfixes

* None

## Features

* Refactoring + Documentation to prepare for Github release