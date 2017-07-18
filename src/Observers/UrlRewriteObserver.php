<?php

/**
 * TechDivision\Import\Product\Observers\UrlRewriteObserver
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

use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\CoreConfigDataKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Utils\StoreViewCodes;

/**
 * Observer that creates/updates the product's URL rewrites.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class UrlRewriteObserver extends AbstractProductImportObserver
{

    /**
     * The entity type to load the URL rewrites for.
     *
     * @var string
     */
    const ENTITY_TYPE = 'product';

    /**
     * The URL key from the CSV file column that has to be processed by the observer.
     *
     * @var string
     */
    protected $urlKey;

    /**
     * The actual category ID to process.
     *
     * @var integer
     */
    protected $categoryId;

    /**
     * The actual entity ID to process.
     *
     * @var integer
     */
    protected $entityId;

    /**
     * The array with the URL rewrites that has to be created.
     *
     * @var array
     */
    protected $urlRewrites = array();

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
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->hasBeenProcessed($this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // try to load the URL key, return immediately if not possible
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            $this->urlKey = $urlKey = $this->getValue(ColumnKeys::URL_KEY);
        } else {
            return;
        }

        // initialize the store view code
        $this->prepareStoreViewCode();

        // prepare the URL rewrites
        $this->prepareUrlRewrites();

        // iterate over the categories and create the URL rewrites
        foreach ($this->urlRewrites as $categoryId => $urlRewrite) {
            // initialize and persist the URL rewrite
            if ($urlRewrite = $this->initializeUrlRewrite($urlRewrite)) {
                // initialize URL rewrite and catagory ID
                $this->categoryId = $categoryId;
                $this->entityId = $urlRewrite[MemberNames::ENTITY_ID];
                $this->urlRewriteId = $this->persistUrlRewrite($urlRewrite);

                // initialize and persist the URL rewrite product => category relation
                $urlRewriteProductCategory = $this->initializeUrlRewriteProductCategory(
                    $this->prepareUrlRewriteProductCategoryAttributes()
                );

                // persist the URL rewrite product category relation
                $this->persistUrlRewriteProductCategory($urlRewriteProductCategory);
            }
        }

        // if changed, override the URL key with the new one
        if ($urlKey !== $this->urlKey) {
            $this->setValue(ColumnKeys::URL_KEY, $this->urlKey);
        }
    }

    /**
     * Initialize the category product with the passed attributes and returns an instance.
     *
     * @param array $attr The category product attributes
     *
     * @return array The initialized category product
     */
    protected function initializeUrlRewrite(array $attr)
    {
        return $attr;
    }

    /**
     * Initialize the URL rewrite product => category relation with the passed attributes
     * and returns an instance.
     *
     * @param array $attr The URL rewrite product => category relation attributes
     *
     * @return array The initialized URL rewrite product => category relation
     */
    protected function initializeUrlRewriteProductCategory($attr)
    {
        return $attr;
    }

    /**
     * Prepare's the URL rewrites that has to be created/updated.
     *
     * @return void
     */
    protected function prepareUrlRewrites()
    {

        // (re-)initialize the array for the URL rewrites
        $this->urlRewrites = array();

        // load the root category, because we need that to create the default product URL rewrite
        $rootCategory = $this->getRootCategory();

        // query whether or not categories has to be used as product URL suffix
        $productCategoryIds = array();
        if ($this->getCoreConfigData(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_USE_CATEGORIES, false)) {
            // if yes, add the category IDs of the products
            $productCategoryIds = $this->getProductCategoryIds();
        }

        // at least, add the root category ID to the category => product relations
        $productCategoryIds[$rootCategory[MemberNames::ENTITY_ID]] = $this->getLastEntityId();

        // prepare the URL rewrites
        foreach ($productCategoryIds as $categoryId => $entityId) {
            // set category/entity ID
            $this->categoryId = $categoryId;
            $this->entityId = $entityId;

            // prepare the attributes for each URL rewrite
            $this->urlRewrites[$categoryId] = $this->prepareAttributes();
        }
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // load the store ID to use
        $storeId = $this->getRowStoreId();

        // load the category to create the URL rewrite for
        $category = $this->getCategory($this->categoryId);

        // initialize the values
        $requestPath = $this->prepareRequestPath($category);
        $targetPath = $this->prepareTargetPath($category);
        $metadata = serialize($this->prepareMetadata($category));

        // return the prepared URL rewrite
        return $this->initializeEntity(
            array(
                MemberNames::ENTITY_TYPE      => UrlRewriteObserver::ENTITY_TYPE,
                MemberNames::ENTITY_ID        => $this->entityId,
                MemberNames::REQUEST_PATH     => $requestPath,
                MemberNames::TARGET_PATH      => $targetPath,
                MemberNames::REDIRECT_TYPE    => 0,
                MemberNames::STORE_ID         => $storeId,
                MemberNames::DESCRIPTION      => null,
                MemberNames::IS_AUTOGENERATED => 1,
                MemberNames::METADATA         => $metadata
            )
        );
    }

    /**
     * Prepare's the URL rewrite product => category relation attributes.
     *
     * @return array The prepared attributes
     */
    protected function prepareUrlRewriteProductCategoryAttributes()
    {

        // return the prepared product
        return $this->initializeEntity(
            array(
                MemberNames::PRODUCT_ID => $this->entityId,
                MemberNames::CATEGORY_ID => $this->categoryId,
                MemberNames::URL_REWRITE_ID => $this->urlRewriteId
            )
        );
    }

    /**
     * Prepare's the target path for a URL rewrite.
     *
     * @param array $category The categroy with the URL path
     *
     * @return string The target path
     */
    protected function prepareTargetPath(array $category)
    {

        // load the actual entity ID
        $lastEntityId = $this->getLastEntityId();

        // query whether or not, the category is the root category
        if ($this->isRootCategory($category)) {
            $targetPath = sprintf('catalog/product/view/id/%d', $lastEntityId);
        } else {
            $targetPath = sprintf('catalog/product/view/id/%d/category/%d', $lastEntityId, $category[MemberNames::ENTITY_ID]);
        }

        // return the target path
        return $targetPath;
    }

    /**
     * Prepare's the request path for a URL rewrite or the target path for a 301 redirect.
     *
     * @param array $category The categroy with the URL path
     *
     * @return string The request path
     * @throws \RuntimeException Is thrown, if the passed category has no or an empty value for attribute "url_path"
     */
    protected function prepareRequestPath(array $category)
    {

        // load the product URL suffix to use
        $urlSuffix = $this->getCoreConfigData(CoreConfigDataKeys::CATALOG_SEO_PRODUCT_URL_SUFFIX, '.html');

        // create a unique URL key, if this is a new URL rewrite
        $this->urlKey = $this->makeUrlKeyUnique($this->urlKey);

        // query whether or not, the category is the root category
        if ($this->isRootCategory($category)) {
            return sprintf('%s%s', $this->urlKey, $urlSuffix);
        } else {
            // query whether or not the category's "url_path" attribute, necessary to create a valid "request_path", is available
            if (isset($category[MemberNames::URL_PATH]) && $category[MemberNames::URL_PATH]) {
                return sprintf('%s/%s%s', $category[MemberNames::URL_PATH], $this->urlKey, $urlSuffix);
            }
        }

        // throw an exception if the category's "url_path" attribute is NOT available
        throw new \RuntimeException(
            sprintf(
                'Can\'t find mandatory attribute "%s" for category ID "%d", necessary to build a valid "request_path"',
                MemberNames::URL_PATH,
                $category[MemberNames::ENTITY_ID]
            )
        );
    }

    /**
     * Prepare's the URL rewrite's metadata with the passed category values.
     *
     * @param array $category The category used for preparation
     *
     * @return array The metadata
     */
    protected function prepareMetadata(array $category)
    {

        // initialize the metadata
        $metadata = array();

        // query whether or not, the passed category IS the root category
        if ($this->isRootCategory($category)) {
            return $metadata;
        }

        // if not, set the category ID in the metadata
        $metadata['category_id'] = $category[MemberNames::ENTITY_ID];

        // return the metadata
        return $metadata;
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
        $entityTypeId = $entityType[MemberNames::ENTITY_TYPE_ID];

        // initialize the query parameters
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

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
     * Return's the root category for the actual view store.
     *
     * @return array The store's root category
     * @throws \Exception Is thrown if the root category for the passed store code is NOT available
     */
    protected function getRootCategory()
    {
        return $this->getSubject()->getRootCategory();
    }

    /**
     * Return's TRUE if the passed category IS the root category, else FALSE.
     *
     * @param array $category The category to query
     *
     * @return boolean TRUE if the passed category IS the root category
     */
    protected function isRootCategory(array $category)
    {

        // load the root category
        $rootCategory = $this->getRootCategory();

        // compare the entity IDs and return the result
        return $rootCategory[MemberNames::ENTITY_ID] === $category[MemberNames::ENTITY_ID];
    }

    /**
     * Return's the list with category IDs the product is related with.
     *
     * @return array The product's category IDs
     */
    protected function getProductCategoryIds()
    {
        return $this->getSubject()->getProductCategoryIds();
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param integer $categoryId The ID of the category to return
     *
     * @return array The category data
     */
    protected function getCategory($categoryId)
    {
        return $this->getSubject()->getCategory($categoryId);
    }

    /**
     * Persist's the URL rewrite with the passed data.
     *
     * @param array $row The URL rewrite to persist
     *
     * @return string The ID of the persisted entity
     */
    protected function persistUrlRewrite($row)
    {
        return $this->getProductBunchProcessor()->persistUrlRewrite($row);
    }

    /**
     * Persist's the URL rewrite product => category relation with the passed data.
     *
     * @param array $row The URL rewrite product => category relation to persist
     *
     * @return void
     */
    protected function persistUrlRewriteProductCategory($row)
    {
        return $this->getProductBunchProcessor()->persistUrlRewriteProductCategory($row);
    }
}
