<?php

/**
 * TechDivision\Import\Product\Observers\ProductAttributeObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Observers\ObserverFactoryInterface;
use TechDivision\Import\Observers\AbstractAttributeObserver;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Observers\StateDetectorAwareObserverInterface;
use TechDivision\Import\Utils\BackendTypeKeys;

/**
 * Observer that creates/updates the product's attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductAttributeObserver extends AbstractAttributeObserver implements StateDetectorAwareObserverInterface, ObserverFactoryInterface
{

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * The array with the column mappings that has to be computed.
     *
     * @var array
     */
    protected $columns = array();

    /**
     * The backend type => data type mappings.
     *
     * @var array
     */
    protected $backendTypeKeyMappings = array(
        BackendTypeKeys::BACKEND_TYPE_DATETIME => 'string',
        BackendTypeKeys::BACKEND_TYPE_DECIMAL => 'float',
        BackendTypeKeys::BACKEND_TYPE_FLOAT => 'float',
        BackendTypeKeys::BACKEND_TYPE_INT => 'integer',
        BackendTypeKeys::BACKEND_TYPE_STATIC => 'string',
        BackendTypeKeys::BACKEND_TYPE_TEXT => 'string',
        BackendTypeKeys::BACKEND_TYPE_VARCHAR => 'string'
    );

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor The product bunch processor instance
     * @param \TechDivision\Import\Observers\StateDetectorInterface|null           $stateDetector         The state detector instance to use
     */
    public function __construct(ProductBunchProcessorInterface $productBunchProcessor, StateDetectorInterface $stateDetector = null)
    {

        // initialize the bunch processor instance
        $this->productBunchProcessor = $productBunchProcessor;

        // pass the state detector to the parent method
        parent::__construct($stateDetector);
    }

    /**
     * Will be invoked by the observer visitor when a factory has been defined to create the observer instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The observer instance
     */
    public function createObserver(SubjectInterface $subject)
    {

        // load the attributes
        $attributes = $subject->getAttributes();

        // create the attribute name => type mapping
        foreach ($attributes as $attribute) {
            $this->columns[$attribute[MemberNames::ATTRIBUTE_ID]] = isset($this->backendTypeKeyMappings[$attribute[MemberNames::BACKEND_TYPE]]) ? $this->backendTypeKeyMappings[$attribute[MemberNames::BACKEND_TYPE]] : 'string';
        }

        // return the instance
        return $this;
    }

    /**
     * Returns an array of the columns with their types to detect state.
     *
     * @param array $attribute The attribute entity to return the columns for
     *
     * @return array The array with the column names as key and their type as value
     */
    public function getColumns(array $attribute = array())
    {
        return array(MemberNames::VALUE => $this->columns[$attribute[MemberNames::ATTRIBUTE_ID]]);
    }

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    protected function getAttributesByPrimaryKeyAndStoreId($pk, $storeId)
    {
        return array();
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
