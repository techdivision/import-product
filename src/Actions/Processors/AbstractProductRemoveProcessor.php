<?php

/**
 * TechDivision\Import\Product\Actions\Processors\AbstractProductRemoveProcessor
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

namespace TechDivision\Import\Product\Actions\Processors;

use TechDivision\Import\Product\Utils\SqlStatements;
use TechDivision\Import\Actions\Processors\AbstractRemoveProcessor;

/**
 * The product category persist processor implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
abstract class AbstractProductRemoveProcessor extends AbstractRemoveProcessor
{

    /**
     * Return's the passed statement from the Magento specific
     * utility class.
     *
     * @return string The utility class name
     */
    protected function getUtilityClassName()
    {
        return SqlStatements::getUtilityClassName($this->getMagentoEdition(), $this->getMagentoVersion());
    }
}
