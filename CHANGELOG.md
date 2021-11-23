# Version 25.0.0

## Bugfixes

* Fixed #PAC-206: Prevent finder mappings of different libraries to be overwritten
* Fix website relation clean up on Magento Commerce
* 
## Features

* Add techdivision/import-product-variant#22
* Add default configuration for tier price import
* Remove stack trace of exception for missing media directories > log a simple debug message instead
* Add missing operation `general/catalog_product/add-update.msi` to `ce/catalog_product_inventory/add-update` shortcut to also process the MSI artefact
* PAC-96: Use new constands for FileUploadConfiguration
    * https://github.com/techdivision/import/issues/181
* PAC-361: Don't check file system if copy-images defined as false
    * https://github.com/techdivision/import-cli-simple/issues/262
* Add missing validation for min_qty, min_sale_qty, max_sale_qty, notify_stock_qty, qty_increments, weight fields
* Add new Observer for Multiple Store View with comma separated.

# Version 24.0.2

## Bugfixes

* Fixed invalid member name

## Features

* None

# Version 24.0.1

## Bugfixes

* Fixed techdivision/import-product#156

## Features

* None

# Version 24.0.0

## Bugfixes

* None

## Features

* Add techdivision/import#184
* Add techdivision/import-product#155

# Version 23.1.0

## Bugfixes

* None

## Features

* Add #PAC-72: Extend dedicated CLI command to delete existing videos (professional + enterprise edition)
* Add #PAC-75: Extend dedicated CLI command to replace existing videos (professional + enterprise edition)

# Version 23.0.0

## Bugfixes

* None

## Features

* Add #PAC-102: Dedicated CLI command to import videos (professional + enterprise edition)

# Version 22.0.1

## Bugfixes

* Fixed techdivision/import#178
* Fixed techdivision/import#182

## Features

* None

# Version 22.0.0

## Bugfixes

* None

## Features

* Add #PAC-46
* Add #PAC-47
* Add #PAC-96

# Version 21.1.2

## Bugfixes

* Fixed invalid JSON configuration file

## Features

* None

# Version 21.1.1

## Bugfixes

* None

## Features

* Add functionality write a log warning instead throwing an exception if the configured media + images file dirctory are not available

# Version 21.1.0

## Bugfixes

* None

## Features

* Add #PAC-48

# Version 21.0.0

## Bugfixes

* None

## Features

* Add #PAC-73

# Version 20.0.0

## Bugfixes

* Add #PAC-52
* Add #PAC-85

## Features

* Switch to latest techdivision/import 16.* version as dependency
* Replace old default observer configuration for MSI sources, if inventory_source_items column is missing

# Version 19.0.6

## Bugfixes

* Fixed invald invokation of operation general/catalog_categor/children-count

## Features

* None

# Version 19.0.5

## Bugfixes

* Fixed import-product#149
* Fixed invalid observer names for validate operations

## Features

* None

# Version 19.0.4

## Bugfixes

* Fixed issue with delta import when SKUs of simples, that are related with grouped, are in database but will not be loaded

## Features

* None

# Version 19.0.3

## Bugfixes

* Fix associated_skus without qty

## Features

* Remove functionality to make given URL keys unique (we assume, that given URL keys HAVE to be provided in a unique manner)

# Version 19.0.2

## Bugfixes

* Fix associated_skus without qty

## Features

* Allow import of product relations in debug mode whether or not the related simple product exists

# Version 19.0.1

## Bugfixes

* None

## Features

* Activate URL rewrite clean-up functionality by default

# Version 19.0.0

## Bugfixes

* Fixed techdivision/import-cli-simple#233

## Features

* Refactor URL key handling
* Remove deprecated classes and methods
* Add techdivision/import#162
* Add techdivision/import-product#146
* Add techdivision/import-cli-simple#216
* Add techdivision/import-configuration-jms#25
* Remove unnecessary identifiers from configuration
* Switch to latest techdivision/import 15.* version as dependency

# Version 18.0.1

## Bugfixes

* None

## Features

* Make SQL to load product entity varchar values case sensitive

# Version 18.0.0

## Bugfixes

* None

## Features

* Add cache warmer for product varchar values to improve performance on CSV files with missing url_key value

# Version 17.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 14.* version as dependency

# Version 16.0.1

## Bugfixes

* Fixed invalid PHPUnit test for ProductInventoryObserver

## Features

* None

# Version 16.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 13.* version as dependency
* Remove unnecessary dedicated default product import configuration file for Magento 2.3.2

# Version 15.0.1

## Bugfixes

* Add missing alias for composite link observer

## Features

* None

# Version 15.0.0

## Bugfixes

* None

## Features

* Update default configuration for usage of new library specific DI identifiers
* Move library specific DI identifiers to the corresponding library, add aliases therefore
* Refactor AbstractProductSubject, add getStoreByStoreCode() + mapSkuToEntityId() methods to improve generics

# Version 14.0.0

## Bugfixes

* None

## Features

* Extend addSkuEntityIdMapping with a new optional entityId param

# Version 13.0.4

## Bugfixes

* None

## Features

* Extend clean-up-empty-columns configuration by price specifc columns

# Version 13.0.3

## Bugfixes

* Remove unnecessary pre-import observer from default configuration files

## Features

* None

# Version 13.0.2

## Bugfixes

* None

## Features

* Add new version specific configuration and Syfmony DI configuration files for Magento 2.3.2

# Version 13.0.1

## Bugfixes

* None

## Features

* Remove unnecessary attribute set observer from price and inventory import Symfony DI configuration

# Version 13.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 12.* version as dependency

# Version 12.0.0

## Bugfixes

* None

## Features

* Refactoring Cache Integration
* Switch to latest techdivision/import 11.* version as dependency

# Version 11.0.1

## Bugfixes

* Fixed issue in DI configuration leading to invalid cache configuration

## Features

* None

# Version 11.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 10.0.* version as dependency

# Version 10.0.1

## Bugfixes

* Fixed invalid persistProduct() method for replace operation

## Features

* None

# Version 10.0.0

## Bugfixes

* None

## Features

* Add SkuToPkMappingUtil implementation
* Add Listeners to add SKU => PK mapping to registry
* Refactor Cache Integration for PSR-6 compliance

# Version 9.0.1

## Bugfixes

* Fixed invalid product bundle composite observer DI configuration

## Features

* None

# Version 9.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 7.0.* version as dependency

# Version 8.0.1

## Bugfixes

* Update default configuration files with listeners

## Features

* None

# Version 8.0.0

## Bugfixes

* None

## Features

* Add composite observers to minimize configuration complexity
* Switch to latest techdivision/import 7.0.* version as dependency
* Make Actions and ActionInterfaces deprecated, replace DI configuration with GenericAction + GenericIdentifierAction

# Version 7.0.2

## Bugfixes

* None

## Features

* Update default configuration for grouped product import

# Version 7.0.1

## Bugfixes

* None

## Features

* Refactor default configuration, replace the import_product_link.observer.link.update with import_product_link.observer.link for update operation

# Version 7.0.0

## Bugfixes

* None

## Features

* Added techdivision/import-cli-simple#198

# Version 6.0.1

## Bugfixes

* Fixed issue in AbstractProductImportObserver::hasBeenProcessedRelation() + AbstractProductImportObserver::addProcessedRelation() methods

## Features

* None

# Version 6.0.0

## Bugfixes

* Fixed techdivision/import-product-variant#21

## Features

* None

# Version 5.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 6.0.* version as dependency

# Version 4.0.0

## Bugfixes

* None

## Features

* Switch to latest techdivision/import 5.0.* version as dependency

# Version 3.0.0

## Bugfixes

* None

## Features

* Compatibility for Magento 2.3.x

# Version 2.0.0

## Bugfixes

* None

## Features

* Compatibility for Magento 2.2.x

# Version 1.0.3

## Bugfixes

* None

## Features

* Also allow techdivision/import ~2.0 versions as dependency

# Version 1.0.2

## Bugfixes

* Inject NULL instead of dummy delete action when creating ProductWebsiteAction instance

## Features

* None

# Version 1.0.1

## Bugfixes

* Switch to phpdocumentor v2.9.* to avoid Travis-CI build errors

## Features

* None

# Version 1.0.0

## Bugfixes

* None

## Features

* Move PHPUnit test from tests to tests/unit folder for integration test compatibility reasons

# Version 1.0.0-beta53

## Bugfixes

* None

## Features

* Add missing interfaces for actions and repositories
* Replace class type hints for ProductBunchProcessor with interfaces

# Version 1.0.0-beta52

## Bugfixes

* Fixed invalid order of method invocation in tearDown() method

## Features

* None

# Version 1.0.0-beta51

## Bugfixes

* Remove stock status create/update functionality because Magento 2 indexer takes care about that

## Features

* Replace type hints for actions in product bunch processor with interfaces

# Version 1.0.0-beta50

## Bugfixes

* None

## Features

* Use interfaces instead of classes to inject product attribute actions in bunch processor

# Version 1.0.0-beta49

## Bugfixes

* None

## Features

* Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements

# Version 1.0.0-beta48

## Bugfixes

* None

## Features

* Add product cache warmer functionality for optimized performance

# Version 1.0.0-beta47

## Bugfixes

* Update category path handling in order to use store view specific slugs

## Features

* None

# Version 1.0.0-beta46

## Bugfixes

* None

## Features

* Remove update of processed file status, because of moving it to AbstractSubject

# Version 1.0.0-beta45

## Bugfixes

* Fixed exception when querying if clean-up-empty-image-columns is NOT set

## Features

* None

# Version 1.0.0-beta44

## Bugfixes

* None

## Features

* Make image types dynamic and extensible

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
