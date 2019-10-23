<?php

/**
 * TechDivision\Import\Product\Loaders\TaxClassLoader
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Loaders;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Loader for tax classes.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class TaxClassLoader implements LoaderInterface
{

    /**
     * The available tax classes.
     *
     * @var array
     */
    protected $taxClasses = array();

    /**
     * Initializes the loader with the import processor instance.
     *
     * @param \TechDivision\Import\Services\ImportProcessorInterface $importProcessor The import processor instance
     */
    public function __construct(ImportProcessorInterface $importProcessor)
    {
        $this->taxClasses = array_keys($importProcessor->getTaxClasses());
    }

    /**
     * Loads and returns data the custom validation data.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        return $this->taxClasses;
    }
}
