<?php

/**
 * TechDivision\Import\Product\Observers\AdditionalAttributeObserver
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
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;

/**
 * Observer that prepares the additional product attribues found in the CSV file
 * in the row 'additional_attributes'.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class AdditionalAttributeObserver extends AbstractProductImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not the row has additional attributes
        if ($additionalAttributes = $this->getValue(ColumnKeys::ADDITIONAL_ATTRIBUTES)) {
            // query if the additional attributes have a value, at least
            if (strstr($additionalAttributes, '=') === false) {
                return;
            }

            // explode the additional attributes
            $additionalAttributes = explode(',', $additionalAttributes);

            // iterate over the attributes and append them to the row
            foreach ($additionalAttributes as $additionalAttribute) {
                // explode attribute code/option value from the attribute
                list ($attributeCode, $optionValue) = explode('=', $additionalAttribute);

                // try to load the appropriate key for the value
                if (!$this->hasHeader($attributeCode)) {
                    $this->addHeader($attributeCode);
                }

                // append/replace the attribute value
                $this->setValue($attributeCode, $optionValue);
            }
        }
    }
}
