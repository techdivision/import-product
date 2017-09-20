<?php

/**
 * TechDivision\Import\Product\Actions\Processors\StockStatusUpdateProcessor
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

namespace TechDivision\Import\Product\Actions\Processors;

use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatements;
use TechDivision\Import\Actions\Processors\AbstractUpdateProcessor;

/**
 * The stock status update processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class StockStatusUpdateProcessor extends AbstractUpdateProcessor
{

    /**
     * Implements the CRUD functionality the processor is responsible for,
     * can be one of CREATE, READ, UPDATE or DELETE a entity.
     *
     * @param array       $row  The data to handle
     * @param string|null $name The name of the prepared statement to execute
     *
     * @return void
     */
    public function execute($row, $name = null)
    {

        // UPDATE cataloginventory_stock_status SET %s WHERE %s',

        // load the field names
        $keys = array_keys($row);

        // create a unique name for the prepared statement
        $name = sprintf('%s-%s', $name, md5(implode('-', $keys)));

        // query whether or not the statement has been prepared
        if (!$this->hasPreparedStatement($name)) {
            // initialize the array for the primary key fields
            $pks = array();
            // remove the last value as PK from the array with the keys
            $pks[] = $keys[array_search(MemberNames::PRODUCT_ID, $row, true)];
            $pks[] = $keys[array_search(MemberNames::WEBSITE_ID, $row, true)];
            $pks[] = $keys[array_search(MemberNames::STOCK_ID, $row, true)];

            // remove the entity status from the keys
            unset($keys[array_search(MemberNames::ATTRIBUTE_ID, $keys, true)]);
            unset($keys[array_search(EntityStatus::MEMBER_NAME, $keys, true)]);

            // prepare the SET part of the SQL statement
            array_walk($keys, function (&$value, $key) {
                $value = sprintf('%s=:%s', $value, $value);
            });

            // prepare the WHERE part of the SQL statement
            array_walk($pks, function(&$value, $key) {
                $value = sprintf('%s=:%s', $value, $value);
            });

            // create the prepared UPDATE statement
            $statement = sprintf($this->getUtilityClass()->find(SqlStatements::UPDATE_STOCK_STATUS), implode(',', $keys), implode(' AND ', $pks));

            // prepare the statement
            $this->addPreparedStatement($name, $this->getConnection()->prepare($statement));
        }

        // pass the call to the parent method
        return parent::execute($row, $name);
    }
}
