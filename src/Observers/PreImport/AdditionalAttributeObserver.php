<?php

/**
 * TechDivision\Import\Product\Observers\PreImport\AdditionalAttributeObserver
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

namespace TechDivision\Import\Product\Observers\PreImport;

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
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param array $row The row to handle
     *
     * @return array The modified row
     * @see \TechDivision\Import\Product\Observers\ImportObserverInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // query whether or not the row has additional attributes
        if ($additionalAttributes = $row[$headers[ColumnKeys::ADDITIONAL_ATTRIBUTES]]) {
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
                if (isset($headers[$attributeCode])) {
                    $newKey = $headers[$attributeCode];
                } else {
                    $headers[$attributeCode] = $newKey = sizeof($headers);
                }

                // append/replace the attribute value
                $row[$newKey] = $optionValue;
            }
        }

        // update the header information
        $this->setHeaders($headers);

        // return the prepared row
        return $row;
    }
}
