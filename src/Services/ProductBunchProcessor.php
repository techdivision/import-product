<?php

/**
 * TechDivision\Import\Product\Services\ProductBunchProcessor
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

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Dbal\Actions\ActionInterface;
use TechDivision\Import\Dbal\Connection\ConnectionInterface;
use TechDivision\Import\Repositories\UrlRewriteRepositoryInterface;
use TechDivision\Import\Repositories\EavAttributeRepositoryInterface;
use TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductRepositoryInterface;
use TechDivision\Import\Product\Repositories\StockItemRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductIntRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductTextRepositoryInterface;
use TechDivision\Import\Product\Assemblers\ProductAttributeAssemblerInterface;
use TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductWebsiteRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductDatetimeRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface;
use TechDivision\Import\Product\Repositories\CategoryProductRepositoryInterface;

/**
 * The product bunch processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductBunchProcessor implements ProductBunchProcessorInterface
{

    /**
     * A PDO connection initialized with the values from the Doctrine EntityManager.
     *
     * @var \TechDivision\Import\Dbal\Connection\ConnectionInterface
     */
    protected $connection;

    /**
     * The repository to access EAV attribute option values.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface
     */
    protected $eavAttributeOptionValueRepository;

    /**
     * The repository to access EAV attributes.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeRepositoryInterface
     */
    protected $eavAttributeRepository;

    /**
     * The action for product CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productAction;

    /**
     * The action for product varchar attribute CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productVarcharAction;

    /**
     * The action for product text attribute CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productTextAction;

    /**
     * The action for product int attribute CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productIntAction;

    /**
     * The action for product decimal attribute CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productDecimalAction;

    /**
     * The action for product datetime attribute CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productDatetimeAction;

    /**
     * The action for product website CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $productWebsiteAction;

    /**
     * The action for category product relation CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $categoryProductAction;

    /**
     * The action for stock item CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $stockItemAction;

    /**
     * The action for URL rewrite CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    protected $urlRewriteAction;

    /**
     * The repository to load the products with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * The repository to load the product website relations with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductWebsiteRepositoryInterface
     */
    protected $productWebsiteRepository;

    /**
     * The repository to load the product datetime attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductDatetimeRepositoryInterface
     */
    protected $productDatetimeRepository;

    /**
     * The repository to load the product decimal attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface
     */
    protected $productDecimalRepository;

    /**
     * The repository to load the product integer attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductIntRepositoryInterface
     */
    protected $productIntRepository;

    /**
     * The repository to load the product text attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductTextRepositoryInterface
     */
    protected $productTextRepository;

    /**
     * The repository to load the product varchar attribute with.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface
     */
    protected $productVarcharRepository;

    /**
     * The repository to load the category product relations with.
     *
     * @var \TechDivision\Import\Product\Repositories\CategoryProductRepositoryInterface
     */
    protected $categoryProductRepository;

    /**
     * The repository to load the stock item with.
     *
     * @var \TechDivision\Import\Product\Repositories\StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * The assembler to load the product attributes with.
     *
     * @var \TechDivision\Import\Product\Assemblers\ProductAttributeAssemblerInterface
     */
    protected $productAttributeAssembler;

    /**
     * The raw entity loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $rawEntityLoader;

    /**
     * The repository to load the stock item with.
     *
     * @var \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface
     */
    protected $urlRewriteRepository;

    /**
     * The action for stock item CRUD methods.
     *
     * @var \TechDivision\Import\Dbal\Actions\ActionInterface
     */
    private $stockItemStatusAction;

    /**
     * Initialize the processor with the necessary assembler and repository instances.
     *
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface                     $connection                        The connection to use
     * @param \TechDivision\Import\Product\Repositories\ProductRepositoryInterface         $productRepository                 The product repository to use
     * @param \TechDivision\Import\Product\Repositories\ProductWebsiteRepositoryInterface  $productWebsiteRepository          The product website repository to use
     * @param \TechDivision\Import\Product\Repositories\ProductDatetimeRepositoryInterface $productDatetimeRepository         The product datetime repository to use
     * @param \TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface  $productDecimalRepository          The product decimal repository to use
     * @param \TechDivision\Import\Product\Repositories\ProductIntRepositoryInterface      $productIntRepository              The product integer repository to use
     * @param \TechDivision\Import\Product\Repositories\ProductTextRepositoryInterface     $productTextRepository             The product text repository to use
     * @param \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface  $productVarcharRepository          The product varchar repository to use
     * @param \TechDivision\Import\Product\Repositories\CategoryProductRepositoryInterface $categoryProductRepository         The category product repository to use
     * @param \TechDivision\Import\Product\Repositories\StockItemRepositoryInterface       $stockItemRepository               The stock item repository to use
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository The EAV attribute option value repository to use
     * @param \TechDivision\Import\Repositories\EavAttributeRepositoryInterface            $eavAttributeRepository            The EAV attribute repository to use
     * @param \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface           $eavEntityTypeRepository           The EAV entity type repository to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $categoryProductAction             The category product action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $productDatetimeAction             The product datetime action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $productDecimalAction              The product decimal action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $productIntAction                  The product integer action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $productAction                     The product action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $productTextAction                 The product text action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $productVarcharAction              The product varchar action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $productWebsiteAction              The product website action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $stockItemAction                   The stock item action to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $urlRewriteAction                  The URL rewrite action to use
     * @param \TechDivision\Import\Product\Assemblers\ProductAttributeAssemblerInterface   $productAttributeAssembler         The assembler to load the product attributes with
     * @param \TechDivision\Import\Loaders\LoaderInterface                                 $rawEntityLoader                   The raw entity loader instance
     * @param \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface              $urlRewriteRepository              The URL rewrite repository to use
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface                            $stockItemStatusAction             The stock item status action to use
     */
    public function __construct(
        ConnectionInterface $connection,
        ProductRepositoryInterface $productRepository,
        ProductWebsiteRepositoryInterface $productWebsiteRepository,
        ProductDatetimeRepositoryInterface $productDatetimeRepository,
        ProductDecimalRepositoryInterface $productDecimalRepository,
        ProductIntRepositoryInterface $productIntRepository,
        ProductTextRepositoryInterface $productTextRepository,
        ProductVarcharRepositoryInterface $productVarcharRepository,
        CategoryProductRepositoryInterface $categoryProductRepository,
        StockItemRepositoryInterface $stockItemRepository,
        EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository,
        EavAttributeRepositoryInterface $eavAttributeRepository,
        EavEntityTypeRepositoryInterface $eavEntityTypeRepository,
        ActionInterface $categoryProductAction,
        ActionInterface $productDatetimeAction,
        ActionInterface $productDecimalAction,
        ActionInterface $productIntAction,
        ActionInterface $productAction,
        ActionInterface $productTextAction,
        ActionInterface $productVarcharAction,
        ActionInterface $productWebsiteAction,
        ActionInterface $stockItemAction,
        ActionInterface $urlRewriteAction,
        ProductAttributeAssemblerInterface $productAttributeAssembler,
        LoaderInterface $rawEntityLoader,
        UrlRewriteRepositoryInterface $urlRewriteRepository,
        ActionInterface $stockItemStatusAction = null
    ) {
        $this->setConnection($connection);
        $this->setProductRepository($productRepository);
        $this->setProductWebsiteRepository($productWebsiteRepository);
        $this->setProductDatetimeRepository($productDatetimeRepository);
        $this->setProductDecimalRepository($productDecimalRepository);
        $this->setProductIntRepository($productIntRepository);
        $this->setProductTextRepository($productTextRepository);
        $this->setProductVarcharRepository($productVarcharRepository);
        $this->setCategoryProductRepository($categoryProductRepository);
        $this->setStockItemRepository($stockItemRepository);
        $this->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $this->setEavAttributeRepository($eavAttributeRepository);
        $this->setEavEntityTypeRepository($eavEntityTypeRepository);
        $this->setCategoryProductAction($categoryProductAction);
        $this->setProductDatetimeAction($productDatetimeAction);
        $this->setProductDecimalAction($productDecimalAction);
        $this->setProductIntAction($productIntAction);
        $this->setProductAction($productAction);
        $this->setProductTextAction($productTextAction);
        $this->setProductVarcharAction($productVarcharAction);
        $this->setProductWebsiteAction($productWebsiteAction);
        $this->setStockItemAction($stockItemAction);
        $this->setUrlRewriteAction($urlRewriteAction);
        $this->setProductAttributeAssembler($productAttributeAssembler);
        $this->setRawEntityLoader($rawEntityLoader);
        $this->setUrlRewriteRepository($urlRewriteRepository);
        $this->setStockItemStatusAction($stockItemStatusAction);
    }

    /**
     * Set's the raw entity loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $rawEntityLoader The raw entity loader instance to set
     *
     * @return void
     */
    public function setRawEntityLoader(LoaderInterface $rawEntityLoader)
    {
        $this->rawEntityLoader = $rawEntityLoader;
    }

    /**
     * Return's the raw entity loader instance.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The raw entity loader instance
     */
    public function getRawEntityLoader()
    {
        return $this->rawEntityLoader;
    }

    /**
     * Set's the passed connection.
     *
     * @param \TechDivision\Import\Dbal\Connection\ConnectionInterface $connection The connection to set
     *
     * @return void
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return's the connection.
     *
     * @return \TechDivision\Import\Dbal\Connection\ConnectionInterface The connection instance
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
     * @param \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository The repository to access EAV attribute option values
     *
     * @return void
     */
    public function setEavAttributeOptionValueRepository(EavAttributeOptionValueRepositoryInterface $eavAttributeOptionValueRepository)
    {
        $this->eavAttributeOptionValueRepository = $eavAttributeOptionValueRepository;
    }

    /**
     * Return's the repository to access EAV attribute option values.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeOptionValueRepositoryInterface The repository instance
     */
    public function getEavAttributeOptionValueRepository()
    {
        return $this->eavAttributeOptionValueRepository;
    }

    /**
     * Set's the repository to access EAV attributes.
     *
     * @param \TechDivision\Import\Repositories\EavAttributeRepositoryInterface $eavAttributeRepository The repository to access EAV attributes
     *
     * @return void
     */
    public function setEavAttributeRepository(EavAttributeRepositoryInterface $eavAttributeRepository)
    {
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    /**
     * Return's the repository to access EAV attributes.
     *
     * @return \TechDivision\Import\Repositories\EavAttributeRepositoryInterface The repository instance
     */
    public function getEavAttributeRepository()
    {
        return $this->eavEntityTypeRepository;
    }

    /**
     * Set's the repository to access EAV entity types.
     *
     * @param \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface $eavEntityTypeRepository The repository to access EAV entity types
     *
     * @return void
     */
    public function setEavEntityTypeRepository(EavEntityTypeRepositoryInterface $eavEntityTypeRepository)
    {
        $this->eavEntityTypeRepository = $eavEntityTypeRepository;
    }

    /**
     * Return's the repository to access EAV entity types.
     *
     * @return \TechDivision\Import\Repositories\EavEntityTypeRepositoryInterface The repository instance
     */
    public function getEavEntityTypeRepository()
    {
        return $this->eavEntityTypeRepository;
    }

    /**
     * Set's the action with the product CRUD methods.
     *
     * @param ActionInterface $productAction The action with the product CRUD methods
     *
     * @return void
     */
    public function setProductAction(ActionInterface $productAction)
    {
        $this->productAction = $productAction;
    }

    /**
     * Return's the action with the product CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getProductAction()
    {
        return $this->productAction;
    }

    /**
     * Set's the action with the product varchar attribute CRUD methods.
     *
     * @param ActionInterface $productVarcharAction The action with the product varchar attriute CRUD methods
     *
     * @return void
     */
    public function setProductVarcharAction(ActionInterface $productVarcharAction)
    {
        $this->productVarcharAction = $productVarcharAction;
    }

    /**
     * Return's the action with the product varchar attribute CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getProductVarcharAction()
    {
        return $this->productVarcharAction;
    }

    /**
     * Set's the action with the product text attribute CRUD methods.
     *
     * @param ActionInterface $productTextAction The action with the product text attriute CRUD methods
     *
     * @return void
     */
    public function setProductTextAction(ActionInterface $productTextAction)
    {
        $this->productTextAction = $productTextAction;
    }

    /**
     * Return's the action with the product text attribute CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getProductTextAction()
    {
        return $this->productTextAction;
    }

    /**
     * Set's the action with the product int attribute CRUD methods.
     *
     * @param ActionInterface $productIntAction The action with the product int attriute CRUD methods
     *
     * @return void
     */
    public function setProductIntAction(ActionInterface $productIntAction)
    {
        $this->productIntAction = $productIntAction;
    }

    /**
     * Return's the action with the product int attribute CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getProductIntAction()
    {
        return $this->productIntAction;
    }

    /**
     * Set's the action with the product decimal attribute CRUD methods.
     *
     * @param ActionInterface $productDecimalAction The action with the product decimal attriute CRUD methods
     *
     * @return void
     */
    public function setProductDecimalAction(ActionInterface $productDecimalAction)
    {
        $this->productDecimalAction = $productDecimalAction;
    }

    /**
     * Return's the action with the product decimal attribute CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getProductDecimalAction()
    {
        return $this->productDecimalAction;
    }

    /**
     * Set's the action with the product datetime attribute CRUD methods.
     *
     * @param ActionInterface $productDatetimeAction The action with the product datetime attriute CRUD methods
     *
     * @return void
     */
    public function setProductDatetimeAction(ActionInterface $productDatetimeAction)
    {
        $this->productDatetimeAction = $productDatetimeAction;
    }

    /**
     * Return's the action with the product datetime attribute CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getProductDatetimeAction()
    {
        return $this->productDatetimeAction;
    }

    /**
     * Set's the action with the product website CRUD methods.
     *
     * @param ActionInterface $productWebsiteAction The action with the product website CRUD methods
     *
     * @return void
     */
    public function setProductWebsiteAction(ActionInterface $productWebsiteAction)
    {
        $this->productWebsiteAction = $productWebsiteAction;
    }

    /**
     * Return's the action with the product website CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getProductWebsiteAction()
    {
        return $this->productWebsiteAction;
    }

    /**
     * Set's the action with the category product relation CRUD methods.
     *
     * @param ActionInterface $categoryProductAction The action with the category product relation CRUD methods
     *
     * @return void
     */
    public function setCategoryProductAction(ActionInterface $categoryProductAction)
    {
        $this->categoryProductAction = $categoryProductAction;
    }

    /**
     * Return's the action with the category product relation CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getCategoryProductAction()
    {
        return $this->categoryProductAction;
    }

    /**
     * Set's the action with the stock item CRUD methods.
     *
     * @param ActionInterface $stockItemAction The action with the stock item CRUD methods
     *
     * @return void
     */
    public function setStockItemAction(ActionInterface $stockItemAction)
    {
        $this->stockItemAction = $stockItemAction;
    }

    /**
     * Return's the action with the stock item CRUD methods.
     *
     * @return ActionInterface The action instance
     */
    public function getStockItemAction()
    {
        return $this->stockItemAction;
    }

    /**
     * Set's the action with the stock item status CRUD methods.
     *
     * @param ActionInterface $stockItemStatusAction The action with the stock item CRUD methods
     *
     * @return void
     */
    public function setStockItemStatusAction($stockItemStatusAction)
    {
        $this->stockItemStatusAction = $stockItemStatusAction;
    }

    /**
     * Return's the action with the stock item status CRUD methods.
     *
     * @return ActionInterface
     */
    public function getStockItemStatusAction()
    {
        return $this->stockItemStatusAction;
    }


    /**
     * Set's the action with the URL rewrite CRUD methods.
     *
     * @param \TechDivision\Import\Dbal\Actions\ActionInterface $urlRewriteAction The action with the URL rewrite CRUD methods
     *
     * @return void
     */
    public function setUrlRewriteAction(ActionInterface $urlRewriteAction)
    {
        $this->urlRewriteAction = $urlRewriteAction;
    }

    /**
     * Return's the action with the URL rewrite CRUD methods.
     *
     * @return \TechDivision\Import\Dbal\Actions\ActionInterface The action instance
     */
    public function getUrlRewriteAction()
    {
        return $this->urlRewriteAction;
    }

    /**
     * Set's the repository to load the products with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductRepositoryInterface $productRepository The repository instance
     *
     * @return void
     */
    public function setProductRepository(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Return's the repository to load the products with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductRepositoryInterface The repository instance
     */
    public function getProductRepository()
    {
        return $this->productRepository;
    }

    /**
     * Set's the repository to load the product website relations with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductWebsiteRepositoryInterface $productWebsiteRepository The repository instance
     *
     * @return void
     */
    public function setProductWebsiteRepository(ProductWebsiteRepositoryInterface $productWebsiteRepository)
    {
        $this->productWebsiteRepository = $productWebsiteRepository;
    }

    /**
     * Return's the repository to load the product website relations with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductWebsiteRepositoryInterface The repository instance
     */
    public function getProductWebsiteRepository()
    {
        return $this->productWebsiteRepository;
    }

    /**
     * Set's the repository to load the product datetime attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductDatetimeRepositoryInterface $productDatetimeRepository The repository instance
     *
     * @return void
     */
    public function setProductDatetimeRepository(ProductDatetimeRepositoryInterface $productDatetimeRepository)
    {
        $this->productDatetimeRepository = $productDatetimeRepository;
    }

    /**
     * Return's the repository to load the product datetime attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductDatetimeRepositoryInterface The repository instance
     */
    public function getProductDatetimeRepository()
    {
        return $this->productDatetimeRepository;
    }

    /**
     * Set's the repository to load the product decimal attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface $productDecimalRepository The repository instance
     *
     * @return void
     */
    public function setProductDecimalRepository(ProductDecimalRepositoryInterface $productDecimalRepository)
    {
        $this->productDecimalRepository = $productDecimalRepository;
    }

    /**
     * Return's the repository to load the product decimal attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface The repository instance
     */
    public function getProductDecimalRepository()
    {
        return $this->productDecimalRepository;
    }

    /**
     * Set's the repository to load the product integer attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductIntRepositoryInterface $productIntRepository The repository instance
     *
     * @return void
     */
    public function setProductIntRepository(ProductIntRepositoryInterface $productIntRepository)
    {
        $this->productIntRepository = $productIntRepository;
    }

    /**
     * Return's the repository to load the product integer attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductIntRepositoryInterface The repository instance
     */
    public function getProductIntRepository()
    {
        return $this->productIntRepository;
    }

    /**
     * Set's the repository to load the product text attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductTextRepositoryInterface $productTextRepository The repository instance
     *
     * @return void
     */
    public function setProductTextRepository(ProductTextRepositoryInterface $productTextRepository)
    {
        $this->productTextRepository = $productTextRepository;
    }

    /**
     * Return's the repository to load the product text attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductTextRepositoryInterface The repository instance
     */
    public function getProductTextRepository()
    {
        return $this->productTextRepository;
    }

    /**
     * Set's the repository to load the product varchar attribute with.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface $productVarcharRepository The repository instance
     *
     * @return void
     */
    public function setProductVarcharRepository(ProductVarcharRepositoryInterface $productVarcharRepository)
    {
        $this->productVarcharRepository = $productVarcharRepository;
    }

    /**
     * Return's the repository to load the product varchar attribute with.
     *
     * @return \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface The repository instance
     */
    public function getProductVarcharRepository()
    {
        return $this->productVarcharRepository;
    }

    /**
     * Set's the repository to load the category product relations with.
     *
     * @param \TechDivision\Import\Product\Repositories\CategoryProductRepositoryInterface $categoryProductRepository The repository instance
     *
     * @return void
     */
    public function setCategoryProductRepository(CategoryProductRepositoryInterface $categoryProductRepository)
    {
        $this->categoryProductRepository = $categoryProductRepository;
    }

    /**
     * Return's the repository to load the category product relations with.
     *
     * @return \TechDivision\Import\Product\Repositories\CategoryProductRepositoryInterface The repository instance
     */
    public function getCategoryProductRepository()
    {
        return $this->categoryProductRepository;
    }

    /**
     * Set's the repository to load the stock items with.
     *
     * @param \TechDivision\Import\Product\Repositories\StockItemRepositoryInterface $stockItemRepository The repository instance
     *
     * @return void
     */
    public function setStockItemRepository(StockItemRepositoryInterface $stockItemRepository)
    {
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * Return's the repository to load the stock items with.
     *
     * @return \TechDivision\Import\Product\Repositories\StockItemRepositoryInterface The repository instance
     */
    public function getStockItemRepository()
    {
        return $this->stockItemRepository;
    }

    /**
     * Set's the assembler to load the product attributes with.
     *
     * @param \TechDivision\Import\Product\Assemblers\ProductAttributeAssemblerInterface $productAttributeAssembler The assembler instance
     *
     * @return void
     */
    public function setProductAttributeAssembler(ProductAttributeAssemblerInterface $productAttributeAssembler)
    {
        $this->productAttributeAssembler = $productAttributeAssembler;
    }

    /**
     * Return's the assembler to load the product attributes with.
     *
     * @return \TechDivision\Import\Product\Assemblers\ProductAttributeAssemblerInterface The assembler instance
     */
    public function getProductAttributeAssembler()
    {
        return $this->productAttributeAssembler;
    }

    /**
     * Set's the repository to load the URL rewrites with.
     *
     * @param \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface $urlRewriteRepository The repository instance
     *
     * @return void
     */
    public function setUrlRewriteRepository(UrlRewriteRepositoryInterface $urlRewriteRepository)
    {
        $this->urlRewriteRepository = $urlRewriteRepository;
    }

    /**
     * Return's the repository to load the URL rewrites with.
     *
     * @return \TechDivision\Import\Repositories\UrlRewriteRepositoryInterface The repository instance
     */
    public function getUrlRewriteRepository()
    {
        return $this->urlRewriteRepository;
    }

    /**
     * Return's an array with the available EAV attributes for the passed is user defined flag.
     *
     * @param integer $isUserDefined The flag itself
     *
     * @return array The array with the EAV attributes matching the passed flag
     */
    public function getEavAttributeByIsUserDefined($isUserDefined = 1)
    {
        return $this->getEavAttributeRepository()->findAllByIsUserDefined($isUserDefined);
    }

    /**
     * Return's the category product relations for the product with the passed SKU.
     *
     * @param string $sku The product SKU to load the category relations for
     *
     * @return array The category product relations for the product with the passed SKU
     */
    public function getCategoryProductsBySku($sku)
    {
        return $this->getCategoryProductRepository()->findAllBySku($sku);
    }

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    public function getProductAttributesByPrimaryKeyAndStoreId($pk, $storeId)
    {
        return $this->getProductAttributeAssembler()->getProductAttributesByPrimaryKeyAndStoreId($pk, $storeId);
    }

    /**
     * Load's and return's a raw entity without primary key but the mandatory members only and nulled values.
     *
     * @param string $entityTypeCode The entity type code to return the raw entity for
     * @param array  $data           An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    public function loadRawEntity($entityTypeCode, array $data = array())
    {
        return $this->getRawEntityLoader()->load($entityTypeCode, $data);
    }

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
    public function loadAttributeOptionValueByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value)
    {
        return $this->getEavAttributeOptionValueRepository()->findOneByEntityTypeIdAndAttributeCodeAndStoreIdAndValue($entityTypeId, $attributeCode, $storeId, $value);
    }

    /**
     * Load's and return's the available products.
     *
     * @return array The available products
     */
    public function loadProducts()
    {
        return $this->getProductRepository()->findAll();
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
     * Load's and return's the product website relations for the product with the passed SKU.
     *
     * @param string $sku The SKU to of the product to load the product website relations for
     *
     * @return array The product website relations
     */
    public function loadProductWebsitesBySku($sku)
    {
        return $this->getProductWebsiteRepository()->findAllBySku($sku);
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
     * Load's and return's the stock item with the passed product/website/stock ID.
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
     * Load's and return's the stock item status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    public function loadStockItemStatus($productId, $websiteId, $stockId)
    {
        return $this->getStockItemRepository()->findOneStockStatusByProductIdAndWebsiteIdAndStockId($productId, $websiteId, $stockId);
    }

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
    public function loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value)
    {
        return $this->loadProductVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value);
    }

    /**
     * Load's and return's the varchar attribute with the passed params.
     *
     * @param integer $attributeCode The attribute code of the varchar attribute
     * @param integer $entityTypeId  The entity type ID of the varchar attribute
     * @param integer $storeId       The store ID of the varchar attribute
     * @param string  $primaryKey    The primary key of the product
     *
     * @return array|null The varchar attribute
     */
    public function loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKey($attributeCode, $entityTypeId, $storeId, $primaryKey)
    {
        return $this->loadProductVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPK($attributeCode, $entityTypeId, $storeId, $primaryKey);
    }

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
    public function loadProductVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value)
    {
        return $this->getProductVarcharRepository()->findOneByAttributeCodeAndEntityTypeIdAndStoreIdAndValue($attributeCode, $entityTypeId, $storeId, $value);
    }

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
    public function loadProductVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPK($attributeCode, $entityTypeId, $storeId, $pk)
    {
        return $this->getProductVarcharRepository()->findOneByAttributeCodeAndEntityTypeIdAndStoreIdAndPk($attributeCode, $entityTypeId, $storeId, $pk);
    }

    /**
     * Return's an EAV entity type with the passed entity type code.
     *
     * @param string $entityTypeCode The code of the entity type to return
     *
     * @return array The entity type with the passed entity type code
     */
    public function loadEavEntityTypeByEntityTypeCode($entityTypeCode)
    {
        return $this->getEavEntityTypeRepository()->findOneByEntityTypeCode($entityTypeCode);
    }

    /**
     * Load's and return's the URL rewrite for the given request path and store ID
     *
     * @param string $requestPath The request path to load the URL rewrite for
     * @param int    $storeId     The store ID to load the URL rewrite for
     *
     * @return array|null The URL rewrite found for the given request path and store ID
     */
    public function loadUrlRewriteByRequestPathAndStoreId(string $requestPath, int $storeId)
    {
        return $this->getUrlRewriteRepository()->findOneByRequestPathAndStoreId($requestPath, $storeId);
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
     * @return string The ID of the persisted attribute
     */
    public function persistProductVarcharAttribute($attribute, $name = null)
    {
        return $this->getProductVarcharAction()->persist($attribute, $name);
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
     * Persist's the passed stock item status data and return's the ID.
     *
     * @param array       $stockItem The stock item data to persist
     * @param string|null $name      The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function persistStockItemStatus($stockItem, $name = null)
    {
        $stockItemStatus = $this->getStockItemStatusAction();
        // backward compatibility for symfony di
        if ($stockItemStatus) {
            $stockItemStatus->persist($stockItem, $name);
        }
    }

    /**
     * Persist's the URL rewrite with the passed data.
     *
     * @param array       $row  The URL rewrite to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the persisted entity
     */
    public function persistUrlRewrite($row, $name = null)
    {
        return $this->getUrlRewriteAction()->persist($row, $name);
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
        $stockItemStatus = $this->getStockItemStatusAction();
        // backward compatibility for symfony di
        if ($stockItemStatus) {
            $stockItemStatus->delete($row, $name);
        }
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

    /**
     * Delete's the product datetime attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductDatetimeAttribute($row, $name = null)
    {
        $this->getProductDatetimeAction()->delete($row, $name);
    }

    /**
     * Delete's the product decimal attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductDecimalAttribute($row, $name = null)
    {
        $this->getProductDecimalAction()->delete($row, $name);
    }

    /**
     * Delete's the product integer attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductIntAttribute($row, $name = null)
    {
        $this->getProductIntAction()->delete($row, $name);
    }

    /**
     * Delete's the product text attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductTextAttribute($row, $name = null)
    {
        $this->getProductTextAction()->delete($row, $name);
    }

    /**
     * Delete's the product varchar attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductVarcharAttribute($row, $name = null)
    {
        $this->getProductVarcharAction()->delete($row, $name);
    }

    /**
     * Clean-Up the repositories to free memory.
     *
     * @return void
     */
    public function cleanUp()
    {
    }
}
