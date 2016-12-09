<?php

/**
 * TechDivision\Import\Product\Callbacks\SelectCallback
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

namespace TechDivision\Import\Product\Callbacks;

use TechDivision\Import\Product\Utils\MemberNames;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class SelectCallback extends AbstractProductImportCallback
{

    /**
     * Will be invoked by a observer it has been registered for.
     *
     * @param mixed $value The value to handle
     *
     * @return mixed The modified value
     * @see \TechDivision\Import\Product\Callbacks\ProductImportCallbackInterface::handle()
     */
    public function handle($value)
    {

        // try to load the attribute option value
        $eavAttributeOptionValue = $this->getEavAttributeOptionValueByOptionValueAndStoreId($value, $this->getRowStoreId());

        // return the option ID
        return $eavAttributeOptionValue[MemberNames::OPTION_ID];
    }

    /**
     * Return's the store ID of the actual row.
     *
     * @return integer The ID of the actual store
     * @throws \Exception Is thrown, if the store with the actual code is not available
     */
    public function getRowStoreId()
    {
        return $this->getSubject()->getRowStoreId();
    }

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {
        return $this->getSubject()->getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);
    }
}
