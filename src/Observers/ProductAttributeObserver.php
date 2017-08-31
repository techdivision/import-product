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

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
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
     * @return \TechDivision\Import\Product\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    protected function getProductBunchProcessor()
    {
        return $this->productBunchProcessor;
    }

    /**
     * Return's the PK to create the product => attribute relation.
     *
     * @return integer The PK to create the relation with
     */
    protected function getPrimaryKey()
    {
        return $this->getSubject()->getLastEntityId();
    }

    /**
     * Return's the PK column name to create the product => attribute relation.
     *
     * @return string The PK column name
     */
    protected function getPrimaryKeyMemberName()
    {
        return MemberNames::ENTITY_ID;
    }

    /**
     * Return's the column name that contains the primary key.
     *
     * @return string the column name that contains the primary key
     */
    protected function getPrimaryKeyColumnName()
    {
        return ColumnKeys::SKU;
    }

    /**
     * Queries whether or not the passed PK and store view code has already been processed.
     *
     * @param string $pk            The PK to check been processed
     * @param string $storeViewCode The store view code to check been processed
     *
     * @return boolean TRUE if the PK and store view code has been processed, else FALSE
     */
    protected function storeViewHasBeenProcessed($pk, $storeViewCode)
    {
        return $this->getSubject()->storeViewHasBeenProcessed($pk, $storeViewCode);
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

    /**
     * Delete's the datetime attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteDatetimeAttribute(array $row, $name = null)
    {
        $this->getProductBunchProcessor()->deleteProductDatetimeAttribute($row, $name);
    }

    /**
     * Delete's the decimal attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteDecimalAttribute(array $row, $name = null)
    {
        $this->getProductBunchProcessor()->deleteProductDecimalAttribute($row, $name);
    }

    /**
     * Delete's the integer attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteIntAttribute(array $row, $name = null)
    {
        $this->getProductBunchProcessor()->deleteProductIntAttribute($row, $name);
    }

    /**
     * Delete's the text attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteTextAttribute(array $row, $name = null)
    {
        $this->getProductBunchProcessor()->deleteProductTextAttribute($row, $name);
    }

    /**
     * Delete's the varchar attribute with the passed value ID.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteVarcharAttribute(array $row, $name = null)
    {
        return $this->getProductBunchProcessor()->deleteProductVarcharAttribute($row, $name);
    }
}
