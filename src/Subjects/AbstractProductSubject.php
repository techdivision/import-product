<?php

/**
 * TechDivision\Import\Product\Subjects\AbstractProductSubject
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

namespace TechDivision\Import\Product\Subjects;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\FrontendInputTypes;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\ConfigurationKeys;
use TechDivision\Import\Subjects\AbstractEavSubject;
use TechDivision\Import\Subjects\EntitySubjectInterface;
use TechDivision\Import\Utils\StoreViewCodes;

/**
 * The abstract product subject implementation that provides basic product
 * handling business logic.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
abstract class AbstractProductSubject extends AbstractEavSubject implements EntitySubjectInterface
{

    /**
     * The available stores.
     *
     * @var array
     */
    protected $stores = array();

    /**
     * The available store websites.
     *
     * @var array
     */
    protected $storeWebsites = array();

    /**
     * The available EAV attributes, grouped by their attribute set and the attribute set name as keys.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * The available tax classes.
     *
     * @var array
     */
    protected $taxClasses = array();

    /**
     * The available categories per store view.
     *
     * @var array
     */
    protected $categoriesPerStoreView = array();

    /**
     * The available link types.
     *
     * @var array
     */
    protected $linkTypes = array();

    /**
     * The ID of the product that has been created recently.
     *
     * @var string
     */
    protected $lastEntityId;

    /**
     * The SKU of the product that has been created recently.
     *
     * @var string
     */
    protected $lastSku;

    /**
     * The Magento 2 configuration.
     *
     * @var array
     */
    protected $coreConfigData;

    /**
     * The mapping for the SKUs to the created entity IDs.
     *
     * @var array
     */
    protected $skuEntityIdMapping = array();

    /**
     * The mapping for the SKUs to the store view codes.
     *
     * @var array
     */
    protected $skuStoreViewCodeMapping = array();

    /**
     * The array with the available image types and their label columns.
     *
     * @var array
     */
    protected $imageTypes = array();

    /**
     * Mappings for CSV column header => attribute code.
     *
     * @var array
     */
    protected $headerMappings = array(
        'product_online'       => 'status',
        'tax_class_name'       => 'tax_class_id',
        'bundle_price_type'    => 'price_type',
        'bundle_sku_type'      => 'sku_type',
        'bundle_price_view'    => 'price_view',
        'bundle_weight_type'   => 'weight_type',
        'bundle_shipment_type' => 'shipment_type',
        'related_skus'         => 'relation_skus',
        'related_position'     => 'relation_position',
        'crosssell_skus'       => 'cross_sell_skus',
        'crosssell_position'   => 'cross_sell_position',
        'upsell_skus'          => 'up_sell_skus',
        'upsell_position'      => 'up_sell_position',
        'msrp_price'           => 'msrp',
        'base_image'           => 'image',
        'base_image_label'     => 'image_label',
        'thumbnail_image'      => 'thumbnail',
        'thumbnail_image_label'=> 'thumbnail_label'
    );

    /**
     * The default mappings for the user defined attributes, based on the attributes frontend input type.
     *
     * @var array
     */
    protected $defaultFrontendInputCallbackMappings = array(
        FrontendInputTypes::SELECT      => 'import_product.callback.select',
        FrontendInputTypes::MULTISELECT => 'import_product.callback.multiselect',
        FrontendInputTypes::BOOLEAN     => 'import_product.callback.boolean'
    );

    /**
     * Return's the default callback frontend input mappings for the user defined attributes.
     *
     * @return array The default frontend input callback mappings
     */
    public function getDefaultFrontendInputCallbackMappings()
    {
        return $this->defaultFrontendInputCallbackMappings;
    }

    /**
     * Return's the available link types.
     *
     * @return array The link types
     */
    public function getLinkTypes()
    {
        return $this->linkTypes;
    }

    /**
     * Set's the SKU of the last imported product.
     *
     * @param string $lastSku The SKU
     *
     * @return void
     */
    public function setLastSku($lastSku)
    {
        $this->lastSku = $lastSku;
    }

    /**
     * Return's the SKU of the last imported product.
     *
     * @return string|null The SKU
     */
    public function getLastSku()
    {
        return $this->lastSku;
    }

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    public function setLastEntityId($lastEntityId)
    {
        $this->lastEntityId = $lastEntityId;
    }

    /**
     * Return's the ID of the product that has been created recently.
     *
     * @return string The entity Id
     */
    public function getLastEntityId()
    {
        return $this->lastEntityId;
    }

    /**
     * Queries whether or not the SKU has already been processed.
     *
     * @param string $sku The SKU to check been processed
     *
     * @return boolean TRUE if the SKU has been processed, else FALSE
     */
    public function hasBeenProcessed($sku)
    {
        return isset($this->skuEntityIdMapping[$sku]);
    }

    /**
     * Queries whether or not the passed PK and store view code has already been processed.
     *
     * @param string $pk            The PK to check been processed
     * @param string $storeViewCode The store view code to check been processed
     *
     * @return boolean TRUE if the PK and store view code has been processed, else FALSE
     */
    public function storeViewHasBeenProcessed($pk, $storeViewCode)
    {
        return isset($this->skuEntityIdMapping[$pk]) && isset($this->skuStoreViewCodeMapping[$pk]) && in_array($storeViewCode, $this->skuStoreViewCodeMapping[$pk]);
    }

    /**
     * Add the passed SKU => entity ID mapping.
     *
     * @param string $sku The SKU
     *
     * @return void
     */
    public function addSkuEntityIdMapping($sku)
    {
        $this->skuEntityIdMapping[$sku] = $this->getLastEntityId();
    }

    /**
     * Add the passed SKU => store view code mapping.
     *
     * @param string $sku           The SKU
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    public function addSkuStoreViewCodeMapping($sku, $storeViewCode)
    {
        $this->skuStoreViewCodeMapping[$sku][] = $storeViewCode;
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute($serial);

        // load the global data we've prepared initially
        $this->linkTypes = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::LINK_TYPES];
        $this->categoriesPerStoreView = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CATEGORIES_PER_STORE_VIEW];
        $this->taxClasses = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::TAX_CLASSES];
        $this->imageTypes =  $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::IMAGE_TYPES];
        $this->storeWebsites =  $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORE_WEBSITES];

        // invoke the parent method
        parent::setUp($serial);
    }


    /**
     * Clean up the global data after importing the bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial)
    {

        // invoke the parent method
        parent::tearDown($serial);

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the status
        $registryProcessor->mergeAttributesRecursive(
            $serial,
            array(
                RegistryKeys::FILES => array($this->getFilename() => array(RegistryKeys::STATUS => 1)),
                RegistryKeys::SKU_ENTITY_ID_MAPPING => $this->skuEntityIdMapping,
                RegistryKeys::SKU_STORE_VIEW_CODE_MAPPING => $this->skuStoreViewCodeMapping
            )
        );
    }

    /**
     * Return's the available image types.
     *
     * @return array The array with the available image types
     */
    public function getImageTypes()
    {
        return $this->imageTypes;
    }

    /**
     * Return's the store ID of the actual row, or of the default store
     * if no store view code is set in the CSV file.
     *
     * @param string|null $default The default store view code to use, if no store view code is set in the CSV file
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    public function getRowStoreId($default = null)
    {

        // initialize the default store view code, if not passed
        if ($default == null) {
            $defaultStore = $this->getDefaultStore();
            $default = $defaultStore[MemberNames::CODE];
        }

        // load the store view code the create the product/attributes for
        $storeViewCode = $this->getStoreViewCode($default);

        // query whether or not, the requested store is available
        if (isset($this->stores[$storeViewCode])) {
            return (integer) $this->stores[$storeViewCode][MemberNames::STORE_ID];
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid store view code %s', $storeViewCode)
            )
        );
    }

    /**
     * Return's the tax class ID for the passed tax class name.
     *
     * @param string $taxClassName The tax class name to return the ID for
     *
     * @return integer The tax class ID
     * @throws \Exception Is thrown, if the tax class with the requested name is not available
     */
    public function getTaxClassIdByTaxClassName($taxClassName)
    {

        // query whether or not, the requested tax class is available
        if (isset($this->taxClasses[$taxClassName])) {
            return (integer) $this->taxClasses[$taxClassName][MemberNames::CLASS_ID];
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid tax class name %s', $taxClassName)
            )
        );
    }

    /**
     * Return's the store website for the passed code.
     *
     * @param string $code The code of the store website to return the ID for
     *
     * @return integer The store website ID
     * @throws \Exception Is thrown, if the store website with the requested code is not available
     */
    public function getStoreWebsiteIdByCode($code)
    {

        // query whether or not, the requested store website is available
        if (isset($this->storeWebsites[$code])) {
            return (integer) $this->storeWebsites[$code][MemberNames::WEBSITE_ID];
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid website code %s', $code)
            )
        );
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     * @param string $storeViewCode The code of a store view; Default 'admin'
     *
     * @return array The category
     * @throws \Exception Is thrown, if the requested category is not available
     */
    public function getCategoryByPath($path, $storeViewCode = StoreViewCodes::ADMIN)
    {
        $categories = $this->getCategoriesByStoreViewCode($storeViewCode);

        // query whether or not the category with the passed path exists
        if (isset($categories[$path])) {
            return $categories[$path];
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Can\'t find category with path %s', $path)
            )
        );
    }

    /**
     * Retrieve categories by given store view code
     * @param string $storeViewCode
     * @return array
     */
    public function getCategoriesByStoreViewCode($storeViewCode)
    {
        return $this->categoriesPerStoreView[$storeViewCode] ?? [];
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param integer $categoryId The ID of the category to return
     * @param string $storeViewCode The code of a store view; Default 'admin'
     *
     * @return array The category data
     * @throws \Exception Is thrown, if the category is not available
     */
    public function getCategory($categoryId, $storeViewCode = StoreViewCodes::ADMIN)
    {
        $categories = $this->getCategoriesByStoreViewCode($storeViewCode);

        // try to load the category with the passed ID
        foreach ($categories as $category) {
            if ($category[MemberNames::ENTITY_ID] == $categoryId) {
                return $category;
            }
        }

        // throw an exception if the category is NOT available
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Can\'t load category with ID %d', $categoryId)
            )
        );
    }

    /**
     * Return's the root category for the actual view store.
     *
     * @return array The store's root category
     * @throws \Exception Is thrown if the root category for the passed store code is NOT available
     */
    public function getRootCategory()
    {

        // load the default store
        $defaultStore = $this->getDefaultStore();

        // load the actual store view code
        $storeViewCode = $this->getStoreViewCode($defaultStore[MemberNames::CODE]);

        // query weather or not we've a root category or not
        if (isset($this->rootCategories[$storeViewCode])) {
            return $this->rootCategories[$storeViewCode];
        }

        // throw an exception if the root category is NOT available
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Root category for %s is not available', $storeViewCode)
            )
        );
    }

    /**
     * Returns an array with the codes of the store views related with the passed website code.
     *
     * @param string $websiteCode The code of the website to return the store view codes for
     *
     * @return array The array with the matching store view codes
     */
    public function getStoreViewCodesByWebsiteCode($websiteCode)
    {

        // query whether or not the website with the passed code exists
        if (!isset($this->storeWebsites[$websiteCode])) {
            // throw an exception if the website is NOT available
            throw new \Exception(
                $this->appendExceptionSuffix(
                    sprintf('Website with code "%s" is not available', $websiteCode)
                )
            );
        }

        // initialize the array for the store view codes
        $storeViewCodes = array();

        // load the website ID
        $websiteId = (integer) $this->storeWebsites[$websiteCode][MemberNames::WEBSITE_ID];

        // iterate over the available stores to find the one of the website
        foreach ($this->stores as $storeCode => $store) {
            if ((integer) $store[MemberNames::WEBSITE_ID] === $websiteId) {
                $storeViewCodes[] = $storeCode;
            }
        }

        // return the array with the matching store view codes
        return $storeViewCodes;
    }

    /**
     * Merge the columns from the configuration with all image type columns to define which
     * columns should be cleaned-up.
     *
     * @return array The columns that has to be cleaned-up
     */
    public function getCleanUpColumns()
    {

        // load the colums that has to be cleaned-up
        $cleanUpColumns = $this->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_EMPTY_COLUMNS);

        // query whether or not the image columns has to be cleaned-up also
        if ($this->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_EMPTY_IMAGE_COLUMNS) &&
            $this->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_EMPTY_IMAGE_COLUMNS, false)
        ) {
            // if yes load the image column names
            $imageTypes = array_keys($this->getImageTypes());

            // and append them to the column names from the configuration
            foreach ($imageTypes as $imageAttribute) {
                $cleanUpColumns[] = $imageAttribute;
            }
        }

        // return the array with the column names that has to be cleaned-up
        return $cleanUpColumns;
    }
}
