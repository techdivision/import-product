<?php

/**
 * TechDivision\Import\Product\Assemblers\ProductAttributeAssemblerInterface
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
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Assemblers;

/**
 * Interface for assembler implementation that provides functionality to assemble product attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
interface ProductAttributeAssemblerInterface
{

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    public function getProductAttributesByPrimaryKeyAndStoreId($pk, $storeId);
}
