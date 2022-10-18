<?php

/**
 * TechDivision\Import\Product\Observers\AbstractProductRelationObserver
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

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Services\ProductRelationAwareProcessorInterface;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * Oberserver that provides abstract functionality for the product relation replace operation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
abstract class AbstractProductRelationObserver extends AbstractProductImportObserver
{

    /**
     * The product relation's parent ID.
     *
     * @var integer
     */
    protected $parentId;

    /**
     * The product relation's child ID.
     *
     * @var integer
     */
    protected $childId;

    /**
     * The product relation aware processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductRelationAwareProcessorInterface
     */
    protected $productRelationAwareProcessor;

    /**
     * Initialize the observer with the passed product relation aware processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductRelationAwareProcessorInterface $productRelationAwareProcessor The product relation aware processor instance
     */
    public function __construct(ProductRelationAwareProcessorInterface $productRelationAwareProcessor)
    {
        $this->productRelationAwareProcessor = $productRelationAwareProcessor;
    }

    /**
     * Returns the product relation aware processor instance.
     *
     * @return \TechDivision\Import\Product\Services\ProductRelationAwareProcessorInterface The product relation aware processor instance
     */
    protected function getProductRelationAwareProcessor()
    {
        return $this->productRelationAwareProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // load the parent/child SKUs
        $parentSku = $this->getValue($parentSkuColumnName = $this->getParentSkuColumnName());
        $childSku = $this->getValue($childSkuColumnName = $this->getChildSkuColumnName());

        // query whether or not the product relation has already been processed
        if ($this->hasBeenProcessedRelation($parentSku, $childSku)) {
            return;
        }

        try {
            // extract the parent + child ID from the row
            $this->parentId = $this->mapSku($parentSku);
            $this->childId = $this->mapChildSku($childSku);
            // prepare and persist the product relation
            if ($productRelation = $this->initializeProductRelation($this->prepareProductRelationAttributes())) {
                $this->persistProductRelation($productRelation);
            }

            // mark the product relation as processed
            $this->addProcessedRelation($parentSku, $childSku);
        } catch (\Exception $e) {
            // prepare a more detailed error message
            $message = $this->appendExceptionSuffix(
                sprintf(
                    'Product relation with SKUs %s => %s can\'t be created',
                    $parentSku,
                    $childSku
                )
            );

            // if we're NOT in debug mode, re-throw a more detailed exception
            $wrappedException = $this->wrapException(
                array($parentSkuColumnName, $childSkuColumnName),
                new \Exception($message, 0, $e)
            );

            // query whether or not, debug mode is enabled
            if (!$this->isStrictMode()) {
                // stop processing the row
                $this->skipRow();
                // log a warning and return immediately
                $this->getSystemLogger()->warning($wrappedException->getMessage());
                $this->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getFilename()) => array(
                                $this->getLineNumber() => array(
                                    $childSkuColumnName => $wrappedException->getMessage()
                                )
                            )
                        )
                    )
                );
                return;
            }

            // else, throw the exception
            throw $wrappedException;
        }
    }

    /**
     * Returns the column name with the parent SKU.
     *
     * @return string The column name with the parent SKU
     */
    abstract protected function getParentSkuColumnName();

    /**
     * Returns the column name with the child SKU.
     *
     * @return string The column name with the child SKU
     */
    abstract protected function getChildSkuColumnName();

    /**
     * Prepare the product relation attributes that has to be persisted.
     *
     * @return array The prepared product relation attributes
     */
    protected function prepareProductRelationAttributes()
    {

        // initialize and return the entity
        return $this->initializeEntity(
            array(
                MemberNames::PARENT_ID => $this->parentId,
                MemberNames::CHILD_ID  => $this->childId
            )
        );
    }

    /**
     * Initialize the product relation with the passed attributes and returns an instance.
     *
     * @param array $attr The product relation attributes
     *
     * @return array|null The initialized product relation, or null if the relation already exsist
     */
    protected function initializeProductRelation(array $attr)
    {
        return $attr;
    }

    /**
     * Return the entity ID for the passed SKU.
     *
     * @param string $sku The SKU to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    protected function mapSku($sku)
    {
        return $this->getSubject()->mapSkuToEntityId($sku);
    }

    /**
     * Return the entity ID for the passed child SKU.
     *
     * @param string $sku The SKU to return the entity ID for
     *
     * @return integer The mapped entity ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    protected function mapChildSku($sku)
    {
        return $this->getSubject()->mapSkuToEntityId($sku);
    }

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array $productRelation The product relation data to persist
     *
     * @return void
     */
    protected function persistProductRelation($productRelation)
    {
        $this->getProductRelationAwareProcessor()->persistProductRelation($productRelation);
    }
}
