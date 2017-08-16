<?php

/**
 * TechDivision\Import\Product\Observers\AbstractProductImportObserver
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

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Observers\AbstractObserver;

/**
 * Abstract category observer that handles the process to import product bunches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
abstract class AbstractProductImportObserver extends AbstractObserver implements ProductImportObserverInterface
{

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     * @see \TechDivision\Import\Observers\ObserverInterface::handle()
     */
    public function handle(SubjectInterface $subject)
    {

        // initialize the row
        $this->setSubject($subject);
        $this->setRow($subject->getRow());

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    abstract protected function process();

    /**
     * Return's the column name that contains the primary key.
     *
     * @return string the column name that contains the primary key
     */
    protected function getPrimaryKeyColumnName()
    {
        return ColumnKeys::SKU;
    }

    /**
     * Queries whether or not the SKU has already been processed.
     *
     * @param string $sku The SKU to check been processed
     *
     * @return boolean TRUE if the SKU has been processed, else FALSE
     */
    protected function hasBeenProcessed($sku)
    {
        return $this->getSubject()->hasBeenProcessed($sku);
    }

    /**
     * Queries whether or not the passed SKU and store view code has already been processed.
     *
     * @param string $sku           The SKU to check been processed
     * @param string $storeViewCode The store view code to check been processed
     *
     * @return boolean TRUE if the SKU and store view code has been processed, else FALSE
     */
    protected function storeViewHasBeenProcessed($sku, $storeViewCode)
    {
        return $this->getSubject()->storeViewHasBeenProcessed($sku, $storeViewCode);
    }

    /**
     * Add the passed SKU => entity ID mapping.
     *
     * @param string $sku The SKU
     *
     * @return void
     */
    protected function addSkuEntityIdMapping($sku)
    {
        $this->getSubject()->addSkuEntityIdMapping($sku);
    }

    /**
     * Add the passed SKU => store view code mapping.
     *
     * @param string $sku           The SKU
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    protected function addSkuStoreViewCodeMapping($sku, $storeViewCode)
    {
        $this->getSubject()->addSkuStoreViewCodeMapping($sku, $storeViewCode);
    }

    /**
     * Return's TRUE if the passed SKU is the actual one.
     *
     * @param string $sku The SKU to check
     *
     * @return boolean TRUE if the passed SKU is the actual one
     */
    protected function isLastSku($sku)
    {
        return $this->getSubject()->getLastSku() === $sku;
    }
}
