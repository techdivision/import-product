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

use TechDivision\Import\Subjects\AbstractSubject;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Services\ProductProcessorInterface;

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
abstract class AbstractProductSubject extends AbstractSubject
{

    /**
     * The processor to read/write the necessary product data.
     *
     * @var \TechDivision\Import\Product\Services\ProductProcessorInterface
     */
    protected $productProcessor;

    /**
     * The available EAV attribute sets.
     *
     * @var array
     */
    protected $attributeSets = array();

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
     * The available categories.
     *
     * @var array
     */
    protected $categories = array();

    /**
     * The available root categories.
     *
     * @var array
     */
    protected $rootCategories = array();

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
     * The store view code the create the product/attributes for.
     *
     * @var string
     */
    protected $storeViewCode;

    /**
     * The default store.
     *
     * @var array
     */
    protected $defaultStore;

    /**
     * Set's the product processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductProcessorInterface $productProcessor The product processor instance
     *
     * @return void
     */
    public function setProductProcessor(ProductProcessorInterface $productProcessor)
    {
        $this->productProcessor = $productProcessor;
    }

    /**
     * Return's the product processor instance.
     *
     * @return \TechDivision\Import\Services\ProductProcessorInterface The product processor instance
     */
    public function getProductProcessor()
    {
        return $this->productProcessor;
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
     * Set's the store view code the create the product/attributes for.
     *
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    public function setStoreViewCode($storeViewCode)
    {
        $this->storeViewCode = $storeViewCode;
    }

    /**
     * Return's the store view code the create the product/attributes for.
     *
     * @param string|null $default The default value to return, if the store view code has not been set
     *
     * @return string The store view code
     */
    public function getStoreViewCode($default = null)
    {

        // return the store view code, if available
        if ($this->storeViewCode != null) {
            return $this->storeViewCode;
        }

        // if NOT and a default code is available
        if ($default != null) {
            // return the default value
            return $default;
        }
    }

    /**
     * Return's the default store.
     *
     * @return array The default store
     */
    public function getDefaultStore()
    {
        return $this->defaultStore;
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @return void
     * @see \Importer\Csv\Actions\ProductImportAction::prepare()
     */
    public function setUp()
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute($this->getSerial());

        // load the global data we've prepared initially
        $this->attributeSets = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ATTRIBUTE_SETS];
        $this->storeWebsites =  $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORE_WEBSITES];
        $this->attributes = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::EAV_ATTRIBUTES];
        $this->stores = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::STORES];
        $this->taxClasses = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::TAX_CLASSES];
        $this->categories = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::CATEGORIES];
        $this->rootCategories = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ROOT_CATEGORIES];
        $this->defaultStore = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::DEFAULT_STORE];

        // prepare the callbacks
        parent::setUp();
    }

    /**
     * Clean up the global data after importing the bunch.
     *
     * @return void
     */
    public function tearDown()
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the status
        $registryProcessor->mergeAttributesRecursive(
            $this->getSerial(),
            array(
                RegistryKeys::FILES => array(
                    $this->getFilename() => array(RegistryKeys::STATUS => 1)
                )
            )
        );
    }

    /**
     * Return's the attributes for the attribute set of the product that has to be created.
     *
     * @return array The attributes
     * @throws \Exception Is thrown if the attributes for the actual attribute set are not available
     */
    public function getAttributes()
    {

        // load the attribute set of the product that has to be created.
        $attributeSet = $this->getAttributeSet();

        // query whether or not, the requested EAV attributes are available
        if (isset($this->attributes[$attributeSetName = $attributeSet[MemberNames::ATTRIBUTE_SET_NAME]])) {
            return $this->attributes[$attributeSetName];
        }

        // throw an exception, if not
        throw new \Exception(sprintf('Found invalid attribute set name %s', $attributeSetName));
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
        throw new \Exception(sprintf('Found invalid store view code %s', $storeViewCode));
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
        throw new \Exception(sprintf('Found invalid tax class name %s', $taxClassName));
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
        throw new \Exception(sprintf('Found invalid website code %s', $code));
    }

    /**
     * Return's the attribute set with the passed attribute set name.
     *
     * @param string $attributeSetName The name of the requested attribute set
     *
     * @return array The attribute set data
     * @throws \Exception Is thrown, if the attribute set with the passed name is not available
     */
    public function getAttributeSetByAttributeSetName($attributeSetName)
    {
        // query whether or not, the requested attribute set is available
        if (isset($this->attributeSets[$attributeSetName])) {
            return $this->attributeSets[$attributeSetName];
        }

        // throw an exception, if not
        throw new \Exception(sprintf('Found invalid attribute set name %s', $attributeSetName));
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     * @throws \Exception Is thrown, if the requested category is not available
     */
    public function getCategoryByPath($path)
    {

        // query whether or not the category with the passed path exists
        if (isset($this->categories[$path])) {
            return $this->categories[$path];
        }

        // throw an exception, if not
        throw new \Exception(sprintf('Found invalid category path %s', $path));
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param integer $categoryId The ID of the category to return
     *
     * @return array The category data
     * @throws \Exception Is thrown, if the category is not available
     */
    public function getCategory($categoryId)
    {

        // try to load the category with the passed ID
        foreach ($this->categories as $category) {
            if ($category[MemberNames::ENTITY_ID] == $categoryId) {
                return $category;
            }
        }

        // throw an exception if the category is NOT available
        throw new \Exception(sprintf('Can\'t load category with ID %d', $categoryId));
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
        throw new \Exception(sprintf('Root category for %s is not available', $storeViewCode));
    }
}
