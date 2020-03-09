<?php

/**
 * TechDivision\Import\Product\Observers\UrlKeyObserver
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

namespace TechDivision\Import\Product\Observers;

use Zend\Filter\FilterInterface;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Utils\Filter\UrlKeyFilterTrait;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;

/**
 * Observer that extracts the URL key from the product name and adds a two new columns
 * with the their values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class UrlKeyObserver extends AbstractProductImportObserver
{

    /**
     * The trait that provides string => URL key conversion functionality.
     *
     * @var \TechDivision\Import\Utils\Filter\UrlKeyFilterTrait
     */
    use UrlKeyFilterTrait;

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * Initialize the observer with the passed product bunch processor and filter instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor   The product bunch processor instance
     * @param \Zend\Filter\FilterInterface                                         $convertLiteralUrlFilter The URL filter instance
     */
    public function __construct(ProductBunchProcessorInterface $productBunchProcessor, FilterInterface $convertLiteralUrlFilter)
    {
        $this->productBunchProcessor = $productBunchProcessor;
        $this->convertLiteralUrlFilter = $convertLiteralUrlFilter;
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
     * Process the observer's business logic.
     *
     * @return void
     * @throws \Exception Is thrown, if either column "url_key" or "name" have a value set
     */
    protected function process()
    {

        // prepare the store view code
        $this->getSubject()->prepareStoreViewCode();

        // set the entity ID for the product with the passed SKU
        if ($product = $this->loadProduct($this->getValue(ColumnKeys::SKU))) {
            $this->setIds($product);
        } else {
            $this->setIds(array());
        }

        // query whether or not the URL key column has a value
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            $this->setValue(ColumnKeys::URL_KEY, $this->getValue(ColumnKeys::URL_KEY));
            return;
        }

        // query whether or not a product name is available
        if ($this->hasValue(ColumnKeys::NAME)) {
            $this->setValue(ColumnKeys::URL_KEY, $this->makeUrlKeyUnique($this->convertNameToUrlKey($this->getValue(ColumnKeys::NAME))));
            return;
        }

        // throw an exception, that the URL key can not be initialized and we're in admin store view
        if ($this->getSubject()->getStoreViewCode(StoreViewCodes::ADMIN) === StoreViewCodes::ADMIN) {
            throw new \Exception('Can\'t initialize the URL key because either columns "url_key" or "name" have a value set for default store view');
        }
    }

    /**
     * Temporarily persist's the IDs of the passed product.
     *
     * @param array $product The product to temporarily persist the IDs for
     *
     * @return void
     */
    protected function setIds(array $product)
    {
        $this->setLastEntityId(isset($product[MemberNames::ENTITY_ID]) ? $product[MemberNames::ENTITY_ID] : null);
    }

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    protected function setLastEntityId($lastEntityId)
    {
        $this->getSubject()->setLastEntityId($lastEntityId);
    }

    /**
     * Load's and return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to load
     *
     * @return array The product
     */
    protected function loadProduct($sku)
    {
        return $this->getProductBunchProcessor()->loadProduct($sku);
    }

    /**
     * Make's the passed URL key unique by adding the next number to the end.
     *
     * @param string $urlKey The URL key to make unique
     *
     * @return string The unique URL key
     */
    protected function makeUrlKeyUnique($urlKey)
    {

        // initialize the entity type ID
        $entityType = $this->getEntityType();
        $entityTypeId = (integer) $entityType[MemberNames::ENTITY_TYPE_ID];

        // initialize the store view ID, use the admin store view if no store view has
        // been set, because the default url_key value has been set in admin store view
        $storeId = $this->getSubject()->getRowStoreId(StoreViewCodes::ADMIN);

        // initialize the counter
        $counter = 0;

        // initialize the counters
        $matchingCounters = array();
        $notMatchingCounters = array();

        // pre-initialze the URL key to query for
        $value = $urlKey;

        do {
            // try to load the attribute
            $productVarcharAttribute = $this->getProductBunchProcessor()
                                            ->loadProductVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndValue(
                                                MemberNames::URL_KEY,
                                                $entityTypeId,
                                                $storeId,
                                                $value
                                            );

            // try to load the product's URL key
            if ($productVarcharAttribute) {
                // this IS the URL key of the passed entity
                if ($this->isUrlKeyOf($productVarcharAttribute)) {
                    $matchingCounters[] = $counter;
                } else {
                    $notMatchingCounters[] = $counter;
                }

                // prepare the next URL key to query for
                $value = sprintf('%s-%d', $urlKey, ++$counter);
            }
        } while ($productVarcharAttribute);

        // sort the array ascending according to the counter
        asort($matchingCounters);
        asort($notMatchingCounters);

        // this IS the URL key of the passed entity => we've an UPDATE
        if (sizeof($matchingCounters) > 0) {
            // load highest counter
            $counter = end($matchingCounters);
            // if the counter is > 0, we've to append it to the new URL key
            if ($counter > 0) {
                $urlKey = sprintf('%s-%d', $urlKey, $counter);
            }
        } elseif (sizeof($notMatchingCounters) > 0) {
            // create a new URL key by raising the counter
            $newCounter = end($notMatchingCounters);
            $urlKey = sprintf('%s-%d', $urlKey, ++$newCounter);
        }

        // return the passed URL key, if NOT
        return $urlKey;
    }

    /**
     * Return's the entity type for the configured entity type code.
     *
     * @return array The requested entity type
     * @throws \Exception Is thrown, if the requested entity type is not available
     */
    protected function getEntityType()
    {
        return $this->getSubject()->getEntityType();
    }

    /**
     * Return's TRUE, if the passed URL key varchar value IS related with the actual PK.
     *
     * @param array $productVarcharAttribute The varchar value to check
     *
     * @return boolean TRUE if the URL key is related, else FALSE
     */
    protected function isUrlKeyOf(array $productVarcharAttribute)
    {
        return $this->getSubject()->isUrlKeyOf($productVarcharAttribute);
    }
}
