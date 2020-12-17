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

use TechDivision\Import\Product\Utils\ConfigurationKeys;
use Zend\Filter\FilterInterface;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Utils\Filter\UrlKeyFilterTrait;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Utils\UrlKeyUtilInterface;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;

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
     * The URL key utility instance.
     *
     * @var \TechDivision\Import\Utils\UrlKeyUtilInterface
     */
    protected $urlKeyUtil;

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
     * @param \TechDivision\Import\Utils\UrlKeyUtilInterface                       $urlKeyUtil              The URL key utility instance
     */
    public function __construct(
        ProductBunchProcessorInterface $productBunchProcessor,
        FilterInterface $convertLiteralUrlFilter,
        UrlKeyUtilInterface $urlKeyUtil
    ) {
        $this->productBunchProcessor = $productBunchProcessor;
        $this->convertLiteralUrlFilter = $convertLiteralUrlFilter;
        $this->urlKeyUtil = $urlKeyUtil;
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
            return;
        }

        // query whether or not the column `url_key` has a value
        if ($product &&
            !$this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::UPDATE_URL_KEY_FROM_NAME, true)
        ) {
            // product already exists and NO new URL key
            // has been specified in column `url_key`, so
            // we stop processing here
            return;
        }

        // query whether or not a product name is available
        if ($this->hasValue(ColumnKeys::NAME)) {
            $this->setValue(
                ColumnKeys::URL_KEY,
                $this->makeUnique(
                    $this->getSubject(),
                    $this->convertNameToUrlKey($this->getValue(ColumnKeys::NAME))
                )
            );
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
     * Returns the URL key utility instance.
     *
     * @return \TechDivision\Import\Utils\UrlKeyUtilInterface The URL key utility instance
     */
    protected function getUrlKeyUtil()
    {
        return $this->urlKeyUtil;
    }

    /**
     * Make's the passed URL key unique by adding the next number to the end.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject The subject to make the URL key unique for
     * @param string                                                    $urlKey  The URL key to make unique
     *
     * @return string The unique URL key
     */
    protected function makeUnique(UrlKeyAwareSubjectInterface $subject, $urlKey)
    {
        return $this->getUrlKeyUtil()->makeUnique($subject, $urlKey);
    }
}
