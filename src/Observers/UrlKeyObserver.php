<?php

/**
 * TechDivision\Import\Product\Observers\UrlKeyObserver
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
use TechDivision\Import\Utils\Filter\UrlKeyFilterTrait;

/**
 * Observer that extracts the URL key from the product name and adds a two new columns
 * with the their values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class UrlKeyObserver extends AbstractProductImportObserver
{

    /**
     * The trait that provides string => URL key conversion functionality.
     *
     * @var \TechDivision\Import\Utils\Filter\UrlKeyFilterTrait
     */
    use UrlKeyFilterTrait;

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // query whether or not the URL key column has a value
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            return;
        }

        // query whether or not a product name is available
        if ($this->hasValue(ColumnKeys::NAME)) {
            $this->setValue(ColumnKeys::URL_KEY, $urlKey = $this->convertNameToUrlKey($this->getValue(ColumnKeys::NAME)));
            return;
        }

        // throw an exception, that the URL key can not be initialized
        $this->getSystemLogger()->debug(
            sprintf(
                'Can\'t initialize the URL key in CSV file %s on line %d',
                $this->getFilename(),
                $this->getLineNumber()
            )
        );
    }
}
