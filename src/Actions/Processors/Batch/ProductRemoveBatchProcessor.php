<?php

/**
 * TechDivision\Import\Product\Actions\Processors\Batch\ProductRemoveBatchProcessor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Product\Actions\Processors\Batch;

use TechDivision\Import\Product\Utils\SqlStatements;
use TechDivision\Import\Actions\Processors\Batch\AbstractRemoveBatchProcessor;

/**
 * The product remove batch processor implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ProductRemoveBatchProcessor extends AbstractRemoveBatchProcessor
{

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Product\Actions\Processors\Batch\AbstractRemoveBatchProcessor::getNumberOfPlaceholders()
     */
    protected function getNumberOfPlaceholders()
    {
        return 1;
    }

    /**
     * {@inheritDoc}
     * @see \TechDivision\Import\Product\Actions\Processors\Batch\AbstractRemoveBatchProcessor::getStatement()
     */
    protected function getStatement()
    {
        $utilityClassName = $this->getUtilityClassName();
        return $utilityClassName::REMOVE_PRODUCT;
    }
}
