<?php

/**
 * TechDivision\Import\Product\Observers\ProductWebsiteObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\ConfigurationKeys;

/**
 * Observer that creates/updates the product's website relations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductWebsiteObserver extends AbstractProductImportObserver
{

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor The product bunch processor instance
     */
    public function __construct(ProductBunchProcessorInterface $productBunchProcessor)
    {
        $this->productBunchProcessor = $productBunchProcessor;
    }

    /**
     * Return's the product bunch processor instance.
     *
     * @return \TechDivision\Import\Product\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    protected function getProductBunchProcessor()
    {
        return $this->productBunchProcessor;
    }

    /**
     * The actual website code that has to be processed.
     *
     * @var string
     */
    protected $code;

    /**
     * Set's the actual website code that has to be processed.
     *
     * @param string $code The website code
     *
     * @return void
     */
    protected function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Return's the webiste code that has to be processed.
     *
     * @return string The website code
     */
    protected function getCode()
    {
        return $this->code;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->hasBeenProcessed($sku = $this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // load the product => website relations
        $codes = $this->getValue(ColumnKeys::PRODUCT_WEBSITES, array(), array($this, 'explode'));

        // query whether or not we've to clean up existing website product relations
        if ($this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_WEBSITE_PRODUCT_RELATIONS) &&
            $this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_WEBSITE_PRODUCT_RELATIONS)
        ) {
            // initialize the array for the website IDs
            $websiteIds = array();

            // prepare the website IDs
            foreach ($codes as $code) {
                $websiteIds[$code] = $this->getStoreWebsiteIdByCode($code);
            }

            // load the available product websites
            if ($productWebsites = $this->loadProductWebsitesBySku($sku)) {
                // iterate over the products websites
                foreach ($productWebsites as $productWebsite) {
                    // if the product websit relation is in the CSV file, do nothing
                    if (in_array($productWebsite[MemberNames::WEBSITE_ID], $websiteIds)) {
                        continue;
                    }

                    // delete it, because we don't need it any longer
                    $this->deleteProductWebsite(
                        array(
                            MemberNames::PRODUCT_ID => $productWebsite[MemberNames::PRODUCT_ID],
                            MemberNames::WEBSITE_ID => $productWebsite[MemberNames::WEBSITE_ID]
                        )
                    );
                }
            }
        }

        // append the product => website relations found
        foreach ($codes as $code) {
            // set the code of the website that has to be processed
            $this->setCode($code);
            // prepare the product website relation attributes
            $attr = $this->prepareAttributes();

            try {
                // create the product website relation
                $productWebsite = $this->initializeProductWebsite($attr);
                $this->persistProductWebsite($productWebsite);
            } catch (\RuntimeException $re) {
                $this->getSystemLogger()->debug($re->getMessage());
            }
        }
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // load the website ID to relate the product with
        $websiteId = $this->getStoreWebsiteIdByCode($this->getCode());

        // return the prepared product
        return $this->initializeEntity(
            array(
                MemberNames::PRODUCT_ID => $lastEntityId,
                MemberNames::WEBSITE_ID => $websiteId
            )
        );
    }

    /**
     * Initialize the product website with the passed attributes and returns an instance.
     *
     * @param array $attr The product website attributes
     *
     * @return array The initialized product website
     * @throws \RuntimeException Is thrown, if the attributes can not be initialized
     */
    protected function initializeProductWebsite(array $attr)
    {
        return $attr;
    }

    /**
     * Load's and return's the product website relations for the product with the passed SKU.
     *
     * @param string $sku The SKU to of the product to load the product website relations for
     *
     * @return array The product website relations
     */
    protected function loadProductWebsitesBySku($sku)
    {
        return $this->getProductBunchProcessor()->loadProductWebsitesBySku($sku);
    }

    /**
     * Persist's the passed product website data.
     *
     * @param array $productWebsite The product website data to persist
     *
     * @return void
     */
    protected function persistProductWebsite(array $productWebsite)
    {
        $this->getProductBunchProcessor()->persistProductWebsite($productWebsite);
    }

    /**
     * Delete's the passed product website data.
     *
     * @param array $productWebsite The product website data to delete
     *
     * @return void
     */
    protected function deleteProductWebsite(array $productWebsite)
    {
        $this->getProductBunchProcessor()->deleteProductWebsite($productWebsite);
    }

    /**
     * Return's the store website for the passed code.
     *
     * @param string $code The code of the store website to return the ID for
     *
     * @return integer The store website ID
     */
    protected function getStoreWebsiteIdByCode($code)
    {
        return $this->getSubject()->getStoreWebsiteIdByCode($code);
    }
}
