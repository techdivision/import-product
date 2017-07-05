<?php

/**
 * TechDivision\Import\Product\Observers\ProductAttributeObserver
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

use TechDivision\Import\Observers\AbstractAttributeObserver;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;

/**
 * Observer that creates/updates the product's attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductAttributeObserver extends AbstractAttributeObserver
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
     * @return \TechDivision\Import\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    protected function getProductBunchProcessor()
    {
        return $this->productBunchProcessor;
    }

    /**
     * Persist's the passed varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistVarcharAttribute($attribute)
    {
        $this->getProductBunchProcessor()->persistProductVarcharAttribute($attribute);
    }

    /**
     * Persist's the passed integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistIntAttribute($attribute)
    {
        $this->getProductBunchProcessor()->persistProductIntAttribute($attribute);
    }

    /**
     * Persist's the passed decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistDecimalAttribute($attribute)
    {
        $this->getProductBunchProcessor()->persistProductDecimalAttribute($attribute);
    }

    /**
     * Persist's the passed datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistDatetimeAttribute($attribute)
    {
        $this->getProductBunchProcessor()->persistProductDatetimeAttribute($attribute);
    }

    /**
     * Persist's the passed text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistTextAttribute($attribute)
    {
        $this->getProductBunchProcessor()->persistProductTextAttribute($attribute);
    }
}
