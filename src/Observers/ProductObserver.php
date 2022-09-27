<?php

/**
 * TechDivision\Import\Product\Observers\ProductObserver
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

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;

/**
 * Observer that create's the product itself.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductObserver extends AbstractProductImportObserver
{

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor The product bunch processor instance
     * @param \TechDivision\Import\Observers\StateDetectorInterface|null           $stateDetector         The state detector instance to use
     */
    public function __construct(ProductBunchProcessorInterface $productBunchProcessor, StateDetectorInterface $stateDetector = null)
    {

        // initialize the bunch processor instance
        $this->productBunchProcessor = $productBunchProcessor;

        // pass the state detector to the parent method
        parent::__construct($stateDetector);
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
     * @return void
     */
    protected function process()
    {

        // query whether or not, we've found a new SKU => means we've found a new product
        if ($this->hasBeenProcessed($this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // prepare the product and query whether or not it has to be persisted or the row has to be skipped
        if ($this->hasChanges($product = $this->initializeProduct($this->prepareAttributes()))) {
            $this->persistProduct($product);
        }
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // prepare the date format for the created at/updated at dates
        $createdAt = $this->getValue(ColumnKeys::CREATED_AT, date('Y-m-d H:i:s'), array($this, 'formatDate'));
        $updatedAt = $this->getValue(ColumnKeys::UPDATED_AT, date('Y-m-d H:i:s'), array($this, 'formatDate'));

        // initialize the product values
        $sku = $this->getValue(ColumnKeys::SKU);
        $productType = $this->getValue(ColumnKeys::PRODUCT_TYPE);

        // load the product's attribute set ID
        $attributeSet = $this->getAttributeSet();
        $attributeSetId = $attributeSet[MemberNames::ATTRIBUTE_SET_ID];

        // return the prepared product
        return $this->initializeEntity(
            array(
                MemberNames::SKU              => $sku,
                MemberNames::CREATED_AT       => $createdAt,
                MemberNames::UPDATED_AT       => $updatedAt,
                MemberNames::HAS_OPTIONS      => 0,
                MemberNames::REQUIRED_OPTIONS => 0,
                MemberNames::TYPE_ID          => $productType,
                MemberNames::ATTRIBUTE_SET_ID => $attributeSetId
            )
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
    protected function mergeEntity(array $entity, array $attr, $changeSetName = null)
    {

        // temporary persist the entity ID
        $this->setLastEntityId($entity[MemberNames::ENTITY_ID]);

        // merge and return the entity
        return parent::mergeEntity($entity, $attr, $changeSetName);
    }

    /**
     * Initialize the product with the passed attributes and returns an instance.
     *
     * @param array $attr The product attributes
     *
     * @return array The initialized product
     */
    protected function initializeProduct(array $attr)
    {
        // load the product with the passed SKU and merge it with the attributes
        if ($entity = $this->loadProduct($attr[MemberNames::SKU])) {
            // wanna update and have no required type_id? use origin
            if (!isset($attr[MemberNames::TYPE_ID]) || empty($attr[MemberNames::TYPE_ID])) {
                unset($attr[MemberNames::TYPE_ID]);
            }
            // wanna update and have no required attribute_set_id? use origin
            if (!isset($attr[MemberNames::ATTRIBUTE_SET_ID]) || empty($attr[MemberNames::ATTRIBUTE_SET_ID])) {
                unset($attr[MemberNames::ATTRIBUTE_SET_ID]);
            }
            // remove the created at date from the attributes, when we update the entity
            unset($attr[MemberNames::CREATED_AT]);
            // Remove has_options and required_options flag from the attributes, when we update the entity
            unset($attr[MemberNames::HAS_OPTIONS]);
            unset($attr[MemberNames::REQUIRED_OPTIONS]);
            // merge the entity with the passed attributes
            return $this->mergeEntity($entity, $attr);
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Load's and return's the product with the passed SKU.
     *
     * @param string $sku The SKU of the product to load
     *
     * @return array The product
     */
    protected function loadProduct($sku)
    {
        return $this->getProductBunchProcessor()->loadProduct($sku);
    }

    /**
     * Persist's the passed product data.
     *
     * @param array $product The product data to persist
     *
     * @return void
     */
    protected function persistProduct($product)
    {
        $this->setLastEntityId($this->getProductBunchProcessor()->persistProduct($product));
    }

    /**
     * Return's the attribute set of the product that has to be created.
     *
     * @return array The attribute set
     */
    protected function getAttributeSet()
    {
        return $this->getSubject()->getAttributeSet();
    }

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    protected function setLastEntityId($lastEntityId)
    {
        $this->getSubject()->setLastEntityId($lastEntityId);
    }
}
