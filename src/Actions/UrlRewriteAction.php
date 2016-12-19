<?php

/**
 * TechDivision\Import\Product\Actions\UrlRewriteAction
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
 * An action implementation that provides CRUD functionality for URL rewrites.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class UrlRewriteAction extends AbstractAction
{

    /**
     * Persist's the passed row.
     *
     * @param array       $row  The row to persist
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return string The last inserted ID
     */
    public function persist($row, $name = null)
    {
        return $this->getPersistProcessor()->execute($row, $name);
    }

    /**
     * Remove's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to remove
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function remove($row, $name = null)
    {
        return $this->getRemoveProcessor()->execute($row, $name);
    }
}
