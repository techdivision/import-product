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

use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\Filter\ConvertLiteralUrl;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

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
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param array $row The row to handle
     *
     * @return array The modified row
     * @see \TechDivision\Import\Product\Observers\ImportObserverInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($row[$headers[ColumnKeys::SKU]])) {
            return $row;
        }

        // prepare the URL key, return immediately if not available
        if ($this->prepareUrlKey($row) == null) {
            return $row;
        }

        // initialize the store view code
        $this->setStoreViewCode($row[$headers[ColumnKeys::STORE_VIEW_CODE]] ?: StoreViewCodes::DEF);

        // load the ID of the last entity
        $lastEntityId = $this->getLastEntityId();

        // initialize the entity type to use
        $entityType = UrlRewriteObserver::ENTITY_TYPE;

        // load the product category IDs
        $productCategoryIds = $this->getProductCategoryIds();

        // load the URL rewrites for the entity type and ID
        $urlRewrites = $this->getUrlRewritesByEntityTypeAndEntityId($entityType, $lastEntityId);

        // prepare the existing URLs => unserialize the metadata
        $existingProductCategoryUrlRewrites = $this->prepareExistingCategoryUrlRewrites($urlRewrites);

        // delete/create/update the URL rewrites
        $this->deleteUrlRewrites($existingProductCategoryUrlRewrites);
        $this->updateUrlRewrites($row, array_intersect_key($existingProductCategoryUrlRewrites, $productCategoryIds));
        $this->createUrlRewrites($row, $productCategoryIds);

        // returns the row
        return $row;
    }

    /**
     * Prepare's and set's the URL key from the passed row of the CSV file.
     *
     * @param array $row The row with the CSV data
     *
     * @return boolean TRUE, if the URL key has been prepared, else FALSE
     */
    protected function prepareUrlKey($row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not we've a URL key available in the CSV file row
        if (isset($row[$headers[ColumnKeys::URL_KEY]])) {
            $urlKey = $row[$headers[ColumnKeys::URL_KEY]];
        }

        // query whether or not an URL key has been specified in the CSV file
        if (empty($urlKey)) {
            // if not, try to use the product name
            if (isset($row[$headers[ColumnKeys::NAME]])) {
                $productName = $row[$headers[ColumnKeys::NAME]];
            }

            // if nor URL key AND product name are empty, return immediately
            if (empty($productName)) {
                return false;
            }

            // initialize the URL key with product name
            $urlKey = $productName;
        }

        // convert and set the URL key
        $this->setUrlKey($this->convertNameToUrlKey($urlKey));

        // return TRUE if the URL key has been prepared
        return true;
    }

    /**
     * Set's the prepared URL key.
     *
     * @param string $urlKey The prepared URL key
     *
     * @return void
     */
    protected function setUrlKey($urlKey)
    {
        $this->urlKey = $urlKey;
    }

    /**
     * Return's the prepared URL key.
     *
     * @return string The prepared URL key
     */
    protected function getUrlKey()
    {
        return $this->urlKey;
    }

    /**
     * Initialize's and return's the URL key filter.
     *
     * @return \TechDivision\Import\Product\Utils\ConvertLiteralUrl The URL key filter
     */
    protected function getUrlKeyFilter()
    {
        return new ConvertLiteralUrl();
    }

    /**
     * Convert's the passed string into a valid URL key.
     *
     * @param string $string The string to be converted, e. g. the product name
     *
     * @return string The converted string as valid URL key
     */
    protected function convertNameToUrlKey($string)
    {
        return $this->getUrlKeyFilter()->filter($string);
    }

    /**
     * Convert's the passed URL rewrites into an array with the category ID from the
     * metadata as key and the URL rewrite as value.
     *
     * If now category ID can be found in the metadata, the ID of the store's root
     * category is used.
     *
     * @param array $urlRewrites The URL rewrites to convert
     *
     * @return array The converted array with the de-serialized category IDs as key
     */
    protected function prepareExistingCategoryUrlRewrites(array $urlRewrites)
    {

        // initialize the array for the existing URL rewrites
        $existingProductCategoryUrlRewrites = array();

        // load the store's root category
        $rootCategory = $this->getRootCategory();

        // iterate over the URL rewrites and convert them
        foreach ($urlRewrites as $urlRewrite) {
            // initialize the array with the metadata
            $metadata = array();

            // de-serialize the category ID from the metadata
            if ($md = $urlRewrite['metadata']) {
                $metadata = unserialize($md);
            }

            // use the store's category ID if not serialized metadata is available
            if (!isset($metadata['category_id'])) {
                $metadata['category_id'] = $rootCategory[MemberNames::ENTITY_ID];
            }

            // append the URL rewrite with the found category ID
            $existingProductCategoryUrlRewrites[$metadata['category_id']] = $urlRewrite;
        }

        // return the array with the existing URL rewrites
        return $existingProductCategoryUrlRewrites;
    }

    /**
     * Remove's the URL rewrites with the passed data.
     *
     * @param array $existingProductCategoryUrlRewrites The array with the URL rewrites to remove
     *
     * @return void
     */
    protected function deleteUrlRewrites(array $existingProductCategoryUrlRewrites)
    {

        // query whether or not we've any URL rewrites that have to be removed
        if (sizeof($existingProductCategoryUrlRewrites) === 0) {
            return;
        }

        // remove the URL rewrites
        foreach ($existingProductCategoryUrlRewrites as $categoryId => $urlRewrite) {
            $this->removeUrlRewrite(array(MemberNames::URL_REWRITE_ID => $urlRewrite[MemberNames::URL_REWRITE_ID]));
        }
    }

    /**
     * Create's the URL rewrites from the passed data.
     *
     * @param array $row                The data to create the URL rewrite from
     * @param array $productCategoryIds The categories to create a URL rewrite for
     *
     * @return void
     */
    protected function createUrlRewrites(array $row, array $productCategoryIds)
    {

        // query whether or not if there is any category to create a URL rewrite for
        if (sizeof($productCategoryIds) === 0) {
            return;
        }

        // load the header information
        $headers = $this->getHeaders();

        // iterate over the categories and create the URL rewrites
        foreach ($productCategoryIds as $categoryId => $entityId) {
            // load the category to create the URL rewrite for
            $category = $this->getCategory($categoryId);

            // initialize the values
            $requestPath = $this->prepareRequestPath($row, $category);
            $targetPath = $this->prepareTargetPath($category);
            $metadata = serialize($this->prepareMetadata($category));

            // initialize the URL rewrite data
            $params = array('product', $entityId, $requestPath, $targetPath, 0, 1, null, 1, $metadata);

            // create the URL rewrite
            $this->persistUrlRewrite($params);
        }
    }

    /**
     * Update's existing URL rewrites by creating 301 redirect URL rewrites for each.
     *
     * @param array $row                                The row with the actual data
     * @param array $existingProductCategoryUrlRewrites The array with the existing URL rewrites
     *
     * @return void
     */
    protected function updateUrlRewrites(array $row, array $existingProductCategoryUrlRewrites)
    {

        // query whether or not, we've existing URL rewrites that need to be redirected
        if (sizeof($existingProductCategoryUrlRewrites) === 0) {
            return;
        }

        // load the header information
        $headers = $this->getHeaders();

        // iterate over the URL redirects that have to be redirected
        foreach ($existingProductCategoryUrlRewrites as $categoryId => $urlRewrite) {
            // load the category data
            $category = $this->getCategory($categoryId);

            // initialize the values
            $entityId = $urlRewrite[MemberNames::ENTITY_ID];
            $requestPath = sprintf('%s', $urlRewrite['request_path']);
            $targetPath = $this->prepareTargetPathForRedirect($row, $category);
            $metadata = serialize($this->prepareMetadata($category));

            // initialize the URL rewrite data
            $params = array('product', $entityId, $requestPath, $targetPath, 301, 1, null, 0, $metadata);

            // create the 301 redirect URL rewrite
            $this->persistUrlRewrite($params);
        }
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

        // initialize the target path
        $targetPath = '';

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
     * Prepare's the request path for a URL rewrite.
     *
     * @param array $row      The actual row with the data
     * @param array $category The categroy with the URL path
     *
     * @return string The request path
     */
    protected function prepareRequestPath(array $row, array $category)
    {

        // load the header information
        $headers = $this->getHeaders();

        // initialize the request path
        $requestPath = '';

        // query whether or not, the category is the root category
        if ($this->isRootCategory($category)) {
            $requestPath = sprintf('%s.html', $this->getUrlKey());
        } else {
            $requestPath = sprintf('%s/%s.html', $category[MemberNames::URL_PATH], $this->getUrlKey());
        }

        // return the request path
        return $requestPath;
    }

    /**
     * Prepare's the target path for a 301 redirect URL rewrite.
     *
     * @param array $row      The actual row with the data
     * @param array $category The categroy with the URL path
     *
     * @return string The target path
     */
    protected function prepareTargetPathForRedirect(array $row, array $category)
    {

        // load the header information
        $headers = $this->getHeaders();

        // initialize the target path
        $targetPath = '';

        // query whether or not, the category is the root category
        if ($this->isRootCategory($category)) {
            $targetPath = sprintf('%s.html', $this->getUrlKey());
        } else {
            $targetPath = sprintf('%s/%s.html', $category[MemberNames::URL_PATH], $this->getUrlKey());
        }

        // return the target path
        return $targetPath;
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
     * Set's the store view code the create the product/attributes for.
     *
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    public function setStoreViewCode($storeViewCode)
    {
        $this->getSubject()->setStoreViewCode($storeViewCode);
    }

    /**
     * Return's the root category for the actual view store.
     *
     * @return array The store's root category
     * @throws \Exception Is thrown if the root category for the passed store code is NOT available
     */
    public function getRootCategory()
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
    public function isRootCategory(array $category)
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
    public function getProductCategoryIds()
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
    public function getCategory($categoryId)
    {
        return $this->getSubject()->getCategory($categoryId);
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
        return $this->getSubject()->getUrlRewritesByEntityTypeAndEntityId($entityType, $entityId);
    }

    /**
     * Persist's the URL write with the passed data.
     *
     * @param array $row The URL rewrite to persist
     *
     * @return void
     */
    public function persistUrlRewrite($row)
    {
        $this->getSubject()->persistUrlRewrite($row);
    }

    /**
     * Delete's the URL rewrite with the passed attributes.
     *
     * @param array $row The attributes of the entity to remove
     *
     * @return void
     */
    public function removeUrlRewrite($row)
    {
        $this->getSubject()->removeUrlRewrite($row);
    }
}
