<?php

/**
 * TechDivision\Import\Product\Actions\Processors\StockStatusCreateProcessor
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
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Actions\Processors\AbstractCreateProcessor;

/**
 * The stock status create processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class StockStatusCreateProcessor extends AbstractCreateProcessor
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

        // load the field names
        $keys = array_keys($row);

        // create a unique name for the prepared statement
        $name = sprintf('%s-%s', $name, md5(implode('-', $keys)));

        // query whether or not the statement has been prepared
        if (!$this->hasPreparedStatement($name)) {
            // remove the entity status from the keys
            unset($keys[array_search(EntityStatus::MEMBER_NAME, $keys)]);

            // create the prepared UPDATE statement
            $statement = sprintf($this->loadStatement(SqlStatementKeys::CREATE_STOCK_STATUS), implode(',', $keys), implode(',:', $keys));

            // prepare the statement
            $this->addPreparedStatement($name, $this->getConnection()->prepare($statement));
        }

        // pass the call to the parent method
        return parent::execute($row, $name);
    }
}
