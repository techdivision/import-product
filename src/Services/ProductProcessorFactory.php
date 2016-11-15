<?php

/**
 * TechDivision\Import\Product\Services\ProductProcessorFactory
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

use Psr\Log\LoggerInterface;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Configuration\SubjectInterface;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Product\Actions\ProductAction;
use TechDivision\Import\Product\Actions\ProductCategoryAction;
use TechDivision\Import\Product\Actions\StockItemAction;
use TechDivision\Import\Product\Actions\StockStatusAction;
use TechDivision\Import\Product\Actions\ProductWebsiteAction;
use TechDivision\Import\Product\Actions\ProductVarcharAction;
use TechDivision\Import\Product\Actions\ProductTextAction;
use TechDivision\Import\Product\Actions\ProductIntAction;
use TechDivision\Import\Product\Actions\ProductDecimalAction;
use TechDivision\Import\Product\Actions\ProductDatetimeAction;
use TechDivision\Import\Product\Actions\Processors\ProductRemoveProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductCategoryPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDatetimePersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDecimalPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductIntPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductTextPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductVarcharPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsitePersistProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusPersistProcessor;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductProcessorFactory
{

    /**
     * Factory method to create a new product processor instance.
     *
     * @param \PDO                                       $connection    The PDO connection to use
     * @param TechDivision\Import\ConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Services\ProductProcessor The processor instance
     */
    public function factory(\PDO $connection, ConfigurationInterface $configuration)
    {

        // extract Magento edition/version
        $magentoEdition = $configuration->getMagentoEdition();
        $magentoVersion = $configuration->getMagentoVersion();

        // initialize the repository that provides EAV attribute option value query functionality
        $eavAttributeOptionValueRepository = new EavAttributeOptionValueRepository();
        $eavAttributeOptionValueRepository->setMagentoEdition($magentoEdition);
        $eavAttributeOptionValueRepository->setMagentoVersion($magentoVersion);
        $eavAttributeOptionValueRepository->setConnection($connection);
        $eavAttributeOptionValueRepository->init();

        // initialize the action that provides product category CRUD functionality
        $productCategoryPersistProcessor = new ProductCategoryPersistProcessor();
        $productCategoryPersistProcessor->setMagentoEdition($magentoEdition);
        $productCategoryPersistProcessor->setMagentoVersion($magentoVersion);
        $productCategoryPersistProcessor->setConnection($connection);
        $productCategoryPersistProcessor->init();
        $productCategoryAction = new ProductCategoryAction();
        $productCategoryAction->setPersistProcessor($productCategoryPersistProcessor);

        // initialize the action that provides product datetime attribute CRUD functionality
        $productDatetimePersistProcessor = new ProductDatetimePersistProcessor();
        $productDatetimePersistProcessor->setMagentoEdition($magentoEdition);
        $productDatetimePersistProcessor->setMagentoVersion($magentoVersion);
        $productDatetimePersistProcessor->setConnection($connection);
        $productDatetimePersistProcessor->init();
        $productDatetimeAction = new ProductDatetimeAction();
        $productDatetimeAction->setPersistProcessor($productDatetimePersistProcessor);

        // initialize the action that provides product decimal attribute CRUD functionality
        $productDecimalPersistProcessor = new ProductDecimalPersistProcessor();
        $productDecimalPersistProcessor->setMagentoEdition($magentoEdition);
        $productDecimalPersistProcessor->setMagentoVersion($magentoVersion);
        $productDecimalPersistProcessor->setConnection($connection);
        $productDecimalPersistProcessor->init();
        $productDecimalAction = new ProductDecimalAction();
        $productDecimalAction->setPersistProcessor($productDecimalPersistProcessor);

        // initialize the action that provides product integer attribute CRUD functionality
        $productIntPersistProcessor = new ProductIntPersistProcessor();
        $productIntPersistProcessor->setMagentoEdition($magentoEdition);
        $productIntPersistProcessor->setMagentoVersion($magentoVersion);
        $productIntPersistProcessor->setConnection($connection);
        $productIntPersistProcessor->init();
        $productIntAction = new ProductIntAction();
        $productIntAction->setPersistProcessor($productIntPersistProcessor);

        // initialize the action that provides product CRUD functionality
        $productPersistProcessor = new ProductPersistProcessor();
        $productPersistProcessor->setMagentoEdition($magentoEdition);
        $productPersistProcessor->setMagentoVersion($magentoVersion);
        $productPersistProcessor->setConnection($connection);
        $productPersistProcessor->init();
        $productRemoveProcessor = new ProductRemoveProcessor();
        $productRemoveProcessor->setMagentoEdition($magentoEdition);
        $productRemoveProcessor->setMagentoVersion($magentoVersion);
        $productRemoveProcessor->setConnection($connection);
        $productRemoveProcessor->init();
        $productAction = new ProductAction();
        $productAction->setPersistProcessor($productPersistProcessor);
        $productAction->setRemoveProcessor($productRemoveProcessor);

        // initialize the action that provides product text attribute CRUD functionality
        $productTextPersistProcessor = new ProductTextPersistProcessor();
        $productTextPersistProcessor->setMagentoEdition($magentoEdition);
        $productTextPersistProcessor->setMagentoVersion($magentoVersion);
        $productTextPersistProcessor->setConnection($connection);
        $productTextPersistProcessor->init();
        $productTextAction = new ProductTextAction();
        $productTextAction->setPersistProcessor($productTextPersistProcessor);

        // initialize the action that provides product varchar attribute CRUD functionality
        $productVarcharPersistProcessor = new ProductVarcharPersistProcessor();
        $productVarcharPersistProcessor->setMagentoEdition($magentoEdition);
        $productVarcharPersistProcessor->setMagentoVersion($magentoVersion);
        $productVarcharPersistProcessor->setConnection($connection);
        $productVarcharPersistProcessor->init();
        $productVarcharAction = new ProductVarcharAction();
        $productVarcharAction->setPersistProcessor($productVarcharPersistProcessor);

        // initialize the action that provides provides product website CRUD functionality
        $productWebsitePersistProcessor = new ProductWebsitePersistProcessor();
        $productWebsitePersistProcessor->setMagentoEdition($magentoEdition);
        $productWebsitePersistProcessor->setMagentoVersion($magentoVersion);
        $productWebsitePersistProcessor->setConnection($connection);
        $productWebsitePersistProcessor->init();
        $productWebsiteAction = new ProductWebsiteAction();
        $productWebsiteAction->setPersistProcessor($productWebsitePersistProcessor);

        // initialize the action that provides stock item CRUD functionality
        $stockItemPersistProcessor = new StockItemPersistProcessor();
        $stockItemPersistProcessor->setMagentoEdition($magentoEdition);
        $stockItemPersistProcessor->setMagentoVersion($magentoVersion);
        $stockItemPersistProcessor->setConnection($connection);
        $stockItemPersistProcessor->init();
        $stockItemAction = new StockItemAction();
        $stockItemAction->setPersistProcessor($stockItemPersistProcessor);

        // initialize the action that provides stock status CRUD functionality
        $stockStatusPersistProcessor = new StockStatusPersistProcessor();
        $stockStatusPersistProcessor->setMagentoEdition($magentoEdition);
        $stockStatusPersistProcessor->setMagentoVersion($magentoVersion);
        $stockStatusPersistProcessor->setConnection($connection);
        $stockStatusPersistProcessor->init();
        $stockStatusAction = new StockStatusAction();
        $stockStatusAction->setPersistProcessor($stockStatusPersistProcessor);

        // initialize the product processor
        $productProcessor = new ProductProcessor();
        $productProcessor->setConnection($connection);
        $productProcessor->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $productProcessor->setProductCategoryAction($productCategoryAction);
        $productProcessor->setProductDatetimeAction($productDatetimeAction);
        $productProcessor->setProductDecimalAction($productDecimalAction);
        $productProcessor->setProductIntAction($productIntAction);
        $productProcessor->setProductAction($productAction);
        $productProcessor->setProductTextAction($productTextAction);
        $productProcessor->setProductVarcharAction($productVarcharAction);
        $productProcessor->setProductWebsiteAction($productWebsiteAction);
        $productProcessor->setStockItemAction($stockItemAction);
        $productProcessor->setStockStatusAction($stockStatusAction);

        // return the instance
        return $productProcessor;
    }
}
