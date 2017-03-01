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

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Utils\Filter\ConvertLiteralUrl;
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
            $this->urlKey = $this->getValue(ColumnKeys::URL_KEY);
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

        // add the root category ID to the category => product relations
        $productCategoryIds = $this->getProductCategoryIds();
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
     * @return arry The prepared attributes
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
        $lastEntityId = $this->getPrimaryKey();

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
     */
    protected function prepareRequestPath(array $category)
    {

        // query whether or not, the category is the root category
        if ($this->isRootCategory($category)) {
            $requestPath = sprintf('%s.html', $this->urlKey);
        } else {
            $requestPath = sprintf('%s/%s.html', $category[MemberNames::URL_PATH], $this->urlKey);
        }

        // return the request path
        return $requestPath;
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
     * Initialize's and return's the URL key filter.
     *
     * @return \TechDivision\Import\Utils\ConvertLiteralUrl The URL key filter
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
     * Return's the store ID of the actual row, or of the default store
     * if no store view code is set in the CSV file.
     *
     * @param string|null $default The default store view code to use, if no store view code is set in the CSV file
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    protected function getRowStoreId($default = null)
    {
        return $this->getSubject()->getRowStoreId($default);
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
        return $this->getSubject()->persistUrlRewrite($row);
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
        return $this->getSubject()->persistUrlRewriteProductCategory($row);
    }

    /**
     * Return's the PK to create the product => attribute relation.
     *
     * @return integer The PK to create the relation with
     */
    protected function getPrimaryKey()
    {
        return $this->getLastEntityId();
    }
}
