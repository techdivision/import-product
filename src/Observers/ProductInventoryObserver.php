<?php

/**
 * TechDivision\Import\Product\Observers\ProductInventoryObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Observers\AttributeLoaderInterface;
use TechDivision\Import\Observers\DynamicAttributeObserverInterface;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Observers\ObserverFactoryInterface;
use TechDivision\Import\Observers\StateDetectorAwareObserverInterface;
use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Observers\EntityMergers\EntityMergerInterface;
use TechDivision\Import\Product\Utils\EntityTypeCodes;

/**
 * Observer that creates/updates the product's inventory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductInventoryObserver extends AbstractProductImportObserver implements DynamicAttributeObserverInterface, StateDetectorAwareObserverInterface, ObserverFactoryInterface
{

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * The attribute loader instance.
     *
     * @var \TechDivision\Import\Observers\AttributeLoaderInterface
     */
    protected $attributeLoader;

    /**
     * The entity merger instance.
     *
     * @var \TechDivision\Import\Observers\EntityMergers\EntityMergerInterface
     */
    protected $entityMerger;

    /**
     * The array with the column mappings that has to be computed.
     *
     * @var array
     */
    protected $columns = array();

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface    $productBunchProcessor The product bunch processor instance
     * @param \TechDivision\Import\Observers\AttributeLoaderInterface                 $attributeLoader       The attribute loader instance
     * @param \TechDivision\Import\Observers\EntityMergers\EntityMergerInterface|null $entityMerger          The entity merger instance
     * @param \TechDivision\Import\Observers\StateDetectorInterface|null              $stateDetector         The state detector instance to use
     */
    public function __construct(
        ProductBunchProcessorInterface $productBunchProcessor,
        AttributeLoaderInterface $attributeLoader,
        EntityMergerInterface $entityMerger = null,
        StateDetectorInterface $stateDetector = null
    ) {

        // initialize the bunch processor and the attribute loader instance
        $this->productBunchProcessor = $productBunchProcessor;
        $this->attributeLoader = $attributeLoader;
        $this->entityMerger = $entityMerger;

        // pass the state detector to the parent method
        parent::__construct($stateDetector);
    }

    /**
     * Will be invoked by the observer visitor when a factory has been defined to create the observer instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The observer instance
     */
    public function createObserver(SubjectInterface $subject)
    {

        // load the header stock mappings from the subject
        $headerStockMappings = $subject->getHeaderStockMappings();

        // prepare the array with column name => type mapping
        foreach ($headerStockMappings as $columnName => $mappings) {
            // explode the mapping details
            list (, $type) = $mappings;
            // add the column name => type mapping
            $this->columns[$columnName] = $type;
        }

        // return the instance itself
        return $this;
    }

    /**
     * Returns an array of the columns with their types to detect state.
     *
     * @return array The array with the column names as key and their type as value
     */
    public function getColumns()
    {
        return array_intersect_key($this->columns, $this->getHeaders());
    }

    /**
     * Return's the product bunch processor instance.
     *
     * @return \TechDivision\Import\Product\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    protected function getProductBunchProcessor()
    {
        return $this->productBunchProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->hasBeenProcessed($this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // prepare, initialize and persist the stock status/item
        if ($this->hasChanges($entity = $this->initializeStockItem($this->prepareStockItemAttributes()))) {
            $this->persistStockItem($entity);
        }

        // prepare, initialize and persist the stock status/item status
        if ($this->hasChanges($entity = $this->initializeStockItemStatus($this->prepareStatusAttributes()))) {
            $this->persistStockItemStatus($entity);
        }
    }

    /**
     * Prepare the basic attributes of the stock status/item entity that has to be persisted.
     *
     * @return array The prepared stock status/item attributes
     */
    protected function prepareAttributes()
    {

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getSubject()->getLastEntityId();

        // initialize the stock status data
        $websiteId =  $this->getValue(ColumnKeys::WEBSITE_ID, 0);

        // return the prepared stock status
        return $this->initializeEntity(
            $this->loadRawEntity(
                array(
                    MemberNames::PRODUCT_ID   => $lastEntityId,
                    MemberNames::WEBSITE_ID   => $websiteId,
                    MemberNames::STOCK_ID     => 1
                )
            )
        );
    }

   /**
     * Prepare the basic attributes of the stock status/item entity that has to be persisted.
     *
     * @return array The prepared stock status/item attributes
     */
    protected function prepareStatusAttributes()
    {

        // load the ID of the product that has been created recently
        $lastEntityId = $this->getSubject()->getLastEntityId();

        // initialize the stock status data
        $websiteId =  $this->getValue(ColumnKeys::WEBSITE_ID, 0);
        $stockStatus =  $this->getValue(ColumnKeys::IS_IN_STOCK, 0);
        $quantity =  $this->getValue(ColumnKeys::QTY, 0);

        // return the prepared stock status
        return $this->initializeEntity(
            $this->loadRawStatusEntity(
                array(
                    MemberNames::PRODUCT_ID   => $lastEntityId,
                    MemberNames::WEBSITE_ID   => $websiteId,
                    MemberNames::STOCK_ID     => 1,
                    MemberNames::STOCK_STATUS => (int) $stockStatus,
                    MemberNames::QTY          => $quantity
                )
            ),
        );
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * passed status.
     *
     * @param array       $entity        The entity to merge the attributes into
     * @param array       $attr          The attributes to be merged
     * @param string|null $changeSetName The change set name to use
     *
     * @return array The merged entity
     * @todo https://github.com/techdivision/import/issues/179
     */
    protected function mergeEntity(array $entity, array $attr, $changeSetName = null)
    {
        return array_merge(
            $entity,
            $this->entityMerger ? $this->entityMerger->merge($this, $entity, $attr) : $attr,
            array(EntityStatus::MEMBER_NAME => $this->detectState($entity, $attr, $changeSetName))
        );
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * passed status.
     *
     * @param array       $entity        The entity to merge the attributes into
     * @param array       $attr          The attributes to be merged
     * @param string|null $changeSetName The change set name to use
     *
     * @return array The merged entity
     */
    protected function mergeEntityStatus(array $entity, array $attr, $changeSetName = null)
    {
        return parent::mergeEntity($entity, $attr, $changeSetName);
    }

    /**
     * Prepare the stock item attributes of the entity that has to be persisted.
     *
     * @return array The prepared stock status item
     */
    protected function prepareStockItemAttributes()
    {
        return array_merge($this->prepareAttributes(), $this->attributeLoader->load($this, $this->getHeaderStockMappings()));
    }

    /**
     * Load's and return's a raw customer entity without primary key but the mandatory members only and nulled values.
     *
     * @param array $data An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    protected function loadRawEntity(array $data = array())
    {
        return $this->getProductBunchProcessor()->loadRawEntity(EntityTypeCodes::CATALOGINVENTORY_STOCK_ITEM, $data);
    }

    /**
     * Load's and return's a raw customer entity without primary key but the mandatory members only and nulled values.
     *
     * @param array $data An array with data that will be used to initialize the raw entity with
     *
     * @return array The initialized entity
     */
    protected function loadRawStatusEntity(array $data = array())
    {
        return $this->getProductBunchProcessor()->loadRawEntity(EntityTypeCodes::CATALOGINVENTORY_STOCK_ITEM_STATUS, $data);
    }

    /**
     * Initialize the stock item with the passed attributes and returns an instance.
     *
     * @param array $attr The stock item attributes
     *
     * @return array The initialized stock item
     */
    protected function initializeStockItem(array $attr)
    {
        return $attr;
    }

    /**
     * Initialize the stock item status with the passed attributes and returns an instance.
     *
     * @param array $attr The stock item attributes
     *
     * @return array The initialized stock item
     */
    protected function initializeStockItemStatus(array $attr)
    {
        return $attr;
    }

    /**
     * Return's the appings for the table column => CSV column header.
     *
     * @return array The header stock mappings
     */
    protected function getHeaderStockMappings()
    {
        return $this->getSubject()->getHeaderStockMappings();
    }

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array $stockItem The stock item data to persist
     *
     * @return void
     */
    protected function persistStockItem($stockItem)
    {
        $this->getProductBunchProcessor()->persistStockItem($stockItem);
    }

    /**
     * Persist's the passed stock item data and return's the ID.
     *
     * @param array $stockItemStatus The stock item data to persist
     *
     * @return void
     */
    protected function persistStockItemStatus($stockItemStatus)
    {
        $this->getProductBunchProcessor()->persistStockItemStatus($stockItemStatus);
    }
}
