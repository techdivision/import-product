<?php

/**
 * TechDivision\Import\Product\Actions\ProductAction
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

namespace TechDivision\Import\Product\Actions;

use TechDivision\Import\Actions\AbstractAction;

/**
 * An action implementation that provides CRUD functionality for products.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductAction extends AbstractAction
{

    /**
     * Creates's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to create
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The last inserted ID
     */
    public function create($row, $name = null)
    {
        return $this->getCreateProcessor()->execute($row, $name);
    }

    /**
     * Update's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to update
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The ID of the updated product
     */
    public function update($row, $name = null)
    {
        return $this->getUpdateProcessor()->execute($row, $name);
    }
}
