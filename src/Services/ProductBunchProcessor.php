<?php

/**
 * TechDivision\Import\Product\Services\ProductBunchProcessor
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
 * The product bunch processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductBunchProcessor implements ProductBunchProcessorInterface
{

    /**
     * A PDO connection initialized with the values from the Doctrine EntityManager.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * The repository to access EAV attribute option values.
     *
     * @var \TechDivision\Import\Product\Repositories\EavAttributeOptionValueRepository
     */
    protected $eavAttributeOptionValueRepository;

    /**
     * The action for product CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductAction
     */
    protected $productAction;

    /**
     * The action for product varchar attribute CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductVarcharAction
     */
    protected $productVarcharAction;

    /**
     * The action for product text attribute CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductTextAction
     */
    protected $productTextAction;

    /**
     * The action for product int attribute CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductIntAction
     */
    protected $productIntAction;

    /**
     * The action for product decimal attribute CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductDecimalAction
     */
    protected $productDecimalAction;

    /**
     * The action for product datetime attribute CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductDatetimeAction
     */
    protected $productDatetimeAction;

    /**
     * The action for product website CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductWebsiteAction
     */
    protected $productWebsiteAction;

    /**
     * The action for category product relation CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\CategoryProductAction
     */
    protected $categoryProductAction;

    /**
     * The action for stock item CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\StockItemAction
     */
    protected $stockItemAction;

    /**
     * The action for stock status CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\StockStatusAction
     */
    protected $stockStatusAction;

    /**
     * The action for URL rewrite CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\UrlRewriteAction
     */
    protected $urlRewriteAction;

    /**
     * The repository to load the products with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * The repository to load the product website relations with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductWebsiteRepository
     */
    protected $productWebsiteRepository;

    /**
     * The repository to load the product datetime attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductDatetimeRepository
     */
    protected $productDatetimeRepository;

    /**
     * The repository to load the product decimal attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductDecimalRepository
     */
    protected $productDecimalRepository;

    /**
     * The repository to load the product integer attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductIntRepository
     */
    protected $productIntRepository;

    /**
     * The repository to load the product text attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductTextRepository
     */
    protected $productTextRepository;

    /**
     * The repository to load the product varchar attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductVarcharRepository
     */
    protected $productVarcharRepository;

    /**
     * The repository to load the category product relations with.
     *
     * @var \TechDivision\Import\Product\Repositories\CategoryProductRepository
     */
    protected $categoryProductRepository;

    /**
     * The repository to load the stock status with.
     *
     * @var \TechDivision\Import\Product\Repositories\StockStatusRepository
     */
    protected $stockStatusRepository;

    /**
     * The repository to load the stock item with.
     *
     * @var \TechDivision\Import\Product\Repositories\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * The repository to load the URL rewrites with.
     *
     * @var \TechDivision\Import\Product\Repositories\UrlRewriteRepository
     */
    protected $urlRewriteRepository;

    /**
     * Set's the passed connection.
     *
     * @param \PDO $connection The connection to set
     *
     * @return void
     */
    public function setConnection(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return's the connection.
     *
     * @return \PDO The connection instance
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO
     * object instance are not committed until you end the transaction by calling ProductProcessor::commit().
     * Calling ProductProcessor::rollBack() will roll back all changes to the database and return the connection
     * to autocommit mode.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.begintransaction.php
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commits a transaction, returning the database connection to autocommit mode until the next call to
     * ProductProcessor::beginTransaction() starts a new transaction.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.commit.php
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rolls back the current transaction, as initiated by ProductProcessor::beginTransaction().
     *
     * If the database was set to autocommit mode, this function will restore autocommit mode after it has
     * rolled back the transaction.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition
     * language (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit
     * COMMIT will prevent you from rolling back any other changes within the transaction boundary.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     * @link http://php.net/manual/en/pdo.rollback.php
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Set's the repository to access EAV attribute option values.
     *
     * @param \TechDivision\Import\Product\Repositories\EavAttributeOptionValueRepository $eavAttributeOptionValueRepository The repository to access EAV attribute option values
     *
     * @return void
     */
    public function setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository)
    {
        $this->eavAttributeOptionValueRepository = $eavAttributeOptionValueRepository;
    }

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Product\Repositories\EavAttributeOptionValueRepository The repository instance
     */
    public function getEavAttributeOptionValueRepository()
    {
        return $this->eavAttributeOptionValueRepository;
    }

    /**
     * Set's the action with the product CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductAction $productAction The action with the product CRUD methods
     *
     * @return void
     */
    public function setProductAction($productAction)
    {
        $this->productAction = $productAction;
    }

    /**
     * Return's the action with the product CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductAction The action instance
     */
    public function getProductAction()
    {
        return $this->productAction;
    }

    /**
     * Set's the action with the product varchar attribute CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductVarcharAction $productVarcharAction The action with the product varchar attriute CRUD methods
     *
     * @return void
     */
    public function setProductVarcharAction($productVarcharAction)
    {
        $this->productVarcharAction = $productVarcharAction;
    }

    /**
     * Return's the action with the product varchar attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductVarcharAction The action instance
     */
    public function getProductVarcharAction()
    {
        return $this->productVarcharAction;
    }

    /**
     * Set's the action with the product text attribute CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductTextAction $productTextAction The action with the product text attriute CRUD methods
     *
     * @return void
     */
    public function setProductTextAction($productTextAction)
    {
        $this->productTextAction = $productTextAction;
    }

    /**
     * Return's the action with the product text attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductTextAction The action instance
     */
    public function getProductTextAction()
    {
        return $this->productTextAction;
    }

    /**
     * Set's the action with the product int attribute CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductIntAction $productIntAction The action with the product int attriute CRUD methods
     *
     * @return void
     */
    public function setProductIntAction($productIntAction)
    {
        $this->productIntAction = $productIntAction;
    }

    /**
     * Return's the action with the product int attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductIntAction The action instance
     */
    public function getProductIntAction()
    {
        return $this->productIntAction;
    }

    /**
     * Set's the action with the product decimal attribute CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductDecimalAction $productDecimalAction The action with the product decimal attriute CRUD methods
     *
     * @return void
     */
    public function setProductDecimalAction($productDecimalAction)
    {
        $this->productDecimalAction = $productDecimalAction;
    }

    /**
     * Return's the action with the product decimal attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductDecimalAction The action instance
     */
    public function getProductDecimalAction()
    {
        return $this->productDecimalAction;
    }

    /**
     * Set's the action with the product datetime attribute CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductDatetimeAction $productDatetimeAction The action with the product datetime attriute CRUD methods
     *
     * @return void
     */
    public function setProductDatetimeAction($productDatetimeAction)
    {
        $this->productDatetimeAction = $productDatetimeAction;
    }

    /**
     * Return's the action with the product datetime attribute CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductDatetimeAction The action instance
     */
    public function getProductDatetimeAction()
    {
        return $this->productDatetimeAction;
    }

    /**
     * Set's the action with the product website CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductWebsiteAction $productWebsiteAction The action with the product website CRUD methods
     *
     * @return void
     */
    public function setProductWebsiteAction($productWebsiteAction)
    {
        $this->productWebsiteAction = $productWebsiteAction;
    }

    /**
     * Return's the action with the product website CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductWebsiteAction The action instance
     */
    public function getProductWebsiteAction()
    {
        return $this->productWebsiteAction;
    }

    /**
     * Set's the action with the category product relation CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductCategoryAction $categoryProductAction The action with the category product relation CRUD methods
     *
     * @return void
     */
    public function setCategoryProductAction($categoryProductAction)
    {
        $this->categoryProductAction = $categoryProductAction;
    }

    /**
     * Return's the action with the category product relation CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\CategoryProductAction The action instance
     */
    public function getCategoryProductAction()
    {
        return $this->categoryProductAction;
    }

    /**
     * Set's the action with the stock item CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\StockItemAction $stockItemAction The action with the stock item CRUD methods
     *
     * @return void
     */
    public function setStockItemAction($stockItemAction)
    {
        $this->stockItemAction = $stockItemAction;
    }

    /**
     * Return's the action with the stock item CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\StockItemAction The action instance
     */
    public function getStockItemAction()
    {
        return $this->stockItemAction;
    }

    /**
     * Set's the action with the stock status CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\StockStatusAction $stockStatusAction The action with the stock status CRUD methods
     *
     * @return void
     */
    public function setStockStatusAction($stockStatusAction)
    {
        $this->stockStatusAction = $stockStatusAction;
    }

    /**
     * Return's the action with the stock status CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\StockStatusAction The action instance
     */
    public function getStockStatusAction()
    {
        return $this->stockStatusAction;
    }

    /**
     * Set's the action with the URL rewrite CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\UrlRewriteAction $urlRewriteAction The action with the URL rewrite CRUD methods
     *
     * @return void
     */
    public function setUrlRewriteAction($urlRewriteAction)
    {
        $this->urlRewriteAction = $urlRewriteAction;
    }

    /**
     * Return's the action with the stock status CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\UrlRewriteAction The action instance
     */
    public function getUrlRewriteAction()
    {
        return $this->urlRewriteAction;
    }

    /**
     * Set's the repository to load the products with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductRepository $productRepository The repository instance
     *
     * @return void
     */
    public function setProductRepository($productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Return's the repository to load the products with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductRepository The repository instance
     */
    public function getProductRepository()
    {
        return $this->productRepository;
    }

    /**
     * Set's the repository to load the product website relations with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductWebsiteRepository $productWebsiteRepository The repository instance
     *
     * @return void
     */
    public function setProductWebsiteRepository($productWebsiteRepository)
    {
        $this->productWebsiteRepository = $productWebsiteRepository;
    }

    /**
     * Return's the repository to load the product website relations with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductWebsiteRepository The repository instance
     */
    public function getProductWebsiteRepository()
    {
        return $this->productWebsiteRepository;
    }

    /**
     * Set's the repository to load the product datetime attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductDatetimeRepository $productDatetimeRepository The repository instance
     *
     * @return void
     */
    public function setProductDatetimeRepository($productDatetimeRepository)
    {
        $this->productDatetimeRepository = $productDatetimeRepository;
    }

    /**
     * Return's the repository to load the product datetime attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductDatetimeRepository The repository instance
     */
    public function getProductDatetimeRepository()
    {
        return $this->productDatetimeRepository;
    }

    /**
     * Set's the repository to load the product decimal attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductDecimalRepository $productDecimalRepository The repository instance
     *
     * @return void
     */
    public function setProductDecimalRepository($productDecimalRepository)
    {
        $this->productDecimalRepository = $productDecimalRepository;
    }

    /**
     * Return's the repository to load the product decimal attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductDecimalRepository The repository instance
     */
    public function getProductDecimalRepository()
    {
        return $this->productDecimalRepository;
    }

    /**
     * Set's the repository to load the product integer attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductIntRepository $productIntRepository The repository instance
     *
     * @return void
     */
    public function setProductIntRepository($productIntRepository)
    {
        $this->productIntRepository = $productIntRepository;
    }

    /**
     * Return's the repository to load the product integer attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductIntRepository The repository instance
     */
    public function getProductIntRepository()
    {
        return $this->productIntRepository;
    }

    /**
     * Set's the repository to load the product text attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductTextRepository $productTextRepository The repository instance
     *
     * @return void
     */
    public function setProductTextRepository($productTextRepository)
    {
        $this->productTextRepository = $productTextRepository;
    }

    /**
     * Return's the repository to load the product text attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductTextRepository The repository instance
     */
    public function getProductTextRepository()
    {
        return $this->productTextRepository;
    }

    /**
     * Set's the repository to load the product varchar attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductVarcharRepository $productVarcharRepository The repository instance
     *
     * @return void
     */
    public function setProductVarcharRepository($productVarcharRepository)
    {
        $this->productVarcharRepository = $productVarcharRepository;
    }

    /**
     * Return's the repository to load the product varchar attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductVarcharRepository The repository instance
     */
    public function getProductVarcharRepository()
    {
        return $this->productVarcharRepository;
    }

    /**
     * Set's the repository to load the category product relations with.
     *
     * @param \TechDivision\Import\Product\Repositories\CategoryProductRepository $productWebsiteRepository The repository instance
     *
     * @return void
     */
    public function setCategoryProductRepository($categoryProductRepository)
    {
        $this->categoryProductRepository = $categoryProductRepository;
    }

    /**
     * Return's the repository to load the category product relations with.
     *
     * @return \TechDivision\Import\Product\Repositories\CategoryProductRepository The repository instance
     */
    public function getCategoryProductRepository()
    {
        return $this->categoryProductRepository;
    }

    /**
     * Set's the repository to load the stock status with.
     *
     * @param \TechDivision\Import\Product\Repositories\StockStatusRepository $stockStatusRepository The repository instance
     *
     * @return void
     */
    public function setStockStatusRepository($stockStatusRepository)
    {
        $this->stockStatusRepository = $stockStatusRepository;
    }

    /**
     * Return's the repository to load the stock status with.
     *
     * @return \TechDivision\Import\Product\Repositories\StockStatusRepository The repository instance
     */
    public function getStockStatusRepository()
    {
        return $this->stockStatusRepository;
    }

    /**
     * Set's the repository to load the stock items with.
     *
     * @param \TechDivision\Import\Product\Repositories\StockItemRepository $stockItemRepository The repository instance
     *
     * @return void
     */
    public function setStockItemRepository($stockItemRepository)
    {
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * Return's the repository to load the stock items with.
     *
     * @return \TechDivision\Import\Product\Repositories\StockItemRepository The repository instance
     */
    public function getStockItemRepository()
    {
        return $this->stockItemRepository;
    }

    /**
     * Set's the repository to load the URL rewrites with.
     *
     * @param \TechDivision\Import\Product\Repositories\UrlRewriteRepository $urlRewriteRepository The repository instance
     *
     * @return void
     */
    public function setUrlRewriteRepository($urlRewriteRepository)
    {
        $this->urlRewriteRepository = $urlRewriteRepository;
    }

    /**
     * Return's the repository to load the URL rewrites with.
     *
     * @return \TechDivision\Import\Product\Repositories\UrlRewriteRepository The repository instance
     */
    public function getUrlRewriteRepository()
    {
        return $this->urlRewriteRepository;
    }

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {
        return $this->getEavAttributeOptionValueRepository()->findEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);
    }

    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to laod the rewrites for
     *
     * @return array The URL rewrites
     */
    public function getUrlRewritesByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->getUrlRewriteRepository()->findAllByEntityTypeAndEntityId($entityType, $entityId);
    }

    /**
     * Load's and return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to load
     *
     * @return array The product
     */
    public function loadProduct($sku)
    {
        return $this->getProductRepository()->findOneBySku($sku);
    }

    /**
     * Load's and return's the product website relation with the passed product and website ID.
     *
     * @param string $productId The product ID of the relation
     * @param string $websiteId The website ID of the relation
     *
     * @return array The product website
     */
    public function loadProductWebsite($productId, $websiteId)
    {
        return $this->getProductWebsiteRepository()->findOneByProductIdAndWebsite($productId, $websiteId);
    }

    /**
     * Return's the category product relation with the passed category/product ID.
     *
     * @param integer $categoryId The category ID of the category product relation to return
     * @param integer $productId  The product ID of the category product relation to return
     *
     * @return array The category product relation
     */
    public function loadCategoryProduct($categoryId, $productId)
    {
        return $this->getCategoryProductRepository()->findOneByCategoryIdAndProductId($categoryId, $productId);
    }

    /**
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock status to load
     * @param integer $websiteId The website ID of the stock status to load
     * @param integer $stockId   The stock ID of the stock status to load
     *
     * @return array The stock status
     */
    public function loadStockStatus($productId, $websiteId, $stockId)
    {
        return $this->getStockStatusRepository()->findOneByProductIdAndWebsiteIdAndStockId($productId, $websiteId, $stockId);
    }

    /**
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    public function loadStockItem($productId, $websiteId, $stockId)
    {
        return $this->getStockItemRepository()->findOneByProductIdAndWebsiteIdAndStockId($productId, $websiteId, $stockId);
    }

    /**
     * Load's and return's the datetime attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The datetime attribute
     */
    public function loadProductDatetimeAttribute($entityId, $attributeId, $storeId)
    {
        return  $this->getProductDatetimeRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the decimal attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The decimal attribute
     */
    public function loadProductDecimalAttribute($entityId, $attributeId, $storeId)
    {
        return  $this->getProductDecimalRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the integer attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The integer attribute
     */
    public function loadProductIntAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getProductIntRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the text attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The text attribute
     */
    public function loadProductTextAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getProductTextRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the varchar attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The varchar attribute
     */
    public function loadProductVarcharAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getProductVarcharRepository()->findOneByEntityIdAndAttributeIdAndStoreId($entityId, $attributeId, $storeId);
    }

    /**
     * Persist's the passed product data and return's the ID.
     *
     * @param array       $product The product data to persist
     * @param string|null $name    The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function persistProduct($product, $name = null)
    {
        return $this->getProductAction()->persist($product, $name);
    }

    /**
     * Persist's the passed product varchar attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductVarcharAttribute($attribute, $name = null)
    {
        $this->getProductVarcharAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed product integer attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductIntAttribute($attribute, $name = null)
    {
        $this->getProductIntAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed product decimal attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductDecimalAttribute($attribute, $name = null)
    {
        $this->getProductDecimalAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductDatetimeAttribute($attribute, $name = null)
    {
        $this->getProductDatetimeAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed product text attribute.
     *
     * @param array       $attribute The attribute to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductTextAttribute($attribute, $name = null)
    {
        $this->getProductTextAction()->persist($attribute, $name);
    }

    /**
     * Persist's the passed product website data and return's the ID.
     *
     * @param array       $productWebsite The product website data to persist
     * @param string|null $name           The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistProductWebsite($productWebsite, $name = null)
    {
        $this->getProductWebsiteAction()->persist($productWebsite, $name);
    }

    /**
     * Persist's the passed category product relation.
     *
     * @param array       $categoryProduct The category product relation to persist
     * @param string|null $name            The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistCategoryProduct($categoryProduct, $name = null)
    {
        $this->getCategoryProductAction()->persist($categoryProduct, $name);
    }

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array       $stockItem The stock item data to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistStockItem($stockItem, $name = null)
    {
        $this->getStockItemAction()->persist($stockItem, $name);
    }

    /**
     * Persist's the passed stock status data and return's the ID.
     *
     * @param array       $stockStatus The stock status data to persist
     * @param string|null $name        The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistStockStatus($stockStatus, $name = null)
    {
        $this->getStockStatusAction()->persist($stockStatus, $name);
    }

    /**
     * Persist's the URL write with the passed data.
     *
     * @param array       $row  The URL rewrite to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistUrlRewrite($row, $name = null)
    {
        $this->getUrlRewriteAction()->persist($row, $name);
    }

    /**
     * Delete's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProduct($row, $name = null)
    {
        $this->getProductAction()->delete($row, $name);
    }

    /**
     * Delete's the URL rewrite with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteUrlRewrite($row, $name = null)
    {
        $this->getUrlRewriteAction()->delete($row, $name);
    }

    /**
     * Delete's the stock item(s) with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteStockItem($row, $name = null)
    {
        $this->getStockItemAction()->delete($row, $name);
    }

    /**
     * Delete's the stock status with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteStockStatus($row, $name = null)
    {
        $this->getStockStatusAction()->delete($row, $name);
    }

    /**
     * Delete's the product website relations with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductWebsite($row, $name = null)
    {
        $this->getProductWebsiteAction()->delete($row, $name);
    }

    /**
     * Delete's the category product relations with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategoryProduct($row, $name = null)
    {
        $this->getCategoryProductAction()->delete($row, $name);
    }
}
