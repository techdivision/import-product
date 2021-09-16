<?php

/**
 * TechDivision\Import\Converter\Callbacks\TaxClassValidatorCallback
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Callbacks;

use TechDivision\Import\Callbacks\AbstractValidatorCallback;

/**
 * Tax class validator callback implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class TaxClassValidatorCallback extends AbstractValidatorCallback
{

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param string|null $attributeCode  The code of the attribute that has to be validated
     * @param string|null $attributeValue The attribute value to be validated
     *
     * @return mixed The modified value
     */
    public function handle($attributeCode = null, $attributeValue = null)
    {

        // the validations for the attribute with the given code
        $validations = $this->getValidations();

        // if the passed value is in the array, return immediately
        if (in_array($attributeValue, $validations) || $this->isNullable($attributeValue)) {
            return;
        }

        // throw an exception if the value is NOT in the array
        throw new \InvalidArgumentException(
            sprintf(
                'Found invalid value "%s" for column "%s" (must be one of: "%s")',
                $attributeValue,
                $attributeCode,
                implode(', ', $validations)
            )
        );
    }

    /**
     * Query whether or not the passed value IS empty and empty values are allowed.
     *
     * @param string $attributeValue The attribute value to query for
     *
     * @return boolean TRUE if empty values are allowed and the passed value IS empty
     */
    protected function isNullable($attributeValue)
    {

        // query whether or not the value is empty
        $isNull = $attributeValue === '' || $attributeValue === null;

        // query whether or not we're on the main row
        return $this->isMainRow() && $isNull ? false : true;
    }
}
