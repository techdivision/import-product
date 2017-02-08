<?php

/**
 * TechDivision\Import\Product\Services\ProductProcessorInterface
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

namespace TechDivision\Import\Product\Services;

/**
 * Interface for a product bunch processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
interface ProductBunchProcessorInterface extends ProductProcessorInterface
{

    /**
     * Return's the action with the product CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductAction The action instance
     */
    public function getProductAction();

    /**
     * Return's the action with the product varchar attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductVarcharAction The action instance
     */
    public function getProductVarcharAction();

    /**
     * Return's the action with the product text attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductTextAction The action instance
     */
    public function getProductTextAction();

    /**
     * Return's the action with the product int attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductIntAction The action instance
     */
    public function getProductIntAction();

    /**
     * Return's the action with the product decimal attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductDecimalAction The action instance
     */
    public function getProductDecimalAction();

    /**
     * Return's the action with the product datetime attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductDatetimeAction The action instance
     */
    public function getProductDatetimeAction();

    /**
     * Return's the action with the product website CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductWebsiteAction The action instance
     */
    public function getProductWebsiteAction();

    /**
     * Return's the action with the category product relation CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\CategoryProductAction The action instance
     */
    public function getCategoryProductAction();
    /**
     * Return's the action with the stock item CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\StockItemAction The action instance
     */
    public function getStockItemAction();

    /**
     * Return's the action with the stock status CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\StockStatusAction The action instance
     */
    public function getStockStatusAction();

    /**
     * Return's the action with the stock status CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\UrlRewriteAction The action instance
     */
    public function getUrlRewriteAction();

    /**
     * Return's the action with the URL rewrite product category CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\UrlRewriteProductCategoryAction The action instance
     */
    public function getUrlRewriteProductCategoryAction();

    /**
     * Return's the repository to load the products with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductRepository The repository instance
     */
    public function getProductRepository();

    /**
     * Return's the repository to load the product website relations with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductWebsiteRepository The repository instance
     */
    public function getProductWebsiteRepository();

    /**
     * Return's the repository to load the category product relations with.
     *
     * @return \TechDivision\Import\Product\Repositories\CategoryProductRepository The repository instance
     */
    public function getCategoryProductRepository();

    /**
     * Return's the repository to load the URL rewrites with.
     *
     * @return \TechDivision\Import\Product\Repositories\UrlRewriteRepository The repository instance
     */
    public function getUrlRewriteRepository();

    /**
     * Return's the repository to load the URL rewrite product category relations with.
     *
     * @return \TechDivision\Import\Product\Repositories\UrlRewriteProductCategoryRepository The repository instance
     */
    public function getUrlRewriteProductCategoryRepository();

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);

    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to laod the rewrites for
     *
     * @return array The URL rewrites
     */
    public function getUrlRewritesByEntityTypeAndEntityId($entityType, $entityId);

    /**
     * Load's and return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to load
     *
     * @return array The product
     */
    public function loadProduct($sku);

    /**
     * Load's and return's the product website with the passed product and website ID.
     *
     * @param string $productId The product ID of the relation
     * @param string $websiteId The website ID of the relation
     *
     * @return array The product website
     */
    public function loadProductWebsite($productId, $websiteId);

    /**
     * Return's the category product relation with the passed category/product ID.
     *
     * @param integer $categoryId The category ID of the category product relation to return
     * @param integer $productId  The product ID of the category product relation to return
     *
     * @return array The category product relation
     */
    public function loadCategoryProduct($categoryId, $productId);

    /**
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock status to load
     * @param integer $websiteId The website ID of the stock status to load
     * @param integer $stockId   The stock ID of the stock status to load
     *
     * @return array The stock status
     */
    public function loadStockStatus($productId, $websiteId, $stockId);

    /**
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    public function loadStockItem($productId, $websiteId, $stockId);

    /**
     * Load's and return's the datetime attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The datetime attribute
     */
    public function loadProductDatetimeAttribute($entityId, $attributeId, $storeId);

    /**
     * Load's and return's the decimal attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The decimal attribute
     */
    public function loadProductDecimalAttribute($entityId, $attributeId, $storeId);

    /**
     * Load's and return's the integer attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The integer attribute
     */
    public function loadProductIntAttribute($entityId, $attributeId, $storeId);

    /**
     * Load's and return's the text attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The text attribute
     */
    public function loadProductTextAttribute($entityId, $attributeId, $storeId);

    /**
     * Load's and return's the varchar attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The varchar attribute
     */
    public function loadProductVarcharAttribute($entityId, $attributeId, $storeId);

    /**
     * Return's the URL rewrite product category relation for the passed
     * product and category ID.
     *
     * @param integer $productId  The product ID to load the URL rewrite product category relation for
     * @param integer $categoryId The category ID to load the URL rewrite product category relation for
     *
     * @return array|null The URL rewrite product category relations
     */
    public function loadUrlRewriteProductCategory($productId, $categoryId);

    /**
     * Persist's the passed product data and return's the ID.
     *
     * @param array       $product The product data to persist
     * @param string|null $name    The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function persistProduct($product, $name = null);

    /**
     * Persist's the passed product varchar attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductVarcharAttribute($attribute, $name = null);

    /**
     * Persist's the passed product integer attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductIntAttribute($attribute, $name = null);

    /**
     * Persist's the passed product decimal attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductDecimalAttribute($attribute, $name = null);

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductDatetimeAttribute($attribute, $name = null);

    /**
     * Persist's the passed product text attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductTextAttribute($attribute, $name = null);

    /**
     * Persist's the passed product website data and return's the ID.
     *
     * @param array       $productWebsite The product website data to persist
     * @param string|null $name           The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductWebsite($productWebsite, $name = null);

    /**
     * Persist's the passed category product relation.
     *
     * @param array       $categoryProduct The category product relation to persist
     * @param string|null $name            The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryProduct($categoryProduct, $name = null);

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array       $stockItem The stock item data to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistStockItem($stockItem, $name = null);

    /**
     * Persist's the passed stock status data and return's the ID.
     *
     * @param array       $stockStatus The stock status data to persist
     * @param string|null $name        The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistStockStatus($stockStatus, $name = null);

    /**
     * Persist's the URL write with the passed data.
     *
     * @param array       $row  The URL rewrite to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function persistUrlRewrite($row, $name = null);

    /**
     * Persist's the URL rewrite product => category relation with the passed data.
     *
     * @param array       $row  The URL rewrite product => category relation to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistUrlRewriteProductCategory($row, $name = null);

    /**
     * Delete's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProduct($row, $name = null);

    /**
     * Delete's the URL rewrite with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteUrlRewrite($row, $name = null);

    /**
     * Delete's the stock item(s) with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteStockItem($row, $name = null);

    /**
     * Delete's the stock status with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteStockStatus($row, $name = null);

    /**
     * Delete's the product website relations with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductWebsite($row, $name = null);

    /**
     * Delete's the category product relations with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategoryProduct($row, $name = null);
}
