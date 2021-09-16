<?php

/**
 * TechDivision\Import\Product\Utils\CoreConfigDataKeys
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Utils;

/**
 * Utility class containing the keys Magento uses to persist values in the "core_config_data table".
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class CoreConfigDataKeys extends \TechDivision\Import\Utils\CoreConfigDataKeys
{

    /**
     * Name for the column 'catalog/seo/save_rewrites_history'.
     *
     * @var string
     */
    const CATALOG_SEO_SAVE_REWRITES_HISTORY = 'catalog/seo/save_rewrites_history';
}
