<?php

/**
 * TechDivision\Import\Product\Actions\Processors\ProductTextDeleteProcessor
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

use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Actions\Processors\AbstractDeleteProcessor;

/**
 * The product integer attribute delete processor implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductTextDeleteProcessor extends AbstractDeleteProcessor
{

    /**
     * Return's the array with the SQL statements that has to be prepared.
     *
     * @return array The SQL statements to be prepared
     * @see \TechDivision\Import\Actions\Processors\AbstractBaseProcessor::getStatements()
     */
    protected function getStatements()
    {

        // return the array with the SQL statements that has to be prepared
        return array(
            SqlStatementKeys::DELETE_PRODUCT_TEXT => $this->loadStatement(SqlStatementKeys::DELETE_PRODUCT_TEXT)
        );
    }
}
