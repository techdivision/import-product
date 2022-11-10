<?php

/**
 * TechDivision\Import\Product\Services\ProductBunchProcessorInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Services;

use TechDivision\Import\Services\EavAwareProcessorInterface;
use TechDivision\Import\Services\UrlKeyAwareProcessorInterface;

/**
 * Interface for a product bunch processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
interface ProductBunchProcessorInterface extends ProductProcessorInterface, EavAwareProcessorInterface, UrlKeyAwareProcessorInterface
{

    /**
     * Return's the raw entity loader instance.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The raw entity loader instance
     */
    public function getRawEntityLoader();

    /**
     * Return's the action with the product CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductAction();

    /**
     * Return's the action with the product varchar attribute CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductVarcharAction();

    /**
     * Return's the action with the product text attribute CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductTextAction();

    /**
     * Return's the action with the product int attribute CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductIntAction();

    /**
     * Return's the action with the product decimal attribute CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductDecimalAction();

    /**
     * Return's the action with the product datetime attribute CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductDatetimeAction();

    /**
     * Return's the action with the product website CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getProductWebsiteAction();

    /**
     * Return's the action with the category product relation CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getCategoryProductAction();
    /**
     * Return's the action with the stock item CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getStockItemAction();

    /**
     * Return's the action with the stock status CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getUrlRewriteAction();

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface The repository instance
     */
    public function getEavAttributeOptionValueRepository();

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepositoryInterface The repository instance
     */
    public function getEavAttributeRepository();

    /**
     * Return's the repository to access EAV entity types.
     *
     * @return \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface The repository instance
     */
    public function getEavEntityTypeRepository();

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
     * Return's the repository to load the stock items with.
     *
     * @return \TechDivision\Import\Product\Repositories\StockItemRepository The repository instance
     */
    public function getStockItemRepository();

    /**
     * Return's the assembler to load the product attributes with.
     *
     * @return \TechDivision\Import\Product\Assemblers\ProductAttributeAssemblerInterface The assembler instance
     */
    public function getProductAttributeAssembler();

    /**
     * Return's an array with the available EAV attributes for the passed is user defined flag.
     *
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed flag
     */
    public function getEavAttributeByIsUserDefined($isUserDefined = 1);

    /**
     * Return's the category product relations for the product with the passed SKU.
     *
     * @param string $sku The product SKU to load the category relations for
     *
     * @return array The category product relations for the product with the passed SKU
     */
    public function getCategoryProductsBySku($sku);

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    public function getProductAttributesByPrimaryKeyAndStoreId($pk, $storeId);

    /**
     * Load's and return's a raw entity without primary key but the mandatory members only and nulled values.
     *
     * @param string $entityTypeCode The entity type code to return the raw entity for
     * @param array  $data           An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    public function loadRawEntity($entityTypeCode, array $data = array());

    /**
     * Load's and return's the EAV attribute option value with the passed entity type ID, code, store ID and value.
     *
     * @param string  $entityTypeId  The entity type ID of the EAV attribute to load the option value for
     * @param string  $attributeCode The code of the EAV attribute option to load
     * @param integer $storeId       The store ID of the attribute option to load
     * @param string  $value         The value of the attribute option to load
     *
     * @return array The EAV attribute option value
     */
    public function loadAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value);

    /**
     * Load's and return's the available products.
     *
     * @return array The available products
     */
    public function loadProducts();

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
     * Load's and return's the product website relations for the product with the passed SKU.
     *
     * @param string $sku The SKU to of the product to load the product website relations for
     *
     * @return array The product website relations
     */
    public function loadProductWebsitesBySku($sku);

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
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    public function loadStockItem($productId, $websiteId, $stockId);

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $value         The value of the varchar attribute
     *
     * @return array|null The varchar attribute
     */
    public function loadProductVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value);

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $pk            The primary key of the product
     *
     * @return array|null The varchar attribute
     */
    public function loadProductVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPK($attributeCode, $entityTypeId, $storeId, $pk);

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
     * @return string The ID of the persisted attribute
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
     * Persist's the passed stock item status data and return's the ID.
     *
     * @param array       $stockItem The stock item data to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistStockItemStatus($stockItem, $name = null);

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

    /**
     * Delete's the product datetime attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductDatetimeAttribute($row, $name = null);

    /**
     * Delete's the product decimal attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductDecimalAttribute($row, $name = null);

    /**
     * Delete's the product integer attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductIntAttribute($row, $name = null);

    /**
     * Delete's the product text attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductTextAttribute($row, $name = null);

    /**
     * Delete's the product varchar attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductVarcharAttribute($row, $name = null);

    /**
     * Clean-Up the repositories to free memory.
     *
     * @return void
     */
    public function cleanUp();
}
