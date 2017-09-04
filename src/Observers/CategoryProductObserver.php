<?php

/**
 * TechDivision\Import\Product\Observers\CategoryProductObserver
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
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Product\Utils\ConfigurationKeys;

/**
 * Observer that creates/updates the category product relations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class CategoryProductObserver extends AbstractProductImportObserver
{

    /**
     * The actual category path that has to be processed.
     *
     * @var string
     */
    protected $path;

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
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->hasBeenProcessed($sku = $this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // initialize the arrays for the new category product relations
        $categoryProducts = array();

        // load the category product relations found in the CSV file
        foreach ($this->getValue(ColumnKeys::CATEGORIES, array(), array($this, 'explode')) as $path) {
            // load the category for the found path
            $this->path = trim($path);

            // prepare the product category relation attributes
            if ($attr = $this->prepareAttributes()) {
                // initialize and persist the category product relation relation and add the
                // category ID to the list => necessary to create the URL rewrites later!
                $this->persistCategoryProduct($categoryProduct = $this->initializeCategoryProduct($attr));
                $this->addProductCategoryId($categoryId = $categoryProduct[MemberNames::CATEGORY_ID]);

                // tempoary persist the category ID
                $categoryProducts[] = $categoryId;
            }
        }

        // query whether or not we've to clean up existing category product relations
        if ($this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_CATEGORY_PRODUCT_RELATIONS) &&
            $this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_CATEGORY_PRODUCT_RELATIONS)
        ) {
            // load the existing category product relations for the product with the given SKU
            $existingCategoryProducts = $this->getProductBunchProcessor()->getCategoryProductsBySku($sku);

            // initialize the counter for the deleted category product relations
            $counter = 0;

            // clean up the existing category product relations
            foreach ($existingCategoryProducts as $categoryId => $categoryProduct) {
                // query whether or not the category product relation exists in the CSV file
                if (!in_array($categoryId, $categoryProducts)) {
                    // if not, delete it from the database
                    $this->getProductBunchProcessor()
                         ->deleteCategoryProduct(array(MemberNames::ENTITY_ID => $categoryProduct[MemberNames::ENTITY_ID]));

                    // raise the counter for the deleted category product relations
                    $counter++;
                }
            }

            // log a message (if any category product relations have been cleaned-up)
            if ($counter > 0) {
                $this->getSubject()
                     ->getSystemLogger()
                     ->warning(
                         $this->getSubject()->appendExceptionSuffix(
                             sprintf(
                                 'Cleaned-up %d category product relation(s) for product with SKU "%s"',
                                 $counter,
                                 $sku
                             )
                         )
                     );
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

        // load the subject
        $subject = $this->getSubject();

        // load the ID of the product that has been created recently
        $lastEntityId = $subject->getLastEntityId();

        try {
            // load the category for the found path
            $category = $this->getCategoryByPath($this->path);

            // return the prepared category product relation
            return $this->initializeEntity(
                array(
                    MemberNames::CATEGORY_ID => $category[MemberNames::ENTITY_ID],
                    MemberNames::PRODUCT_ID  => $lastEntityId,
                    MemberNames::POSITION    => 0
                )
            );

        } catch (\Exception $e) {
            // query whether or not debug mode has been enabled
            if ($subject->isDebugMode()) {
                $subject->getSystemLogger()->warning($subject->appendExceptionSuffix($e->getMessage()));
            } else {
                throw $e;
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
    protected function initializeCategoryProduct(array $attr)
    {
        return $attr;
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
     * Add the passed category ID to the product's category list.
     *
     * @param integer $categoryId The category ID to add
     *
     * @return void
     */
    protected function addProductCategoryId($categoryId)
    {
        $this->getSubject()->addProductCategoryId($categoryId);
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     */
    protected function getCategoryByPath($path)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }

    /**
     * Persist's the passed category product relation.
     *
     * @param array $categoryProduct The category product relation to persist
     *
     * @return string The ID of the persisted entity
     */
    protected function persistCategoryProduct($categoryProduct)
    {
        return $this->getProductBunchProcessor()->persistCategoryProduct($categoryProduct);
    }
}
