<?php

/**
 * TechDivision\Import\Product\Observers\GenericSkuEntityIdMappingObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * A generic oberserver implementation that provides functionality to add the SKU => entity ID
 * mapping for products that has not yet been processed or are NOT part of the actual CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class GenericSkuEntityIdMappingObserver extends AbstractProductImportObserver
{

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * The column name with the SKU to map the entity ID for.
     *
     * @var string
     */
    protected $skuColumnName;

    /**
     * Initialize the observer with the passed product bunch processor instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor The product bunch processor instance
     * @param string                                                               $skuColumnName         The column name with the SKU to map the entity ID for
     */
    public function __construct(
        ProductBunchProcessorInterface $productBunchProcessor,
        $skuColumnName = ColumnKeys::SKU
    ) {
        $this->productBunchProcessor = $productBunchProcessor;
        $this->skuColumnName = $skuColumnName;
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
     * Returns the column name with the SKU to map the entity ID for.
     *
     * @return string The column name
     */
    protected function getSkuColumnName()
    {
        return $this->skuColumnName;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     * @throws \Exception Is thrown, if the product with the SKU can not be loaded
     */
    protected function process()
    {

        // load the SKU from the column
        $sku = $this->getValue($this->getSkuColumnName());

        // query whether or not the product has already been processed
        if ($this->hasBeenProcessed($sku)) {
            return;
        }

        // try to load it and map the entity ID of the product with the passed SKU otherwise
        if ($product = $this->loadProduct($sku)) {
            $this->addSkuPkMapping($product);
        } else {
            // initialize the error message
            $message = sprintf('Can\'t load product with SKU "%s"', $sku);
            // load the subject
            $subject = $this->getSubject();
            // query whether or not strict mode has been enabled
            if (!$subject->isStrictMode()) {
                $subject->getSystemLogger()->warning($subject->appendExceptionSuffix($message));

                $this->getSubject()->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getSubject()->getFilename()) => array(
                                $this->getSubject()->getLineNumber() => array(
                                    $this->getSkuColumnName()  => $message
                                )
                            )
                        )
                    )
                );
            } else {
                throw new \Exception($message);
            }
        }
    }

    /**
     * Map the PK for the product with the passed SKU.
     *
     * @param array $product The product to add the SKU => entity ID mapping for
     *
     * @return void
     */
    protected function addSkuPkMapping(array $product)
    {
        $this->addSkuEntityIdMapping($product[MemberNames::SKU], $product[MemberNames::ENTITY_ID]);
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
}
