<?php

/**
 * TechDivision\Import\Product\Utils\SqlStatements
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Repositories;

use TechDivision\Import\Product\Utils\SqlStatementKeys;

/**
 * Repository class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class SqlStatementRepository extends \TechDivision\Import\Repositories\SqlStatementRepository
{

    /**
     * The SQL statements.
     *
     * @var array
     */
    private $statements = array(
        SqlStatementKeys::PRODUCT =>
            'SELECT * FROM ${table:catalog_product_entity} WHERE sku = :sku',
        SqlStatementKeys::PRODUCTS =>
            'SELECT * FROM ${table:catalog_product_entity}',
        SqlStatementKeys::PRODUCT_WEBSITE =>
            'SELECT * FROM ${table:catalog_product_website} WHERE product_id = :product_id AND website_id = :website_id',
        SqlStatementKeys::PRODUCT_WEBSITES_BY_SKU =>
             'SELECT *
                FROM ${table:catalog_product_website} t0
          INNER JOIN ${table:catalog_product_entity} t1
               WHERE t1.sku = :sku
                 AND t0.product_id = t1.entity_id',
        SqlStatementKeys::PRODUCT_DATETIMES =>
            'SELECT *
               FROM ${table:catalog_product_entity_datetime}
           ORDER BY entity_id, store_id',
        SqlStatementKeys::PRODUCT_DATETIMES_BY_PK_AND_STORE_ID =>
            'SELECT *
               FROM ${table:catalog_product_entity_datetime}
              WHERE entity_id = :pk
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_DECIMALS =>
         'SELECT *
               FROM ${table:catalog_product_entity_decimal}
           ORDER BY entity_id, store_id',
        SqlStatementKeys::PRODUCT_DECIMALS_BY_PK_AND_STORE_ID =>
            'SELECT *
               FROM ${table:catalog_product_entity_decimal}
              WHERE entity_id = :pk
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_INTS =>
            'SELECT *
               FROM ${table:catalog_product_entity_int}
           ORDER BY entity_id, store_id',
        SqlStatementKeys::PRODUCT_INTS_BY_PK_AND_STORE_ID =>
            'SELECT *
               FROM ${table:catalog_product_entity_int}
              WHERE entity_id = :pk
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_TEXTS =>
            'SELECT *
               FROM ${table:catalog_product_entity_text}
           ORDER BY entity_id, store_id',
        SqlStatementKeys::PRODUCT_TEXTS_BY_PK_AND_STORE_ID =>
            'SELECT *
               FROM ${table:catalog_product_entity_text}
              WHERE entity_id = :pk
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_VARCHARS =>
            'SELECT *
               FROM ${table:catalog_product_entity_varchar}
           ORDER BY entity_id, store_id',
        SqlStatementKeys::PRODUCT_VARCHARS_BY_PK_AND_STORE_ID =>
            'SELECT *
               FROM ${table:catalog_product_entity_varchar}
              WHERE entity_id = :pk
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID =>
            'SELECT t1.*
               FROM ${table:catalog_product_entity_varchar} t1,
                    ${table:eav_attribute} t2
              WHERE t2.attribute_code = :attribute_code
                AND t2.entity_type_id = :entity_type_id
                AND t1.attribute_id = t2.attribute_id
                AND t1.store_id = :store_id',
        SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE =>
            'SELECT t1.*
               FROM ${table:catalog_product_entity_varchar} t1,
                    ${table:eav_attribute} t2
              WHERE t2.attribute_code = :attribute_code
                AND t2.entity_type_id = :entity_type_id
                AND t1.attribute_id = t2.attribute_id
                AND t1.store_id = :store_id
                AND t1.value = BINARY :value',
        SqlStatementKeys::CATEGORY_PRODUCT =>
            'SELECT * FROM ${table:catalog_category_product} WHERE category_id = :category_id AND product_id = :product_id',
        SqlStatementKeys::CATEGORY_PRODUCT_BY_SKU =>
            'SELECT t1.*
               FROM ${table:catalog_category_product} t1,
                    ${table:catalog_product_entity} t2
              WHERE t2.sku = :sku
                AND t1.product_id = t2.entity_id',
        SqlStatementKeys::STOCK_ITEM =>
            'SELECT * FROM ${table:cataloginventory_stock_item} WHERE product_id = :product_id AND website_id = :website_id AND stock_id = :stock_id',
        SqlStatementKeys::STOCK_ITEM_STATUS =>
            'SELECT * FROM ${table:cataloginventory_stock_status} WHERE product_id = :product_id AND website_id = :website_id AND stock_id = :stock_id',
        SqlStatementKeys::PRODUCT_RELATION =>
            'SELECT *
               FROM ${table:catalog_product_relation}
              WHERE parent_id = :parent_id
                AND child_id = :child_id',
        SqlStatementKeys::CREATE_PRODUCT =>
            'INSERT
               INTO ${table:catalog_product_entity}
                    (sku,
                     created_at,
                     updated_at,
                     has_options,
                     required_options,
                     type_id,
                     attribute_set_id)
             VALUES (:sku,
                     :created_at,
                     :updated_at,
                     :has_options,
                     :required_options,
                     :type_id,
                     :attribute_set_id)',
        SqlStatementKeys::UPDATE_PRODUCT =>
            'UPDATE ${table:catalog_product_entity}
                SET sku = :sku,
                   created_at = :created_at,
                    updated_at = :updated_at,
                    has_options = :has_options,
                    required_options = :required_options,
                    type_id = :type_id,
                    attribute_set_id = :attribute_set_id
              WHERE entity_id = :entity_id',
        SqlStatementKeys::CREATE_PRODUCT_WEBSITE =>
            'INSERT
               INTO ${table:catalog_product_website}
                    (product_id,
                     website_id)
             VALUES (:product_id,
                     :website_id)',
        SqlStatementKeys::CREATE_CATEGORY_PRODUCT =>
            'INSERT ${table:catalog_category_product}
                    (${column-names:catalog_category_product})
             VALUES (${column-placeholders:catalog_category_product})',
        SqlStatementKeys::UPDATE_CATEGORY_PRODUCT =>
            'UPDATE ${table:catalog_category_product}
                SET ${column-values:catalog_category_product}
              WHERE entity_id = :entity_id
                AND category_id = :category_id
                AND product_id = :product_id',
        SqlStatementKeys::CREATE_PRODUCT_DATETIME =>
            'INSERT
               INTO ${table:catalog_product_entity_datetime}
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
            VALUES (:entity_id,
                    :attribute_id,
                    :store_id,
                    :value)',
        SqlStatementKeys::UPDATE_PRODUCT_DATETIME =>
            'UPDATE ${table:catalog_product_entity_datetime}
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_DATETIME =>
            'DELETE
               FROM ${table:catalog_product_entity_datetime}
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_DECIMAL =>
            'INSERT
               INTO ${table:catalog_product_entity_decimal}
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
            VALUES (:entity_id,
                    :attribute_id,
                    :store_id,
                    :value)',
        SqlStatementKeys::UPDATE_PRODUCT_DECIMAL =>
            'UPDATE ${table:catalog_product_entity_decimal}
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_DECIMAL =>
            'DELETE
               FROM ${table:catalog_product_entity_decimal}
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_INT =>
            'INSERT
               INTO ${table:catalog_product_entity_int}
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
             VALUES (:entity_id,
                     :attribute_id,
                     :store_id,
                     :value)',
        SqlStatementKeys::UPDATE_PRODUCT_INT =>
            'UPDATE ${table:catalog_product_entity_int}
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_INT =>
            'DELETE
               FROM ${table:catalog_product_entity_int}
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_VARCHAR =>
            'INSERT
               INTO ${table:catalog_product_entity_varchar}
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
             VALUES (:entity_id,
                     :attribute_id,
                     :store_id,
                     :value)',
        SqlStatementKeys::UPDATE_PRODUCT_VARCHAR =>
            'UPDATE ${table:catalog_product_entity_varchar}
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_VARCHAR =>
            'DELETE
               FROM ${table:catalog_product_entity_varchar}
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_TEXT =>
            'INSERT
               INTO ${table:catalog_product_entity_text}
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
             VALUES (:entity_id,
                     :attribute_id,
                     :store_id,
                     :value)',
        SqlStatementKeys::UPDATE_PRODUCT_TEXT =>
            'UPDATE ${table:catalog_product_entity_text}
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_RELATION =>
            'INSERT
                   INTO ${table:catalog_product_relation}
                        (parent_id,
                         child_id)
                 VALUES (:parent_id,
                         :child_id)',
        SqlStatementKeys::DELETE_PRODUCT_TEXT =>
            'DELETE
               FROM ${table:catalog_product_entity_text}
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_STOCK_ITEM_STATUS =>
            'INSERT 
               INTO ${table:cataloginventory_stock_status}
                    (${column-names:cataloginventory_stock_status})
             VALUES (${column-placeholders:cataloginventory_stock_status})',
        SqlStatementKeys::UPDATE_STOCK_ITEM_STATUS =>
            'UPDATE ${table:cataloginventory_stock_status} 
                SET ${column-values:cataloginventory_stock_status}
              WHERE product_id = :product_id
                AND website_id = :website_id
                AND stock_id = :stock_id',
        SqlStatementKeys::DELETE_STOCK_ITEM_STATUS_BY_SKU =>
            'DELETE t1.*
               FROM ${table:cataloginventory_stock_status} t1
         INNER JOIN ${table:catalog_product_entity} t2
              WHERE t2.sku = :sku
                AND t1.product_id = t2.entity_id',
        SqlStatementKeys::CREATE_STOCK_ITEM =>
            'INSERT INTO ${table:cataloginventory_stock_item} (%s) VALUES (:%s)',
        SqlStatementKeys::UPDATE_STOCK_ITEM =>
            'UPDATE ${table:cataloginventory_stock_item} SET %s WHERE %s',
        SqlStatementKeys::DELETE_PRODUCT =>
            'DELETE
               FROM ${table:catalog_product_entity}
              WHERE sku = :sku',
        SqlStatementKeys::DELETE_STOCK_ITEM_BY_SKU =>
            'DELETE t1.*
               FROM ${table:cataloginventory_stock_item} t1
         INNER JOIN ${table:catalog_product_entity} t2
              WHERE t2.sku = :sku
                AND t1.product_id = t2.entity_id',
        SqlStatementKeys::DELETE_PRODUCT_WEBSITE_BY_SKU =>
            'DELETE t1.*
               FROM ${table:catalog_product_website} t1
         INNER JOIN ${table:catalog_product_entity} t2
              WHERE t2.sku = :sku
                AND t1.product_id = t2.entity_id',
        SqlStatementKeys::DELETE_PRODUCT_WEBSITE =>
            'DELETE t1.*
               FROM ${table:catalog_product_website} t1
              WHERE t1.product_id = :product_id
                AND t1.website_id = :website_id',
        SqlStatementKeys::DELETE_CATEGORY_PRODUCT =>
            'DELETE t1.*
               FROM ${table:catalog_category_product} t1
              WHERE entity_id = :entity_id',
        SqlStatementKeys::DELETE_CATEGORY_PRODUCT_BY_SKU =>
            'DELETE t1.*
               FROM ${table:catalog_category_product} t1
         INNER JOIN ${table:catalog_product_entity} t2
              WHERE t2.sku = :sku
                AND t1.product_id = t2.entity_id',
        SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_PK =>
            'SELECT t1.*
               FROM ${table:catalog_product_entity_varchar} t1,
                    ${table:eav_attribute} t2
              WHERE t2.attribute_code = :attribute_code
                AND t2.entity_type_id = :entity_type_id
                AND t1.attribute_id = t2.attribute_id
                AND (t1.store_id = :store_id OR t1.store_id = 0)
                AND t1.entity_id = :pk
           ORDER BY t1.store_id DESC'
    );

    /**
     * Initializes the SQL statement repository with the primary key and table prefix utility.
     *
     * @param \IteratorAggregate<\TechDivision\Import\Dbal\Utils\SqlCompilerInterface> $compilers The array with the compiler instances
     */
    public function __construct(\IteratorAggregate $compilers)
    {

        // pass primary key + table prefix utility to parent instance
        parent::__construct($compilers);

        // compile the SQL statements
        $this->compile($this->statements);
    }
}
