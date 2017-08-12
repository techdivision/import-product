<?php

/**
 * TechDivision\Import\Product\Observers\UrlRewriteUpdateObserver
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

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\CoreConfigDataKeys;

/**
 * Observer that creates/updates the product's URL rewrites.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class UrlRewriteUpdateObserver extends UrlRewriteObserver
{

    /**
     * Array with the existing URL rewrites of the actual product.
     *
     * @var array
     */
    protected $existingUrlRewrites = array();

    /**
     * Return's the URL rewrite for the passed request path.
     *
     * @param string $requestPath The request path to return the URL rewrite for
     *
     * @return array|null The URL rewrite
     */
    protected function getExistingUrlRewrite($requestPath)
    {
        if (isset($this->existingUrlRewrites[$requestPath])) {
            return $this->existingUrlRewrites[$requestPath];
        }
    }

    /**
     * Remove's the passed URL rewrite from the existing one's.
     *
     * @param array $urlRewrite The URL rewrite to remove
     *
     * @return void
     */
    protected function removeExistingUrlRewrite(array $urlRewrite)
    {

        // load request path
        $requestPath = $urlRewrite[MemberNames::REQUEST_PATH];

        // query whether or not the URL rewrite exists and remove it, if available
        if (isset($this->existingUrlRewrites[$requestPath])) {
            unset($this->existingUrlRewrites[$requestPath]);
        }
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     * @see \TechDivision\Import\Product\Observers\UrlRewriteObserver::process()
     */
    protected function process()
    {

        // process the new URL rewrites first
        parent::process();

        // load the root category
        $rootCategory = $this->getRootCategory();

        // create redirect URL rewrites for the existing URL rewrites
        foreach ($this->existingUrlRewrites as $existingUrlRewrite) {
            // if the URL rewrite has been created manually
            if ((integer) $existingUrlRewrite[MemberNames::IS_AUTOGENERATED] === 0) {
                // do NOT create another redirect
                continue;
            }

            // if the URL rewrite already IS a redirect
            if ((integer) $existingUrlRewrite[MemberNames::REDIRECT_TYPE] !== 0) {
                // do NOT create another redirect
                continue;
            }

            // load the metadata from the existing URL rewrite
            $metadata = $this->getMetadata($existingUrlRewrite);

            // initialize the category with the root category
            $category = $rootCategory;

            // query whether or not, the existing URL rewrite has been replaced
            if (isset($this->urlRewrites[$metadata['category_id']])) {
                // if yes, load the category of the original one
                $category = $this->getCategory($metadata['category_id']);
            }

            // load target path/metadata for the actual category
            $targetPath = $this->prepareRequestPath($category);
            $metadata = serialize($this->prepareMetadata($category));

            // override data with the 301 configuration
            $attr = array(
                MemberNames::IS_AUTOGENERATED => 0,
                MemberNames::REDIRECT_TYPE    => 301,
                MemberNames::METADATA         => $metadata,
                MemberNames::TARGET_PATH      => $targetPath,
            );

            // merge and return the prepared URL rewrite
            $existingUrlRewrite = $this->mergeEntity($existingUrlRewrite, $attr);

            // create the URL rewrite
            $this->persistUrlRewrite($existingUrlRewrite);
        }
    }

    /**
     * Prepare's the URL rewrites that has to be created/updated.
     *
     * @return void
     * @see \TechDivision\Import\Product\Observers\UrlRewriteObserver::prepareUrlRewrites()
     */
    protected function prepareUrlRewrites()
    {

        // (re-)initialize the array for the existing URL rewrites
        $this->existingUrlRewrites = array();

        // prepare the new URL rewrites first
        parent::prepareUrlRewrites();

        // query whether or not 301 redirects have to be created
        if ($this->getSubject()->getCoreConfigData(CoreConfigDataKeys::CATALOG_SEO_SAVE_REWRITES_HISTORY, true)) {
            // load the existing URL rewrites of the actual entity
            $existingUrlRewrites = $this->getUrlRewritesByEntityTypeAndEntityIdAndStoreId(
                UrlRewriteObserver::ENTITY_TYPE,
                $this->getSubject()->getLastEntityId(),
                $this->getSubject()->getRowStoreId()
            );

            // prepare the existing URL rewrites to improve searching them by request path
            foreach ($existingUrlRewrites as $existingUrlRewrite) {
                $this->existingUrlRewrites[$existingUrlRewrite[MemberNames::REQUEST_PATH]] = $existingUrlRewrite;
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

        // try to load the URL rewrite for the store ID and request path
        if ($urlRewrite = $this->getExistingUrlRewrite($attr[MemberNames::REQUEST_PATH])) {
            // if a URL rewrite has been found, do NOT create a redirect
            $this->removeExistingUrlRewrite($urlRewrite);

            // if the found URL rewrite has been created manually
            if ((integer) $urlRewrite[MemberNames::IS_AUTOGENERATED] === 0) {
                // do NOT update it nor create a another redirect
                return false;
            }

            // if the found URL rewrite has been autogenerated, then update it
            return $this->mergeEntity($urlRewrite, $attr);
        }

        // simple return the attributes
        return $attr;
    }

    /**
     * Initialize the URL rewrite product => category relation with the passed attributes
     * and returns an instance.
     *
     * @param array $attr The URL rewrite product => category relation attributes
     *
     * @return array|null The initialized URL rewrite product => category relation
     */
    protected function initializeUrlRewriteProductCategory($attr)
    {

        // try to load the URL rewrite product category relation
        if ($urlRewriteProductCategory = $this->loadUrlRewriteProductCategory($attr[MemberNames::URL_REWRITE_ID])) {
            return $this->mergeEntity($urlRewriteProductCategory, $attr);
        }

        // simple return the URL rewrite product category
        return $attr;
    }

    /**
     * Return's the unserialized metadata of the passed URL rewrite. If the
     * metadata doesn't contain a category ID, the category ID of the root
     * category will be added.
     *
     * @param array $urlRewrite The URL rewrite to return the metadata for
     *
     * @return array The metadata of the passed URL rewrite
     */
    protected function getMetadata($urlRewrite)
    {

        // initialize the array with the metaddata
        $metadata = array();

        // try to unserialize the metadata from the passed URL rewrite
        if (isset($urlRewrite[MemberNames::METADATA])) {
            $metadata = unserialize($urlRewrite[MemberNames::METADATA]);
        }

        // query whether or not a category ID has been found
        if (isset($metadata['category_id'])) {
            // if yes, return the metadata
            return $metadata;
        }

        // if not, append the ID of the root category
        $rootCategory = $this->getRootCategory();
        $metadata['category_id'] = $rootCategory[MemberNames::ENTITY_ID];

        // and return the metadata
        return $metadata;
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
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     * @param integer $storeId    The store ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function getUrlRewritesByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId)
    {
        return $this->getProductBunchProcessor()->getUrlRewritesByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId);
    }

    /**
     * Return's the URL rewrite product category relation for the passed
     * URL rewrite ID.
     *
     * @param integer $urlRewriteId The URL rewrite ID to load the URL rewrite product category relation for
     *
     * @return array|false The URL rewrite product category relations
     */
    protected function loadUrlRewriteProductCategory($urlRewriteId)
    {
        return $this->getProductBunchProcessor()->loadUrlRewriteProductCategory($urlRewriteId);
    }
}
