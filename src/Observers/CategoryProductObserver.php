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
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

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
     * Set's the actual category path that has to be processed.
     *
     * @param string $path The category path
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Return's the category path that has to be processed.
     *
     * @return string The category path
     */
    public function getPath()
    {
        return $this->path;
    }

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

        // initialize the row
        $this->setRow($row);

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->isLastSku($this->getValue(ColumnKeys::SKU))) {
            return $row;
        }

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    public function process()
    {

        // query whether or not, product => website relations has been specified
        if (!$this->hasValue(ColumnKeys::CATEGORIES)) {
            return;
        }

        // append the category => product relations found
        $paths = explode(',', $this->getValue(ColumnKeys::CATEGORIES));
        foreach ($paths as $path) {
            // load the category for the found path
            $this->setPath(trim($path));
            // prepare the product website relation attributes
            $attr = $this->prepareAttributes();

            // create the category product relation
            $categoryProduct = $this->initializeCategoryProduct($attr);
            $this->persistCategoryProduct($categoryProduct);

            // add the category ID to the list => necessary to create the URL rewrites later!
            $this->addProductCategoryId($categoryProduct[MemberNames::CATEGORY_ID]);
        }
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    public function prepareAttributes()
    {

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getLastEntityId();

        // load the category for the found path
        $category = $this->getCategoryByPath($this->getPath());

        // return the prepared category product relation
        return $this->initializeEntity(
            array(
                MemberNames::CATEGORY_ID => $category[MemberNames::ENTITY_ID],
                MemberNames::PRODUCT_ID  => $lastEntityId,
                MemberNames::POSITION    => 0
            )
        );
    }

    /**
     * Initialize the category product with the passed attributes and returns an instance.
     *
     * @param array $attr The category product attributes
     *
     * @return array The initialized category product
     */
    public function initializeCategoryProduct(array $attr)
    {
        return $attr;
    }

    /**
     * Add the passed category ID to the product's category list.
     *
     * @param integer $categoryId The category ID to add
     *
     * @return void
     */
    public function addProductCategoryId($categoryId)
    {
        $this->getSubject()->addProductCategoryId($categoryId);
    }

    /**
     * Persist's the passed category product relation.
     *
     * @param array $categoryProduct The category product relation to persist
     *
     * @return void
     */
    public function persistCategoryProduct($categoryProduct)
    {
        $this->getSubject()->persistCategoryProduct($categoryProduct);
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     */
    public function getCategoryByPath($path)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }
}
