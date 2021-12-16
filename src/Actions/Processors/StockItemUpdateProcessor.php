<?php

/**
 * TechDivision\Import\Product\Actions\Processors\StockItemUpdateProcessor
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Actions\Processors;

use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Collection\Actions\Processors\AbstractBaseProcessor;

/**
 * The stock item update processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class StockItemUpdateProcessor extends AbstractBaseProcessor
{

    /**
     * Implements the CRUD functionality the processor is responsible for,
     * can be one of CREATE, READ, UPDATE or DELETE a entity.
     *
     * @param array       $row                  The row to persist
     * @param string|null $name                 The name of the prepared statement that has to be executed
     * @param string|null $primaryKeyMemberName The primary key member name of the entity to use
     *
     * @return string The ID of the updated entity
     */
    public function execute($row, $name = null, $primaryKeyMemberName = null)
    {

        // load the field names
        $keys = array_keys($row);

        // create a unique name for the prepared statement
        $name = sprintf('%s-%s', $name, md5(implode('-', $keys)));

        // query whether or not the statement has been prepared
        if (!$this->hasPreparedStatement($name)) {
            // initialize the array for the primary key fields
            $pks = array();
            // load the last value as PK from the array with the keys
            $pks[] = $keys[array_search(MemberNames::ITEM_ID, $keys, true)];

            // remove the entity status and the primary key from the keys
            unset($keys[array_search(MemberNames::ITEM_ID, $keys, true)]);
            unset($keys[array_search(EntityStatus::MEMBER_NAME, $keys, true)]);

            // prepare the SET part of the SQL statement
            array_walk($keys, function (&$value, $key) {
                $value = sprintf('%s=:%s', $value, $value);
            });

            // prepare the SET part of the SQL statement
            array_walk($pks, function (&$value, $key) {
                $value = sprintf('%s=:%s', $value, $value);
            });

            // create the prepared UPDATE statement
            $statement = sprintf($this->loadStatement(SqlStatementKeys::UPDATE_STOCK_ITEM), implode(',', $keys), implode(',', $pks));

            // prepare the statement
            $this->addPreparedStatement($name, $this->getConnection()->prepare($statement));
        }

        // pass the call to the parent method
        return parent::execute($row, $name);
    }
}
