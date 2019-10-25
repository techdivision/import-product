<?php

/**
 * TechDivision\Import\Product\Callbacks\LinkValidatorCallback
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

use TechDivision\Import\Callbacks\ArrayValidatorCallback;

/**
 * A callback implementation that validates the SKUs on the bound column.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class LinkValidatorCallback extends ArrayValidatorCallback
{

    /**
     * Will be invoked by the observer it has been registered for.
     *
     * @param string|null $attributeCode  The code of the attribute that has to be validated
     * @param string|null $attributeValue The attribute value to be validated
     *
     * @return mixed The modified value
     * @throws \InvalidArgumentException Is thrown, if the SKU is not already in the database
     */
    public function handle($attributeCode = null, $attributeValue = null)
    {

        // explode the SKUs from the link column
        if ($this->isNullable($skus = $this->getSubject()->explode($attributeValue))) {
            return;
        }

        // the validations for the attribute with the given code
        $validations = $this->getValidations();

        // iterate over the exploded SKUs and validate them
        foreach ($skus as $sku) {
            // if the passed SKU is in the array, return immediately
            if (in_array($sku, $validations)) {
                continue;
            }

            // throw an exception if the value is NOT in the array
            throw new \InvalidArgumentException(
                sprintf(
                    'Found not exisisting SKU "%s" in column "%s"',
                    $sku,
                    $attributeCode
                )
            );
        }
    }
}
