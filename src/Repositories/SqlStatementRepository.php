<?php

/**
 * TechDivision\Import\Product\Repositories\SqlStatementKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
            'SELECT * FROM catalog_product_entity WHERE sku = :sku',
        SqlStatementKeys::PRODUCT_WEBSITE =>
            'SELECT * FROM catalog_product_website WHERE product_id = :product_id AND website_id = :website_id',
        SqlStatementKeys::PRODUCT_DATETIME =>
            'SELECT *
               FROM catalog_product_entity_datetime
              WHERE entity_id = :entity_id
                AND attribute_id = :attribute_id
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_DECIMAL =>
            'SELECT *
               FROM catalog_product_entity_decimal
              WHERE entity_id = :entity_id
                AND attribute_id = :attribute_id
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_INT =>
            'SELECT *
               FROM catalog_product_entity_int
              WHERE entity_id = :entity_id
                AND attribute_id = :attribute_id
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_TEXT =>
            'SELECT *
               FROM catalog_product_entity_text
              WHERE entity_id = :entity_id
                AND attribute_id = :attribute_id
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_VARCHAR =>
            'SELECT *
               FROM catalog_product_entity_varchar
              WHERE entity_id = :entity_id
                AND attribute_id = :attribute_id
                AND store_id = :store_id',
        SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE =>
            'SELECT t1.*
               FROM catalog_product_entity_varchar t1,
                    eav_attribute t2
              WHERE t2.attribute_code = :attribute_code
                AND t2.entity_type_id = :entity_type_id
                AND t1.attribute_id = t2.attribute_id
                AND t1.store_id = :store_id
                AND t1.value = :value',
        SqlStatementKeys::CATEGORY_PRODUCT =>
            'SELECT * FROM catalog_category_product WHERE category_id = :category_id AND product_id = :product_id',
        SqlStatementKeys::CATEGORY_PRODUCT_BY_SKU =>
            'SELECT t1.*
               FROM catalog_category_product t1,
                    catalog_product_entity t2
              WHERE t2.sku = :sku
                AND t1.product_id = t2.entity_id',
        SqlStatementKeys::STOCK_STATUS =>
            'SELECT * FROM cataloginventory_stock_status WHERE product_id = :product_id AND website_id = :website_id AND stock_id = :stock_id',
        SqlStatementKeys::STOCK_ITEM =>
            'SELECT * FROM cataloginventory_stock_item WHERE product_id = :product_id AND website_id = :website_id AND stock_id = :stock_id',
        SqlStatementKeys::CREATE_PRODUCT =>
            'INSERT
               INTO catalog_product_entity
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
            'UPDATE catalog_product_entity
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
               INTO catalog_product_website
                    (product_id,
                     website_id)
             VALUES (:product_id,
                     :website_id)',
        SqlStatementKeys::CREATE_CATEGORY_PRODUCT =>
            'INSERT
               INTO catalog_category_product
                    (category_id,
                     product_id,
                     position)
             VALUES (:category_id,
                     :product_id,
                     :position)',
        SqlStatementKeys::UPDATE_CATEGORY_PRODUCT =>
            'UPDATE catalog_category_product
                SET category_id = :category_id,
                    product_id = :product_id,
                    position = :position
             WHERE  entity_id = :entity_id',
        SqlStatementKeys::CREATE_PRODUCT_DATETIME =>
            'INSERT
               INTO catalog_product_entity_datetime
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
            VALUES (:entity_id,
                    :attribute_id,
                    :store_id,
                    :value)',
        SqlStatementKeys::UPDATE_PRODUCT_DATETIME =>
            'UPDATE catalog_product_entity_datetime
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_DATETIME =>
            'DELETE
               FROM catalog_product_entity_datetime
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_DECIMAL =>
            'INSERT
               INTO catalog_product_entity_decimal
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
            VALUES (:entity_id,
                    :attribute_id,
                    :store_id,
                    :value)',
        SqlStatementKeys::UPDATE_PRODUCT_DECIMAL =>
            'UPDATE catalog_product_entity_decimal
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_DECIMAL =>
            'DELETE
               FROM catalog_product_entity_decimal
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_INT =>
            'INSERT
               INTO catalog_product_entity_int
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
             VALUES (:entity_id,
                     :attribute_id,
                     :store_id,
                     :value)',
        SqlStatementKeys::UPDATE_PRODUCT_INT =>
            'UPDATE catalog_product_entity_int
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_INT =>
            'DELETE
               FROM catalog_product_entity_int
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_VARCHAR =>
            'INSERT
               INTO catalog_product_entity_varchar
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
             VALUES (:entity_id,
                     :attribute_id,
                     :store_id,
                     :value)',
        SqlStatementKeys::UPDATE_PRODUCT_VARCHAR =>
            'UPDATE catalog_product_entity_varchar
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_VARCHAR =>
            'DELETE
               FROM catalog_product_entity_varchar
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_PRODUCT_TEXT =>
            'INSERT
               INTO catalog_product_entity_text
                    (entity_id,
                     attribute_id,
                     store_id,
                     value)
             VALUES (:entity_id,
                     :attribute_id,
                     :store_id,
                     :value)',
        SqlStatementKeys::UPDATE_PRODUCT_TEXT =>
            'UPDATE catalog_product_entity_text
                SET entity_id = :entity_id,
                    attribute_id = :attribute_id,
                    store_id = :store_id,
                    value = :value
              WHERE value_id = :value_id',
        SqlStatementKeys::DELETE_PRODUCT_TEXT =>
            'DELETE
               FROM catalog_product_entity_text
              WHERE value_id = :value_id',
        SqlStatementKeys::CREATE_STOCK_STATUS =>
            'INSERT INTO cataloginventory_stock_status (%s) VALUES (:%s)',
        SqlStatementKeys::UPDATE_STOCK_STATUS =>
            'UPDATE cataloginventory_stock_status SET %s WHERE %s',
        SqlStatementKeys::CREATE_STOCK_ITEM =>
            'INSERT INTO cataloginventory_stock_item (%s) VALUES (:%s)',
        SqlStatementKeys::UPDATE_STOCK_ITEM =>
            'UPDATE cataloginventory_stock_item SET %s WHERE %s',
        SqlStatementKeys::DELETE_PRODUCT =>
            'DELETE
               FROM catalog_product_entity
              WHERE sku = :sku',
        SqlStatementKeys::DELETE_STOCK_STATUS_BY_SKU =>
            'DELETE cataloginventory_stock_status
               FROM cataloginventory_stock_status
         INNER JOIN catalog_product_entity
              WHERE catalog_product_entity.sku = :sku
                AND cataloginventory_stock_status.product_id = catalog_product_entity.entity_id',
        SqlStatementKeys::DELETE_STOCK_ITEM_BY_SKU =>
            'DELETE cataloginventory_stock_item
               FROM cataloginventory_stock_item
         INNER JOIN catalog_product_entity
              WHERE catalog_product_entity.sku = :sku
                AND cataloginventory_stock_item.product_id = catalog_product_entity.entity_id',
        SqlStatementKeys::DELETE_PRODUCT_WEBSITE_BY_SKU =>
            'DELETE catalog_product_website
               FROM catalog_product_website
         INNER JOIN catalog_product_entity
              WHERE catalog_product_entity.sku = :sku
                AND catalog_product_website.product_id = catalog_product_entity.entity_id',
        SqlStatementKeys::DELETE_CATEGORY_PRODUCT =>
            'DELETE
               FROM catalog_category_product
              WHERE entity_id = :entity_id',
        SqlStatementKeys::DELETE_CATEGORY_PRODUCT_BY_SKU =>
            'DELETE catalog_category_product
               FROM catalog_category_product
         INNER JOIN catalog_product_entity
              WHERE catalog_product_entity.sku = :sku
                AND catalog_category_product.product_id = catalog_product_entity.entity_id'
    );

    /**
     * Initialize the the SQL statements.
     */
    public function __construct()
    {

        // call the parent constructor
        parent::__construct();

        // merge the class statements
        foreach ($this->statements as $key => $statement) {
            $this->preparedStatements[$key] = $statement;
        }
    }
}
