<?php

/**
 * TechDivision\Import\Converter\Callbacks\TaxClassValidatorCallback
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
        if (in_array($attributeValue, $validations)) {
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
}
