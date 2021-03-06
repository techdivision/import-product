<?php

/**
 * TechDivision\Import\Loaders\ProductValueLoader
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
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Loaders;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;

/**
 * Generic loader for product values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ProductValueLoader implements LoaderInterface
{

    /**
     * The column name to load the values for.
     *
     * @var string
     */
    protected $columnName;

    /**
     * The available values.
     *
     * @var array
     */
    protected $values = array();

    /**
     * The registry loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $registryLoader;

    /**
     * Construct that initializes the iterator with the product processor instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface                         $registryLoader   The registry loader instance
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productProcessor The product processor instance
     * @param string                                                               $columName        The column name to load the values for
     */
    public function __construct(LoaderInterface $registryLoader, ProductBunchProcessorInterface $productProcessor, $columName = MemberNames::SKU)
    {

        // initialize the column name and the registry loader
        $this->columnName = $columName;
        $this->registryLoader = $registryLoader;

        // initialize the array with the SKUs
        foreach ($productProcessor->loadProducts() as $product) {
            $this->values[] = $product[$this->columnName];
        }
    }

    /**
     * Loads and returns data.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        // load the already processed SKUs from the registry, merge
        // them with the ones from the DB and return them
        $collectedColumns = $this->getRegistryLoader()->load();

        // query whether or not values for the configured column name are available
        if (is_array($collectedColumns[$this->columnName])) {
            // if yes merge the values into the array with the values from the DB
            foreach ($collectedColumns[$this->columnName] as $value) {
                // query whether or not the value already exits
                if (in_array($value, $this->values)) {
                    continue;
                }

                // append the value in the array
                $this->values[] = $value;
            }
        }

        // return the values
        return $this->values;
    }

    /**
     * The registry loader instance.
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The loader instance
     */
    protected function getRegistryLoader()
    {
        return $this->registryLoader;
    }
}
