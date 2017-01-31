<?php

/**
 * TechDivision\Import\Product\Subjects\BunchSubject
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

namespace TechDivision\Import\Product\Subjects;

use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessor;
use TechDivision\Import\Product\Utils\VisibilityKeys;

/**
 * The subject implementation that handles the business logic to persist products.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class BunchSubject extends AbstractProductSubject
{

    /**
     * The mapping for the supported backend types (for the product entity) => persist methods.
     *
     * @var array
     */
    protected $backendTypes = array(
        'datetime' => array('persistProductDatetimeAttribute', 'loadProductDatetimeAttribute'),
        'decimal'  => array('persistProductDecimalAttribute', 'loadProductDecimalAttribute'),
        'int'      => array('persistProductIntAttribute', 'loadProductIntAttribute'),
        'text'     => array('persistProductTextAttribute', 'loadProductTextAttribute'),
        'varchar'  => array('persistProductVarcharAttribute', 'loadProductVarcharAttribute')
    );

    /**
     * Mappings for attribute code => CSV column header.
     *
     * @var array
     */
    protected $headerMappings = array(
        'status' => 'product_online',
        'tax_class_id' => 'tax_class_name',
        'price_type'  => 'bundle_price_type',
        'sku_type' => 'bundle_sku_type',
        'price_view' => 'bundle_price_view',
        'weight_type' => 'bundle_weight_type',
        'image' => 'base_image',
        'image_label' => 'base_image_label',
        'thumbnail' => 'thumbnail_image',
        'thumbnail_label' => 'thumbnail_image_label',
        'shipment_type' => 'bundle_shipment_type'
    );

    /**
     * Mappings for the table column => CSV column header.
     *
     * @var array
     */
    protected $headerStockMappings = array(
        'qty'                         => array('qty', 'float'),
        'min_qty'                     => array('out_of_stock_qty', 'float'),
        'use_config_min_qty'          => array('use_config_min_qty', 'int'),
        'is_qty_decimal'              => array('is_qty_decimal', 'int'),
        'backorders'                  => array('allow_backorders', 'int'),
        'use_config_backorders'       => array('use_config_backorders', 'int'),
        'min_sale_qty'                => array('min_cart_qty', 'float'),
        'use_config_min_sale_qty'     => array('use_config_min_sale_qty', 'int'),
        'max_sale_qty'                => array('max_cart_qty', 'float'),
        'use_config_max_sale_qty'     => array('use_config_max_sale_qty', 'int'),
        'is_in_stock'                 => array('is_in_stock', 'int'),
        'notify_stock_qty'            => array('notify_on_stock_below', 'float'),
        'use_config_notify_stock_qty' => array('use_config_notify_stock_qty', 'int'),
        'manage_stock'                => array('manage_stock', 'int'),
        'use_config_manage_stock'     => array('use_config_manage_stock', 'int'),
        'use_config_qty_increments'   => array('use_config_qty_increments', 'int'),
        'qty_increments'              => array('qty_increments', 'float'),
        'use_config_enable_qty_inc'   => array('use_config_enable_qty_inc', 'int'),
        'enable_qty_increments'       => array('enable_qty_increments', 'int'),
        'is_decimal_divided'          => array('is_decimal_divided', 'int'),
    );

    /**
     * The array with the available visibility keys.
     *
     * @var array
     */
    protected $availableVisibilities = array(
        'Not Visible Individually' => VisibilityKeys::VISIBILITY_NOT_VISIBLE,
        'Catalog'                  => VisibilityKeys::VISIBILITY_IN_CATALOG,
        'Search'                   => VisibilityKeys::VISIBILITY_IN_SEARCH,
        'Catalog, Search'          => VisibilityKeys::VISIBILITY_BOTH
    );

    /**
     * The attribute set of the product that has to be created.
     *
     * @var array
     */
    protected $attributeSet = array();

    /**
     * The array containing the data for product type configuration (configurables, bundles, etc).
     *
     * @var array
     */
    protected $artefacs = array();

    /**
     * The category IDs the product is related with.
     *
     * @var array
     */
    protected $productCategoryIds = array();

    /**
     * Set's the attribute set of the product that has to be created.
     *
     * @param array $attributeSet The attribute set
     *
     * @return void
     */
    public function setAttributeSet(array $attributeSet)
    {
        $this->attributeSet = $attributeSet;
    }

    /**
     * Return's the attribute set of the product that has to be created.
     *
     * @return array The attribute set
     */
    public function getAttributeSet()
    {
        return $this->attributeSet;
    }

    /**
     * Clean up the global data after importing the bunch.
     *
     * @return void
     */
    public function tearDown()
    {

        // invoke parent method
        parent::tearDown();

        // export the artefacts
        $this->exportArtefacts();
    }

    /**
     * Export's the artefacts to CSV files.
     *
     * @return void
     */
    protected function exportArtefacts()
    {

        // load the target directory and the actual timestamp
        $targetDir = $this->getTargetDir();
        $timestamp = date('Ymd-His');

        // iterate over the artefacts and export them
        foreach ($this->getArtefacts() as $artefactType => $artefacts) {
            // initialize the bunch and the exporter
            $bunch = array();
            $exporter = new Exporter($this->getExportConfig());

            // iterate over the artefact types artefacts
            foreach ($artefacts as $entityArtefacts) {
                // set the bunch header and append the artefact data
                if (sizeof($bunch) === 0) {
                    $first = reset($entityArtefacts);
                    $second = reset($first);
                    $bunch[] = array_keys($second);
                }

                // export the artefacts
                foreach ($entityArtefacts as $entityArtefact) {
                    $bunch = array_merge($bunch, $entityArtefact);
                }
            }

            // export the artefact (bunch)
            $exporter->export(sprintf('%s/%s-%s_01.csv', $targetDir, $artefactType, $timestamp), $bunch);
        }
    }

    /**
     * Return's the target directory for the artefact export.
     *
     * @return string The target directory for the artefact export
     */
    protected function getTargetDir()
    {
        return $this->getNewSourceDir();
    }

    /**
     * Initialize and return the exporter configuration.
     *
     * @return \Goodby\CSV\Export\Standard\ExporterConfig The exporter configuration
     */
    protected function getExportConfig()
    {

        // initialize the lexer configuration
        $config = new ExporterConfig();

        // query whether or not a delimiter character has been configured
        if ($delimiter = $this->getConfiguration()->getDelimiter()) {
            $config->setDelimiter($delimiter);
        }

        // query whether or not a custom escape character has been configured
        if ($escape = $this->getConfiguration()->getEscape()) {
            $config->setEscape($escape);
        }

        // query whether or not a custom enclosure character has been configured
        if ($enclosure = $this->getConfiguration()->getEnclosure()) {
            $config->setEnclosure($enclosure);
        }

        // query whether or not a custom source charset has been configured
        if ($fromCharset = $this->getConfiguration()->getFromCharset()) {
            $config->setFromCharset($fromCharset);
        }

        // query whether or not a custom target charset has been configured
        if ($toCharset = $this->getConfiguration()->getToCharset()) {
            $config->setToCharset($toCharset);
        }

        // query whether or not a custom file mode has been configured
        if ($fileMode = $this->getConfiguration()->getFileMode()) {
            $config->setFileMode($fileMode);
        }

        // return the lexer configuratio
        return $config;
    }

    /**
     * Cast's the passed value based on the backend type information.
     *
     * @param string $backendType The backend type to cast to
     * @param mixed  $value       The value to be casted
     *
     * @return mixed The casted value
     */
    public function castValueByBackendType($backendType, $value)
    {

        // cast the value to a valid timestamp
        if ($backendType === 'datetime') {
            return \DateTime::createFromFormat($this->getSourceDateFormat(), $value)->format('Y-m-d H:i:s');
        }

        // cast the value to a float value
        if ($backendType === 'float') {
            return (float) $value;
        }

        // cast the value to an integer
        if ($backendType === 'int') {
            return (int) $value;
        }

        // we don't need to cast strings
        return $value;
    }

    /**
     * Return's the mappings for the table column => CSV column header.
     *
     * @return array The header stock mappings
     */
    public function getHeaderStockMappings()
    {
        return $this->headerStockMappings;
    }

    /**
     * Return's mapping for the supported backend types (for the product entity) => persist methods.
     *
     * @return array The mapping for the supported backend types
     */
    public function getBackendTypes()
    {
        return $this->backendTypes;
    }

    /**
     * Return's the artefacts for post-processing.
     *
     * @return array The artefacts
     */
    public function getArtefacts()
    {
        return $this->artefacs;
    }

    /**
     * Return's the visibility key for the passed visibility string.
     *
     * @param string $visibility The visibility string to return the key for
     *
     * @return integer The requested visibility key
     * @throws \Exception Is thrown, if the requested visibility is not available
     */
    public function getVisibilityIdByValue($visibility)
    {

        // query whether or not, the requested visibility is available
        if (isset($this->availableVisibilities[$visibility])) {
            return $this->availableVisibilities[$visibility];
        }

        // throw an exception, if not
        throw new \Exception(
            sprintf(
                'Found invalid visibility %s in file %s on line %d',
                $visibility,
                $this->getFilename(),
                $this->getLineNumber()
            )
        );
    }

    /**
     * Map the passed attribute code, if a header mapping exists and return the
     * mapped mapping.
     *
     * @param string $attributeCode The attribute code to map
     *
     * @return string The mapped attribute code, or the original one
     */
    public function mapAttributeCodeByHeaderMapping($attributeCode)
    {

        // query weather or not we've a mapping, if yes, map the attribute code
        if (isset($this->headerMappings[$attributeCode])) {
            $attributeCode = $this->headerMappings[$attributeCode];
        }

        // return the (mapped) attribute code
        return $attributeCode;
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param string $type      The artefact type, e. g. configurable
     * @param array  $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Product\Subjects\BunchSubject::getLastEntityId()
     */
    public function addArtefacts($type, array $artefacts)
    {

        // query whether or not, any artefacts are available
        if (sizeof($artefacts) === 0) {
            return;
        }

        // append the artefacts to the stack
        $this->artefacs[$type][$this->getLastEntityId()][] = $artefacts;
    }

    /**
     * Add the passed category ID to the product's category list.
     *
     * @param integer $categoryId The category ID to add
     *
     * @return void
     */
    public function addProductCategoryId($categoryId)
    {
        $this->productCategoryIds[$this->getLastEntityId()][$categoryId] = $this->getLastEntityId();
    }

    /**
     * Return's the list with category IDs the product is related with.
     *
     * @return array The product's category IDs
     */
    public function getProductCategoryIds()
    {

        // initialize the array with the product's category IDs
        $categoryIds = array();

        // query whether or not category IDs are available for the actual product entity
        if (isset($this->productCategoryIds[$lastEntityId = $this->getLastEntityId()])) {
            $categoryIds = $this->productCategoryIds[$lastEntityId];
        }

        // return the array with the product's category IDs
        return $categoryIds;
    }

    /**
     * Return's the attribute option value with the passed value and store ID.
     *
     * @param mixed   $value   The option value
     * @param integer $storeId The ID of the store
     *
     * @return array|boolean The attribute option value instance
     */
    public function getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId)
    {
        return $this->getProductProcessor()->getEavAttributeOptionValueByOptionValueAndStoreId($value, $storeId);
    }

    /**
     * Return's the URL rewrites for the passed URL entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to laod the rewrites for
     *
     * @return array The URL rewrites
     */
    public function getUrlRewritesByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->getProductProcessor()->getUrlRewritesByEntityTypeAndEntityId($entityType, $entityId);
    }

    /**
     * Load's and return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to load
     *
     * @return array The product
     */
    public function loadProduct($sku)
    {
        return $this->getProductProcessor()->loadProduct($sku);
    }

    /**
     * Load's and return's the product website relation with the passed product and website ID.
     *
     * @param string $productId The product ID of the relation
     * @param string $websiteId The website ID of the relation
     *
     * @return array The product website
     */
    public function loadProductWebsite($productId, $websiteId)
    {
        return $this->getProductProcessor()->loadProductWebsite($productId, $websiteId);
    }

    /**
     * Return's the category product relation with the passed category/product ID.
     *
     * @param integer $categoryId The category ID of the category product relation to return
     * @param integer $productId  The product ID of the category product relation to return
     *
     * @return array The category product relation
     */
    public function loadCategoryProduct($categoryId, $productId)
    {
        return $this->getProductProcessor()->loadCategoryProduct($categoryId, $productId);
    }

    /**
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock status to load
     * @param integer $websiteId The website ID of the stock status to load
     * @param integer $stockId   The stock ID of the stock status to load
     *
     * @return array The stock status
     */
    public function loadStockStatus($productId, $websiteId, $stockId)
    {
        return $this->getProductProcessor()->loadStockStatus($productId, $websiteId, $stockId);
    }

    /**
     * Load's and return's the stock status with the passed product/website/stock ID.
     *
     * @param integer $productId The product ID of the stock item to load
     * @param integer $websiteId The website ID of the stock item to load
     * @param integer $stockId   The stock ID of the stock item to load
     *
     * @return array The stock item
     */
    public function loadStockItem($productId, $websiteId, $stockId)
    {
        return $this->getProductProcessor()->loadStockItem($productId, $websiteId, $stockId);
    }

    /**
     * Load's and return's the datetime attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The datetime attribute
     */
    public function loadProductDatetimeAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getProductProcessor()->loadProductDatetimeAttribute($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the decimal attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The decimal attribute
     */
    public function loadProductDecimalAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getProductProcessor()->loadProductDecimalAttribute($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the integer attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The integer attribute
     */
    public function loadProductIntAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getProductProcessor()->loadProductIntAttribute($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the text attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The text attribute
     */
    public function loadProductTextAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getProductProcessor()->loadProductTextAttribute($entityId, $attributeId, $storeId);
    }

    /**
     * Load's and return's the varchar attribute with the passed entity/attribute/store ID.
     *
     * @param integer $entityId    The entity ID of the attribute
     * @param integer $attributeId The attribute ID of the attribute
     * @param integer $storeId     The store ID of the attribute
     *
     * @return array|null The varchar attribute
     */
    public function loadProductVarcharAttribute($entityId, $attributeId, $storeId)
    {
        return $this->getProductProcessor()->loadProductVarcharAttribute($entityId, $attributeId, $storeId);
    }

    /**
     * Persist's the passed product data and return's the ID.
     *
     * @param array $product The product data to persist
     *
     * @return string The ID of the persisted entity
     */
    public function persistProduct($product)
    {
        return $this->getProductProcessor()->persistProduct($product);
    }

    /**
     * Persist's the passed product varchar attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductVarcharAttribute($attribute)
    {
        $this->getProductProcessor()->persistProductVarcharAttribute($attribute);
    }

    /**
     * Persist's the passed product integer attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductIntAttribute($attribute)
    {
        $this->getProductProcessor()->persistProductIntAttribute($attribute);
    }

    /**
     * Persist's the passed product decimal attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductDecimalAttribute($attribute)
    {
        $this->getProductProcessor()->persistProductDecimalAttribute($attribute);
    }

    /**
     * Persist's the passed product datetime attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductDatetimeAttribute($attribute)
    {
        $this->getProductProcessor()->persistProductDatetimeAttribute($attribute);
    }

    /**
     * Persist's the passed product text attribute.
     *
     * @param array $attribute The attribute to persist
     *
     * @return void
     */
    public function persistProductTextAttribute($attribute)
    {
        $this->getProductProcessor()->persistProductTextAttribute($attribute);
    }

    /**
     * Persist's the passed product website data and return's the ID.
     *
     * @param array $productWebsite The product website data to persist
     *
     * @return void
     */
    public function persistProductWebsite($productWebsite)
    {
        $this->getProductProcessor()->persistProductWebsite($productWebsite);
    }

    /**
     * Persist's the passed category product relation.
     *
     * @param array $categoryProduct The category product relation to persist
     *
     * @return void
     */
    public function persistCategoryProduct($categoryProduct)
    {
        $this->getProductProcessor()->persistCategoryProduct($categoryProduct);
    }

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array $stockItem The stock item data to persist
     *
     * @return void
     */
    public function persistStockItem($stockItem)
    {
        $this->getProductProcessor()->persistStockItem($stockItem);
    }

    /**
     * Persist's the passed stock status data and return's the ID.
     *
     * @param array $stockStatus The stock status data to persist
     *
     * @return void
     */
    public function persistStockStatus($stockStatus)
    {
        $this->getProductProcessor()->persistStockStatus($stockStatus);
    }

    /**
     * Persist's the URL write with the passed data.
     *
     * @param array $row The URL rewrite to persist
     *
     * @return void
     */
    public function persistUrlRewrite($row)
    {
        $this->getProductProcessor()->persistUrlRewrite($row);
    }

    /**
     * Delete's the entity with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProduct($row, $name = null)
    {
        $this->getProductProcessor()->deleteProduct($row, $name);
    }

    /**
     * Delete's the URL rewrite(s) with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteUrlRewrite($row, $name = null)
    {
        $this->getProductProcessor()->deleteUrlRewrite($row, $name);
    }

    /**
     * Delete's the stock item(s) with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteStockItem($row, $name = null)
    {
        $this->getProductProcessor()->deleteStockItem($row, $name);
    }

    /**
     * Delete's the stock status with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteStockStatus($row, $name = null)
    {
        $this->getProductProcessor()->deleteStockStatus($row, $name);
    }

    /**
     * Delete's the product website relations with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteProductWebsite($row, $name = null)
    {
        $this->getProductProcessor()->deleteProductWebsite($row, $name);
    }

    /**
     * Delete's the category product relations with the passed attributes.
     *
     * @param array       $row  The attributes of the entity to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    public function deleteCategoryProduct($row, $name = null)
    {
        $this->getProductProcessor()->deleteCategoryProduct($row, $name);
    }
}
