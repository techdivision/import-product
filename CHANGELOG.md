# Version 1.0.0-beta43

## Bugfixes

* None

## Features

* Remove unnecessary configuration key from utility class CoreConfigDataKeys

# Version 1.0.0-beta42

## Bugfixes

* Fixed error in SQL statement preparation for stock status updates

## Features

* None

# Version 1.0.0-beta41

## Bugfixes

* None

## Features

* Remove unnecessary error_log statements

# Version 1.0.0-beta40

## Bugfixes

* Columns with empty values, related to the inventory, doesn't overwrite already existing values

## Features

* None

# Version 1.0.0-beta39

## Bugfixes

* Skip row instead of continue processing (in debug mode) when product with SKU can not be loaded in LastEntityIdObserver

## Features

* None

# Version 1.0.0-beta38

## Bugfixes

* None

## Features

* Minor refactoring

# Version 1.0.0-beta37

## Bugfixes

* None

## Features

* Append filename + linenumber for log message when categories that are not longer available in the CSV file

# Version 1.0.0-beta36

## Bugfixes

* None

## Features

* Switch log level for removing categories that are not longer available in the CSV file from notice to warning

# Version 1.0.0-beta35

## Bugfixes

* None

## Features

* Refactor file upload functionality

# Version 1.0.0-beta34

## Bugfixes

* None

## Features

* Refactor attribute import functionality

# Version 1.0.0-beta33

## Features

* Add configurable functionality to remove category product relations that not longer exists in the CSV file

## Bugfixes

* None

# Version 1.0.0-beta32

## Features

* None

## Bugfixes

* Removed invalid clear URL rewrite observer from default configuration file

# Version 1.0.0-beta31

## Features

* Completely remove URL rewrite handling

## Bugfixes

* None

# Version 1.0.0-beta30

## Features

* Move functionality to make URL unique from UrlRewriteObserver to UrlKeyObserver

## Bugfixes

* None

# Version 1.0.0-beta29

## Features

* Add functionality to load URL rewrites and their relations (for integration testing purposes)

## Bugfixes

* None

# Version 1.0.0-beta28

## Features

* None

## Bugfixes

* Fixed invalid URL rewrite creation

# Version 1.0.0-beta27

## Features

* None

## Bugfixes

* Fixed issue when invoking AbstractProductSubject::storeViewHasBeenProcessed($pk, $storeViewCode) method always returns false

# Version 1.0.0-beta26

## Features

* None

## Bugfixes

* Fixed issue with product import `add-update` operation that toggles between none and `-1` .html suffix for URL rewrites

# Version 1.0.0-beta25

## Features

* None

## Bugfixes

* Fixed issue with missing URL rewrites for additional store views in a multi website setup

# Version 1.0.0-beta24

## Bugfixes

* Fixed invalid URL rewrite handling in replace operation

## Features

* None

# Version 1.0.0-beta23

## Bugfixes

* None

## Features

* Remove unnecessary error_log() statement

# Version 1.0.0-beta22

## Bugfixes

* None

## Features

* Refactoring for better URL rewrite + attribute handling

# Version 1.0.0-beta21

## Bugfixes

* Fixed exception when creating URL rewrites if history flag in Magento 2 configuration is set to No

## Features

* None

# Version 1.0.0-beta20

## Bugfixes

* Fixed invalid URL rewrite handling in multi store environments

## Features

* None

# Version 1.0.0-beta19

## Bugfixes

* Fixed #75 [Invalid creation of product entities in a multi-store environment with replace operation](https://github.com/techdivision/import-product/issues/75)

## Features

* Make available image types configurable
* Add generic configurations for product price + inventory import
* Add generic LastEntityIdObserver that loads the product by the SKU found in the CSV file and set the entity ID as lastEntityId

# Version 1.0.0-beta18

## Bugfixes

* None

## Features

* Allow missing products when SKU can't be pre-loaded in debug-mode, else throw an exception
* Add PHPUnit tests for PreLoadEntityIdObserver class

# Version 1.0.0-beta17

## Bugfixes

* None

## Features

* Add custom system logger to default configuration

# Version 1.0.0-beta16

## Bugfixes

* None

## Features

* Replace array with system loggers with a collection

# Version 1.0.0-beta15

## Bugfixes

* None

## Features

* Use EntitySubjectInterface for entity related subjects

# Version 1.0.0-beta14

## Bugfixes

* Remove unnecessary admin store code

## Features

* None

# Version 1.0.0-beta13

## Bugfixes

* None

## Features

* Add self explaining exception message for missing category attribute "url_path" when creating product URL rewrites

# Version 1.0.0-beta12

## Bugfixes

* None

## Features

* Refactor to optimize DI integration

# Version 1.0.0-beta11

## Bugfixes

* None

## Features

* Switch to new plugin + subject factory implementations

# Version 1.0.0-beta10

## Bugfixes

* None

## Features

* Add fallback for url paths in case categories are not indexed

# Version 1.0.0-beta9

## Bugfixes

* None

## Features

* Use Robo for Travis-CI build process 
* Refactoring for new ConnectionInterface + SqlStatementsInterface

# Version 1.0.0-beta8

## Bugfixes

* None

## Features

* Remove archive directory from default configuration file

# Version 1.0.0-beta7

## Bugfixes

* None

## Features

* Refactoring Symfony DI integration

# Version 1.0.0-beta6

## Bugfixes

* Add missing loadEavAttributeOptionValueByAttributeCodeAndStoreIdAndValue() method to AbstracProductSubject + ProductBunchProcessor

## Features

* None


# Version 1.0.0-beta5

## Bugfixes

* None

## Features

* Update README.md

# Version 1.0.0-beta4

## Bugfixes

* Bugfix for invalid PK loading

## Features

* None

# Version 1.0.0-beta3

## Bugfixes

* Bugfix invalid URL rewrite creation if the CSV file has NO url_key specified

## Features

* None

# Version 1.0.0-beta2

## Bugfixes

* None

## Features

* Update default configuration file

# Version 1.0.0-beta1

## Bugfixes

* None

## Features

* Integrate Symfony DI functionality

# Version 1.0.0-alpha44

## Features

* Make select, multiselect + boolean callbacks abstract
* Refactoring for DI integrations

## Bugfixes

* None

# Version 1.0.0-alpha43

## Features

* Switch to latest callback interface and optimise error messages

## Bugfixes

* None

# Version 1.0.0-alpha42

## Features

* Extend method getSystemLogger() with parameter name to load a specific logger

## Bugfixes

* None

# Version 1.0.0-alpha41

## Features

* Add PreLoadEntityIdObserver to temporary store the entity IDs of deleted products

## Bugfixes

* Removed unnecessary array merging into registry in AbstractProductSubject::tearDown() method

# Version 1.0.0-alpha40

## Features

* Move UrlRewriteRepository to techdivision/import library

## Bugfixes

* None

# Version 1.0.0-alpha39

## Features

* None

## Bugfixes

* Use Magento configuration for product URL suffix and category path in product URLs

# Version 1.0.0-alpha38

## Features

* None

## Bugfixes

* Set URL redirects is_autogenerated flag to 0

# Version 1.0.0-alpha37

## Features

* None

## Bugfixes

* Fixed issue when updating URL rewrites

# Version 1.0.0-alpha36

## Features

* Add link type handling to AbstractProductSubject

## Bugfixes

* None

# Version 1.0.0-alpha35

## Features

* Refactoring to make existing functionality more generic

## Bugfixes

* None

# Version 1.0.0-alpha34

## Features

* Extract URL key parsing to separate observer

## Bugfixes

* None

# Version 1.0.0-alpha33

## Features

* Move generic UrlRewrite actions/processor to this techdivision/import library

## Bugfixes

* None

# Version 1.0.0-alpha32

## Features

* Refactoring to optimise for new category import functionality

## Bugfixes

* None

# Version 1.0.0-alpha31

## Features

* Refactoring for new plugin functionality

## Bugfixes

* None

# Version 1.0.0-alpha30

## Features

* Fixed invalid mapping for tax class 

## Bugfixes

* None

# Version 1.0.0-alpha29

## Features

* Add method ProductBunchProcessor::getEavAttributeByIsUserDefined() to load the user defined attributes
* Initialize the callbacks and observers in the BunchSubject::setUp() method instead of Simple class

## Bugfixes

* Fixed invald method call to ProductAttributeObserver::getBackendType()

# Version 1.0.0-alpha28

## Features

* None

## Bugfixes

* Fixed invald method call to ProductAttributeObserver::getBackendType()

# Version 1.0.0-alpha27

## Features

* None

## Bugfixes

* Add missing update functionality for URL rewrite product category relations

#Version 1.0.0-alpha26

## Features

* None

## Bugfixes

* Add URL rewrite product category relations to table catalog_url_rewrite_product_category

# Version 1.0.0-alpha25

## Features

* None

## Bugfixes

* Refactoring attribute observer to iterate over not empty columns in CSV files instead over all available attributes

# Version 1.0.0-alpha24

## Features

* None

## Bugfixes

* Separating artefact export functionality into an interface/trait

# Version 1.0.0-alpha23

## Features

* None

## Bugfixes

* Fixed PHPMD errors

# Version 1.0.0-alpha22

## Features

* Add mapping for SKU => store view code to improve multilange imports

## Bugfixes

* Query whether or not a row has already been imported, independent on the previous row's position 

# Version 1.0.0-alpha21

## Features

* Ignore missing categories on category product relation in debug mode
* Add CSV filename/line number to exceptions to improve error handling/debugging

## Bugfixes

* None

# Version 1.0.0-alpha20

## Features

* None

## Bugfixes

* Fixed PHPMD recommendations

# Version 1.0.0-alpha19

## Features

* Refactoring artefact export to reduce number of exported files

## Bugfixes

* None

# Version 1.0.0-alpha18

## Features

* Call parent::tearDown() method in AbstractProductSubject
* Change target-dir for artefact export in BunchSubject::exportArtefacts() method to next source dir

## Bugfixes

* None

# Version 1.0.0-alpha17

## Features

* Refactoring to use multiple field delimiter instead of hard coded (,)

## Bugfixes

* None

# Version 1.0.0-alpha16

## Features

* None

## Bugfixes

* Fixed not returned default value 0 for AbstractProductImportObserver::getValue()

# Version 1.0.0-alpha15

## Bugfixes

* Fixed error that AbstractProductImportObserver::hasValue() returns TRUE for empty column
* Fixed error that AbstractProductImportObserver::getValue() returns an empty string for an empty column
* Fixed exception when empty website ID found in ProductInventoryObserver

## Features

* None

# Version 1.0.0-alpha14

## Bugfixes

* Fixed exception when CSV file contains empty website and/or category columns

## Features

* None

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