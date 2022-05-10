<?php

/**
 * TechDivision\Import\Product\Observers\CategoryProductObserver
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

use TechDivision\Import\Dbal\Utils\EntityStatus;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\EntityTypeCodes;
use TechDivision\Import\Product\Utils\ConfigurationKeys;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Observers\AttributeLoaderInterface;
use TechDivision\Import\Observers\DynamicAttributeObserverInterface;
use TechDivision\Import\Observers\EntityMergers\EntityMergerInterface;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * Observer that creates/updates the category product relations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class CategoryProductObserver extends AbstractProductImportObserver implements DynamicAttributeObserverInterface
{

    /**
     * The actual category path that has to be processed.
     *
     * @var string
     */
    protected $path;

    /**
     * The position of the actual category.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * The attribute loader instance.
     *
     * @var \TechDivision\Import\Observers\AttributeLoaderInterface
     */
    protected $attributeLoader;

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * The entity merger instance.
     *
     * @var \TechDivision\Import\Observers\EntityMergers\EntityMergerInterface
     */
    protected $entityMerger;

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor The product bunch processor instance
     * @param \TechDivision\Import\Observers\AttributeLoaderInterface|null         $attributeLoader       The attribute loader instance
     * @param \TechDivision\Import\Observers\EntityMergers\EntityMergerInterface   $entityMerger          The entity merger instance
     * @param \TechDivision\Import\Observers\StateDetectorInterface|null           $stateDetector         The state detector instance to use
     */
    public function __construct(
        ProductBunchProcessorInterface $productBunchProcessor,
        AttributeLoaderInterface $attributeLoader,
        EntityMergerInterface $entityMerger,
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
        if ($this->hasBeenProcessed($sku = $this->getValue(ColumnKeys::SKU))) {
            return;
        }

        // initialize the arrays for the new category product relations
        $categoryProducts = array();

        // explode the categories as well as their positions, if available
        $categories = $this->getValue(ColumnKeys::CATEGORIES, array(), array($this, 'explode'));
        $categoryPositions = $this->getValue(ColumnKeys::CATEGORIES_POSITION, array(), array($this, 'explode'));

        // load the category product relations found in the CSV file
        foreach ($categories as $key => $this->path) {
            // initialize the category position with the default value
            $this->position = 0;

            // query whether or not a position for the category has been specified
            if (isset($categoryPositions[$key])) {
                // set the position, if available
                $this->position = (int) $categoryPositions[$key];
            }

            // prepare the product category relation attributes
            if ($attr = $this->prepareDynamicAttributes()) {
                // initialize and persist the category product relation relation and add the
                // category ID to the list => necessary to create the URL rewrites later!
                if ($this->hasChanges($categoryProduct = $this->initializeCategoryProduct($attr))) {
                    $this->persistCategoryProduct($categoryProduct);
                }

                // tempoary persist the category ID
                $categoryProducts[] = $categoryProduct[MemberNames::CATEGORY_ID];
            }
        }

        // query whether or not we've to clean up existing category product relations
        if ($this->hasHeader(ColumnKeys::CATEGORIES) && $this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::CLEAN_UP_CATEGORY_PRODUCT_RELATIONS) &&
            $this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::CLEAN_UP_CATEGORY_PRODUCT_RELATIONS)
        ) {
            // load the existing category product relations for the product with the given SKU
            $existingCategoryProducts = $this->getProductBunchProcessor()->getCategoryProductsBySku($sku);

            // initialize the counter for the deleted category product relations
            $counter = 0;

            // clean up the existing category product relations
            foreach ($existingCategoryProducts as $categoryId => $categoryProduct) {
                // query whether or not the category product relation exists in the CSV file
                if (!in_array($categoryId, $categoryProducts)) {
                    // if not, delete it from the database
                    $this->getProductBunchProcessor()
                         ->deleteCategoryProduct(array(MemberNames::ENTITY_ID => $categoryProduct[MemberNames::ENTITY_ID]));

                    // raise the counter for the deleted category product relations
                    $counter++;
                }
            }

            // log a message (if any category product relations have been cleaned-up)
            if ($counter > 0) {
                $this->getSubject()
                     ->getSystemLogger()
                     ->warning(
                         $this->getSubject()->appendExceptionSuffix(
                             sprintf(
                                 'Cleaned-up %d category product relation(s) for product with SKU "%s"',
                                 $counter,
                                 $sku
                             )
                         )
                     );
            }
        }
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
     * Appends the dynamic attributes to the static ones and returns them.
     *
     * @return array The array with all available attributes
     */
    protected function prepareDynamicAttributes()
    {
        return array_merge($this->prepareAttributes(), $this->attributeLoader ? $this->attributeLoader->load($this, array()) : array());
    }

    /**
     * Prepare the attributes of the entity that has to be persisted.
     *
     * @return array The prepared attributes
     */
    protected function prepareAttributes()
    {

        // load the subject
        $subject = $this->getSubject();

        // load the ID of the product that has been created recently
        $lastEntityId = $subject->getLastEntityId();

        try {
            // load the category for the found path
            $category = $this->getCategoryByPath($this->path);
            // return the prepared category product relation
            return $this->initializeEntity(
                $this->loadRawEntity(
                    array(
                        MemberNames::CATEGORY_ID => $category[MemberNames::ENTITY_ID],
                        MemberNames::PRODUCT_ID  => $lastEntityId,
                        MemberNames::POSITION    => $this->position
                    )
                )
            );
        } catch (\Exception $e) {
            // query whether or not debug mode has been enabled
            if (!$subject->isStrictMode()) {
                $message = sprintf('Category error on SKU "%s"! Detail: %s', $this->getValue(ColumnKeys::SKU), $e->getMessage());
                $subject->getSystemLogger()->warning($subject->appendExceptionSuffix($message));
                $this->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getFilename()) => array(
                                $this->getLineNumber() => array(
                                    ColumnKeys::CATEGORIES =>  $message
                                )
                            )
                        )
                    )
                );
                return [];
            } else {
                throw $e;
            }
        }
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
        return $this->getProductBunchProcessor()->loadRawEntity(EntityTypeCodes::CATALOG_CATEGORY_PRODUCT, $data);
    }

    /**
     * Initialize the category product with the passed attributes and returns an instance.
     *
     * @param array $attr The category product attributes
     *
     * @return array The initialized category product
     */
    protected function initializeCategoryProduct(array $attr)
    {
        return $attr;
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path The path of the category to return
     *
     * @return array The category
     */
    protected function getCategoryByPath($path)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }

    /**
     * Persist's the passed category product relation.
     *
     * @param array $categoryProduct The category product relation to persist
     *
     * @return string The ID of the persisted entity
     */
    protected function persistCategoryProduct($categoryProduct)
    {
        return $this->getProductBunchProcessor()->persistCategoryProduct($categoryProduct);
    }
}
