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

use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

/**
 * Observer that creates/updates the product's attributes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductAttributeObserver extends AbstractProductImportObserver
{

    /**
     * The ID of the attribute to create the values for.
     *
     * @var integer
     */
    protected $attributeId;

    /**
     * The attribute code of the attribute to create the values for.
     *
     * @var string
     */
    protected $attributeCode;

    /**
     * The backend type of the attribute to create the values for.
     *
     * @var string
     */
    protected $backendType;

    /**
     * The attribute value to process.
     *
     * @var mixed
     */
    protected $attributeValue;

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // initialize the store view code
        $this->prepareStoreViewCode();

        // load the attributes by the found attribute set
        $attributes = $this->getAttributes();

        // iterate over the attribute related by the found attribute set
        foreach ($attributes as $attribute) {
            // load the attribute code/ID
            $attributeCode = $attribute[MemberNames::ATTRIBUTE_CODE];
            $attributeId = (integer) $attribute[MemberNames::ATTRIBUTE_ID];

            // query weather or not we've a mapping, if yes, map the attribute code
            $attributeCode = $this->mapAttributeCodeByHeaderMapping($attributeCode);

            // query whether or not we've a attribute value found
            $attributeValue = $this->getValue($attributeCode);
            if ($attributeValue == null) {
                continue;
            }

            // load the backend type => to find the apropriate entity
            $backendType = $attribute[MemberNames::BACKEND_TYPE];
            if ($backendType == null) {
                $this->getSystemLogger()
                     ->warning(sprintf('Found EMTPY backend type for attribute %s', $attributeCode));
                continue;
            }

            // load the supported backend types
            $backendTypes = $this->getBackendTypes();

            // query whether or not we've found a supported backend type
            if (isset($backendTypes[$backendType])) {
                // initialize attribute ID/code and backend type
                $this->setAttributeId($attributeId);
                $this->setAttributeCode($attributeCode);
                $this->setBackendType($backendType);

                // initialize the persist method for the found backend type
                list ($persistMethod, ) = $backendTypes[$backendType];

                // set the attribute value
                $this->setAttributeValue($attributeValue);

                // load the prepared values
                $entity = $this->initializeAttribute($this->prepareAttributes());

                // persist the attribute
                $this->$persistMethod($entity);

            } else {
                // log the debug message
                $this->getSystemLogger()->debug(
                    sprintf('Found invalid backend type %s for attribute %s', $backendType, $attributeCode)
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

        // load the attribute value
        $attributeValue = $this->getAttributeValue();

        // laod the callbacks for the actual attribute code
        $callbacks = $this->getCallbacksByType($this->getAttributeCode());

        // invoke the pre-cast callbacks
        foreach ($callbacks as $callback) {
            $attributeValue = $callback->handle($attributeValue);
        }

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getPrimaryKey();

        // load the ID of the attribute to create the values for
        $attributeId = $this->getAttributeId();

        // load the store ID
        $storeId = $this->getRowStoreId(StoreViewCodes::ADMIN);

        // load the backend type of the actual attribute
        $backendType = $this->getBackendType();

        // cast the value based on the backend type
        $castedValue = $this->castValueByBackendType($backendType, $attributeValue);

        // prepare the attribute values
        return $this->initializeEntity(
            array(
                MemberNames::ENTITY_ID    => $lastEntityId,
                MemberNames::ATTRIBUTE_ID => $attributeId,
                MemberNames::STORE_ID     => $storeId,
                MemberNames::VALUE        => $castedValue
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
    protected function initializeAttribute(array $attr)
    {
        return $attr;
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

    /**
     * Set's the attribute value to process.
     *
     * @param mixed $attributeValue The attribute value
     *
     * @return void
     */
    protected function setAttributeValue($attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }

    /**
     * Return's the attribute value to process.
     *
     * @return mixed The attribute value
     */
    protected function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * Set's the backend type of the attribute to create the values for.
     *
     * @param string $backendType The backend type
     *
     * @return void
     */
    protected function setBackendType($backendType)
    {
        $this->backendType = $backendType;
    }

    /**
     * Return's the backend type of the attribute to create the values for.
     *
     * @return string The backend type
     */
    protected function getBackendType()
    {
        return $this->backendType;
    }

    /**
     * Set's the attribute code of the attribute to create the values for.
     *
     * @param string $attributeCode The attribute code
     *
     * @return void
     */
    protected function setAttributeCode($attributeCode)
    {
        $this->attributeCode = $attributeCode;
    }

    /**
     * Return's the attribute code of the attribute to create the values for.
     *
     * @return string The attribute code
     */
    protected function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     * Set's the ID of the attribute to create the values for.
     *
     * @param integer $attributeId The attribute ID
     *
     * @return void
     */
    protected function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
    }

    /**
     * Return's the ID of the attribute to create the values for.
     *
     * @return integer The attribute ID
     */
    protected function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * Map the passed attribute code, if a header mapping exists and return the
     * mapped mapping.
     *
     * @param string $attributeCode The attribute code to map
     *
     * @return string The mapped attribute code, or the original one
     */
    protected function mapAttributeCodeByHeaderMapping($attributeCode)
    {
        return $this->getSubject()->mapAttributeCodeByHeaderMapping($attributeCode);
    }

    /**
     * Return's the array with callbacks for the passed type.
     *
     * @param string $type The type of the callbacks to return
     *
     * @return array The callbacks
     */
    protected function getCallbacksByType($type)
    {
        return $this->getSubject()->getCallbacksByType($type);
    }

    /**
     * Return's mapping for the supported backend types (for the product entity) => persist methods.
     *
     * @return array The mapping for the supported backend types
     */
    protected function getBackendTypes()
    {
        return $this->getSubject()->getBackendTypes();
    }

    /**
     * Return's the attributes for the attribute set of the product that has to be created.
     *
     * @return array The attributes
     * @throws \Exception
     */
    protected function getAttributes()
    {
        return $this->getSubject()->getAttributes();
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
     * Persist's the passed product varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistProductVarcharAttribute($attribute)
    {
        $this->getSubject()->persistProductVarcharAttribute($attribute);
    }

    /**
     * Persist's the passed product integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistProductIntAttribute($attribute)
    {
        $this->getSubject()->persistProductIntAttribute($attribute);
    }

    /**
     * Persist's the passed product decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistProductDecimalAttribute($attribute)
    {
        $this->getSubject()->persistProductDecimalAttribute($attribute);
    }

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistProductDatetimeAttribute($attribute)
    {
        $this->getSubject()->persistProductDatetimeAttribute($attribute);
    }

    /**
     * Persist's the passed product text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    protected function persistProductTextAttribute($attribute)
    {
        $this->getSubject()->persistProductTextAttribute($attribute);
    }
}
