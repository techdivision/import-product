# M2IF - Product Import

[![Latest Stable Version](https://img.shields.io/packagist/v/techdivision/import-product.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-product) 
 [![Total Downloads](https://img.shields.io/packagist/dt/techdivision/import-product.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-product)
 [![License](https://img.shields.io/packagist/l/techdivision/import-product.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-product)
 [![Build Status](https://img.shields.io/travis/techdivision/import-product/master.svg?style=flat-square)](http://travis-ci.org/techdivision/import-product)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/techdivision/import-product/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-product/?branch=master) [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/techdivision/import-product/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-product/?branch=master)

# Missing Index

As the M2IF functionality differs from the Magento 2 standard, for performance reasons, it is 
necessary to manually add a missing index.

To do that, open a MySQL command line and enter the following SQL statement
 
```sql
$ ALTER TABLE `eav_attribute_option_value` ADD INDEX `EAV_ATTRIBUTE_OPTION_VALUE_VALUE` (`value` ASC);
$ ALTER TABLE `catalog_product_entity_int` ADD INDEX `CATALOG_PRODUCT_ENTITY_INT_VALUE` (`value` ASC);
$ ALTER TABLE `catalog_product_entity_varchar` ADD INDEX `CATALOG_PRODUCT_ENTITY_VARCHAR_VALUE` (`value` ASC);
$ ALTER TABLE `catalog_product_entity_decimal` ADD INDEX `CATALOG_PRODUCT_ENTITY_DECIMAL_VALUE` (`value` ASC);
$ ALTER TABLE `catalog_product_entity_datetime` ADD INDEX `CATALOG_PRODUCT_ENTITY_DATETIME_VALUE` (`value` ASC);
$ ALTER TABLE `url_rewrite` ADD INDEX `URL_REWRITE_ENTITY_ID` (`entity_id` ASC);
$ ALTER TABLE `url_rewrite` ADD INDEX `URL_REWRIRE_ENTITY_TYPE_ENTITY_ID` (`entity_id` ASC, `entity_type` ASC);
```

> This also improves performance of the Magento 2 standard import functionality, but not at
> same level as for M2IF.