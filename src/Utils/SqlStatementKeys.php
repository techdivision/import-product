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

namespace TechDivision\Import\Product\Utils;

/**
 * Utility class with the SQL statements to use.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class SqlStatementKeys extends \TechDivision\Import\Utils\SqlStatementKeys
{

    /**
     * The SQL statement to load the product with the passed SKU.
     *
     * @var string
     */
    const PRODUCT = 'product';

    /**
     * The SQL statement to load the available products.
     *
     * @var string
     */
    const PRODUCTS = 'products';

    /**
     * The SQL statement to load the product website relations with the passed product/website ID.
     *
     * @var string
     */
    const PRODUCT_WEBSITE = 'product_website';

    /**
     * The SQL statement to load the product website relations for the product with the passed SKU.
     *
     * @var string
     */
    const PRODUCT_WEBSITES_BY_SKU = 'product_website.by.sku';

    /**
     * The SQL statement to load the available product datetime attributes.
     *
     * @var string
     */
    const PRODUCT_DATETIMES = 'product_datetimes';

    /**
     * The SQL statement to load the product datetime attributes with the passed entity/row and store ID.
     *
     * @var string
     */
    const PRODUCT_DATETIMES_BY_PK_AND_STORE_ID = 'product_datetimes.by.pk.and.store_id';

    /**
     * The SQL statement to load the available product decimal attributes.
     *
     * @var string
     */
    const PRODUCT_DECIMALS = 'product_decimals';

    /**
     * The SQL statement to load the product decimal attributes with the passed entity/row and store ID.
     *
     * @var string
     */
    const PRODUCT_DECIMALS_BY_PK_AND_STORE_ID = 'product_decimals.by.pk.and.store_id';

    /**
     * The SQL statement to load the available product integer attributes.
     *
     * @var string
     */
    const PRODUCT_INTS = 'product_ints';

    /**
     * The SQL statement to load the product integer attributes with the passed entity/row and store ID.
     *
     * @var string
     */
    const PRODUCT_INTS_BY_PK_AND_STORE_ID = 'product_ints.by.pk.and.store_id';

    /**
     * The SQL statement to load the available product text attributes.
     *
     * @var string
     */
    const PRODUCT_TEXTS = 'product_texts';

    /**
     * The SQL statement to load the product text attributes with the passed entity/row and store ID.
     *
     * @var string
     */
    const PRODUCT_TEXTS_BY_PK_AND_STORE_ID = 'product_texts.by.pk.and.store_id';

    /**
     * The SQL statement to load the available product varchar attributes.
     *
     * @var string
     */
    const PRODUCT_VARCHARS = 'product_varchars';

    /**
     * The SQL statement to load the product varchar attributes with the passed entity/row and store ID.
     *
     * @var string
     */
    const PRODUCT_VARCHARS_BY_PK_AND_STORE_ID = 'product_varchars.by.pk.and.store_id';

    /**
     * The SQL statement to load a product varchar attribute by the passed attribute code,
     * entity typy and store ID.
     *
     * @var string
     */
    const PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID = 'product_varchar.by.attribute_code.and.entity_type_id.and.store_id';

    /**
     * The SQL statement to load a product varchar attribute by the passed attribute code,
     * entity typy and store ID as well as the passed value.
     *
     * @var string
     */
    const PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE = 'product_varchar.by.attribute_code.and.entity_type_id.and.store_id.and.value';

    /**
     * The SQL statement to load a product varchar attribute by the passed attribute code,
     * entity typy and store ID as well as the passed entity_id/row_id from product.
     *
     * @var string
     */
    const PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_PK = 'product_varchar.by.attribute_code.and.entity_type_id.and.store_id.and.pk';

    /**
     * The SQL statement to load the category product relations with the passed product/website ID.
     *
     * @var string
     */
    const CATEGORY_PRODUCT = 'category_product';

    /**
     * The SQL statement to load the category product relations with the passed product SKU.
     *
     * @var string
     */
    const CATEGORY_PRODUCT_BY_SKU = 'category_product.by.sku';

    /**
     * The SQL statement to load the stock item with the passed product/website/stock ID.
     *
     * @var string
     */
    const STOCK_ITEM = 'stock_item';

    /**
     * The SQL statement to load the stock item with the passed product/website/stock ID.
     *
     * @var string
     */
    const STOCK_ITEM_STATUS = 'stock_item_status';

    /**
     * The SQL statement to create a product's stock status.
     *
     * @var string
     */
    const CREATE_STOCK_ITEM_STATUS = 'create.stock_item_status';

    /**
     * The SQL statement to create a product's stock status.
     *
     * @var string
     */
    const UPDATE_STOCK_ITEM_STATUS = 'update.stock_item_status';

    /**
     * The SQL statement to remove all existing stock item related with the SKU passed as parameter.
     *
     * @var string
     */
    const DELETE_STOCK_ITEM_STATUS_BY_SKU = 'delete.stock_item_status.by.sku';
    /**
     * The SQL statement to load an existing product relation with the passed parent/child ID.
     *
     * @var string
     */
    const PRODUCT_RELATION = 'product_relation';

    /**
     * The SQL statement to create new products.
     *
     * @var string
     */
    const CREATE_PRODUCT = 'create.product';

    /**
     * The SQL statement to update an existing product.
     *
     * @var string
     */
    const UPDATE_PRODUCT = 'update.product';

    /**
     * The SQL statement to create a new product website relation.
     *
     * @var string
     */
    const CREATE_PRODUCT_WEBSITE = 'create.product_website';

    /**
     * The SQL statement to create a new category product relation.
     *
     * @var string
     */
    const CREATE_CATEGORY_PRODUCT = 'create.category_product';

    /**
     * The SQL statement to update an existing category product relation.
     *
     * @var string
     */
    const UPDATE_CATEGORY_PRODUCT = 'update.category_product';

    /**
     * The SQL statement to create a new product datetime value.
     *
     * @var string
     */
    const CREATE_PRODUCT_DATETIME = 'create.product_datetime';

    /**
     * The SQL statement to update an existing product datetime value.
     *
     * @var string
     */
    const UPDATE_PRODUCT_DATETIME = 'update.product_datetime';

    /**
     * The SQL statement to delete an existing product datetime value.
     *
     * @var string
     */
    const DELETE_PRODUCT_DATETIME = 'delete.product_datetime';

    /**
     * The SQL statement to create a new product decimal value.
     *
     * @var string
     */
    const CREATE_PRODUCT_DECIMAL = 'create.product_decimal';

    /**
     * The SQL statement to create a new product relation.
     *
     * @var string
     */
    const CREATE_PRODUCT_RELATION = 'create.product_relation';

    /**
     * The SQL statement to update an existing product decimal value.
     *
     * @var string
     */
    const UPDATE_PRODUCT_DECIMAL = 'update.product_decimal';

    /**
     * The SQL statement to delete an existing product decimal value.
     *
     * @var string
     */
    const DELETE_PRODUCT_DECIMAL = 'delete.product_decimal';

    /**
     * The SQL statement to create a new product integer value.
     *
     * @var string
     */
    const CREATE_PRODUCT_INT = 'create.product_int';

    /**
     * The SQL statement to update an existing product integer value.
     *
     * @var string
     */
    const UPDATE_PRODUCT_INT = 'update.product_int';

    /**
     * The SQL statement to delete an existing product integer value.
     *
     * @var string
     */
    const DELETE_PRODUCT_INT = 'delete.product_int';

    /**
     * The SQL statement to create a new product varchar value.
     *
     * @var string
     */
    const CREATE_PRODUCT_VARCHAR = 'create.product_varchar';

    /**
     * The SQL statement to update an existing product varchar value.
     *
     * @var string
     */
    const UPDATE_PRODUCT_VARCHAR = 'update.product_varchar';

    /**
     * The SQL statement to delete an existing product varchar value.
     *
     * @var string
     */
    const DELETE_PRODUCT_VARCHAR = 'delete.product_varchar';

    /**
     * The SQL statement to create a new product text value.
     *
     * @var string
     */
    const CREATE_PRODUCT_TEXT = 'create.product_text';

    /**
     * The SQL statement to update an existing product text value.
     *
     * @var string
     */
    const UPDATE_PRODUCT_TEXT = 'update.product_text';

    /**
     * The SQL statement to delete an existing product text value.
     *
     * @var string
     */
    const DELETE_PRODUCT_TEXT = 'delete.product_text';

    /**
     * The SQL statement to create a product's stock status.
     *
     * @var string
     */
    const CREATE_STOCK_ITEM = 'create.stock_item';

    /**
     * The SQL statement to create a product's stock status.
     *
     * @var string
     */
    const UPDATE_STOCK_ITEM = 'update.stock_item';

    /**
     * The SQL statement to remove a existing product.
     *
     * @var string
     */
    const DELETE_PRODUCT = 'delete.product';

    /**
     * The SQL statement to remove all existing stock item related with the SKU passed as parameter.
     *
     * @var string
     */
    const DELETE_STOCK_ITEM_BY_SKU = 'delete.stock_item.by.sku';

    /**
     * The SQL statement to remove the product website relation for the product/website with the IDs passed as parameter.
     *
     * @var string
     */
    const DELETE_PRODUCT_WEBSITE = 'delete.product_website';

    /**
     * The SQL statement to remove all product website relations for the product with the SKU passed as parameter.
     *
     * @var string
     */
    const DELETE_PRODUCT_WEBSITE_BY_SKU = 'delete.product_website.by.sku';

    /**
     * The SQL statement to remove all product category relations for the product.
     *
     * @var string
     */
    const DELETE_CATEGORY_PRODUCT = 'delete.category_product';

    /**
     * The SQL statement to remove all product category relations for the product with the SKU passed as parameter.
     *
     * @var string
     */
    const DELETE_CATEGORY_PRODUCT_BY_SKU = 'delete.category_product.by.sku';
}
