<?php

/**
 * TechDivision\Import\Product\Services\ProductProcessor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Product\Services;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductProcessor implements ProductProcessorInterface
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
     * @var \TechDivision\Import\Product\Actions\ProductTextAction
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
     * @var \TechDivision\Import\Product\Actions\ProductDatetiemAction
     */
    protected $productDatetimeAction;

    /**
     * The action for product website CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductWebsiteAction
     */
    protected $productWebsiteAction;

    /**
     * The action for product category CRUD methods.
     *
     * @var \TechDivision\Import\Product\Actions\ProductCategoryAction
     */
    protected $productCategoryAction;

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
     * Set's the action with the product category CRUD methods.
     *
     * @param \TechDivision\Import\Product\Actions\ProductCategoryAction $productCategoryAction The action with the product category CRUD methods
     *
     * @return void
     */
    public function setProductCategoryAction($productCategoryAction)
    {
        $this->productCategoryAction = $productCategoryAction;
    }

    /**
     * Return's the action with the product category CRUD methods.
     *
     * @return \TechDivision\Import\Product\Actions\ProductCategoryAction The action instance
     */
    public function getProductCategoryAction()
    {
        return $this->productCategoryAction;
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
     * Persist's the passed product data and return's the ID.
     *
     * @param array $product The product data to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistProduct($product)
    {
        return $this->getProductAction()->persist($product);
    }

    /**
     * Persist's the passed product varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductVarcharAttribute($attribute)
    {
        $this->getProductVarcharAction()->persist($attribute);
    }

    /**
     * Persist's the passed product integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductIntAttribute($attribute)
    {
        $this->getProductIntAction()->persist($attribute);
    }

    /**
     * Persist's the passed product decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductDecimalAttribute($attribute)
    {
        $this->getProductDecimalAction()->persist($attribute);
    }

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductDatetimeAttribute($attribute)
    {
        $this->getProductDatetimeAction()->persist($attribute);
    }

    /**
     * Persist's the passed product text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductTextAttribute($attribute)
    {
        $this->getProductTextAction()->persist($attribute);
    }

    /**
     * Persist's the passed product website data and return's the ID.
     *
     * @param array $productWebsite The product website data to persist
     *
     * @return void
     */
    public function persistProductWebsite($productWebsite)
    {
        $this->getProductWebsiteAction()->persist($productWebsite);
    }

    /**
     * Persist's the passed product category data and return's the ID.
     *
     * @param array $productWebsite The product category data to persist
     *
     * @return void
     */
    public function persistProductCategory($productCategory)
    {
        $this->getProductCategoryAction()->persist($productCategory);
    }

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array $stockItem The stock item data to persist
     *
     * @return void
     */
    public function persistStockItem($stockItem)
    {
        $this->getStockItemAction()->persist($stockItem);
    }

    /**
     * Persist's the passed stock status data and return's the ID.
     *
     * @param array $stockItem The stock status data to persist
     *
     * @return void
     */
    public function persistStockStatus($stockStatus)
    {
        $this->getStockStatusAction()->persist($stockStatus);
    }
}
