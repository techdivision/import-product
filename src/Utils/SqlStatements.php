<?php

/**
 * TechDivision\Import\Product\Utils\SqlStatements
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

namespace TechDivision\Import\Product\Utils;

use TechDivision\Import\Utils\SqlStatements as FallbackStatements;

/**
 * Utility class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class SqlStatements extends FallbackStatements
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * The SQL statement to create new products.
     *
     * @var string
     */
    const CREATE_PRODUCT = 'INSERT
                              INTO catalog_product_entity (
                                       sku,
                                       created_at,
                                       updated_at,
                                       has_options,
                                       required_options,
                                       type_id,
                                       attribute_set_id
                                   )
                            VALUES (?, ?, ?, ?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product website relation.
     *
     * @var string
     */
    const CREATE_PRODUCT_WEBSITE = 'INSERT
                                      INTO catalog_product_website (
                                               product_id,
                                               website_id
                                           )
                                    VALUES (?, ?)';

    /**
     * The SQL statement to create a new product category relation.
     *
     * @var string
     */
    const CREATE_PRODUCT_CATEGORY = 'INSERT
                                       INTO catalog_category_product (
                                                category_id,
                                                product_id,
                                                position
                                            )
                                     VALUES (?, ?, ?)';

    /**
     * The SQL statement to create a new product datetime value.
     *
     * @var string
     */
    const CREATE_PRODUCT_DATETIME = 'INSERT
                                       INTO catalog_product_entity_datetime (
                                                entity_id,
                                                attribute_id,
                                                store_id,
                                                value
                                            )
                                    VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product decimal value.
     *
     * @var string
     */
    const CREATE_PRODUCT_DECIMAL = 'INSERT
                                      INTO catalog_product_entity_decimal (
                                               entity_id,
                                               attribute_id,
                                               store_id,
                                               value
                                           )
                                   VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product integer value.
     *
     * @var string
     */
    const CREATE_PRODUCT_INT = 'INSERT
                                  INTO catalog_product_entity_int (
                                           entity_id,
                                           attribute_id,
                                           store_id,
                                           value
                                       )
                                VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product varchar value.
     *
     * @var string
     */
    const CREATE_PRODUCT_VARCHAR = 'INSERT
                                      INTO catalog_product_entity_varchar (
                                               entity_id,
                                               attribute_id,
                                               store_id,
                                               value
                                           )
                                    VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a new product text value.
     *
     * @var string
     */
    const CREATE_PRODUCT_TEXT = 'INSERT
                                   INTO catalog_product_entity_text (
                                            entity_id,
                                            attribute_id,
                                            store_id,
                                            value
                                        )
                                 VALUES (?, ?, ?, ?)';

    /**
     * The SQL statement to create a product's stock status.
     *
     * @var string
     */
    const CREATE_STOCK_STATUS = 'INSERT
                                   INTO cataloginventory_stock_status (
                                            product_id,
                                            website_id,
                                            stock_id,
                                            qty,
                                            stock_status
                                        )
                                 VALUES (?, ?, ?, ?, ?)';

    /**
     * The SQL statement to create a product's stock status.
     *
     * @var string
     */
    const CREATE_STOCK_ITEM = 'INSERT
                                 INTO cataloginventory_stock_item (
                                          product_id,
                                          stock_id,
                                          website_id,
                                          qty,
                                          min_qty,
                                          use_config_min_qty,
                                          is_qty_decimal,
                                          backorders,
                                          use_config_backorders,
                                          min_sale_qty,
                                          use_config_min_sale_qty,
                                          max_sale_qty,
                                          use_config_max_sale_qty,
                                          is_in_stock,
                                          notify_stock_qty,
                                          use_config_notify_stock_qty,
                                          manage_stock,
                                          use_config_manage_stock,
                                          use_config_qty_increments,
                                          qty_increments,
                                          use_config_enable_qty_inc,
                                          enable_qty_increments,
                                          is_decimal_divided
                                      )
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    /**
     * The SQL statement to create new URL rewrites.
     *
     * @var string
     */
    const CREATE_URL_REWRITE = 'INSERT
                                  INTO url_rewrite (
                                       entity_type,
                                       entity_id,
                                       request_path,
                                       target_path,
                                       redirect_type,
                                       store_id,
                                       description,
                                       is_autogenerated,
                                       metadata
                                   )
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

    /**
     * The SQL statement to remove a existing product.
     *
     * @var string
     */
    const REMOVE_PRODUCT = 'DELETE
                              FROM catalog_product_entity
                             WHERE sku = ?';

    /**
     * The SQL statement to remove a existing URL rewrite.
     *
     * @var string
     */
    const REMOVE_URL_REWRITE = 'DELETE
                                  FROM url_rewrite
                                 WHERE url_rewrite_id = ?';

    /**
     * The SQL statement to remove all existing URL rewrites related with the SKU passed as parameter.
     *
     * @var string
     */
    const REMOVE_URL_REWRITE_BY_SKU = 'DELETE url_rewrite
                                         FROM url_rewrite
                                   INNER JOIN catalog_product_entity
                                        WHERE catalog_product_entity.sku = ?
                                          AND url_rewrite.entity_id = catalog_product_entity.entity_id';

    /**
     * The SQL statement to remove all existing stock status related with the SKU passed as parameter.
     *
     * @var string
     */
    const REMOVE_STOCK_STATUS_BY_SKU = 'DELETE cataloginventory_stock_status
                                         FROM cataloginventory_stock_status
                                   INNER JOIN catalog_product_entity
                                        WHERE catalog_product_entity.sku = ?
                                          AND cataloginventory_stock_status.product_id = catalog_product_entity.entity_id';

    /**
     * The SQL statement to remove all existing stock item related with the SKU passed as parameter.
     *
     * @var string
     */
    const REMOVE_STOCK_ITEM_BY_SKU = 'DELETE cataloginventory_stock_item
                                        FROM cataloginventory_stock_item
                                  INNER JOIN catalog_product_entity
                                       WHERE catalog_product_entity.sku = ?
                                         AND cataloginventory_stock_item.product_id = catalog_product_entity.entity_id';

    /**
     * The SQL statement to remove all product website relations for the product with the SKU passed as parameter.
     *
     * @var string
     */
    const REMOVE_PRODUCT_WEBSITE_BY_SKU = 'DELETE catalog_product_website
                                             FROM catalog_product_website
                                       INNER JOIN catalog_product_entity
                                            WHERE catalog_product_entity.sku = ?
                                              AND catalog_product_website.product_id = catalog_product_entity.entity_id';

    /**
     * The SQL statement to remove all product category relations for the product with the SKU passed as parameter.
     *
     * @var string
     */
    const REMOVE_PRODUCT_CATEGORY_BY_SKU = 'DELETE catalog_category_product
                                              FROM catalog_category_product
                                        INNER JOIN catalog_product_entity
                                             WHERE catalog_product_entity.sku = ?
                                               AND catalog_category_product.product_id = catalog_product_entity.entity_id';

    /**
     * The SQL statement to load the URL rewrites for the passed entity type and ID.
     *
     * @var string
     */
    const URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID = 'SELECT *
                                                         FROM url_rewrite
                                                        WHERE entity_type = ?
                                                          AND entity_id = ?';
}
