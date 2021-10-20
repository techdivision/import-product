<?php

/**
 * TechDivision\Import\Product\Observers\ProductMultiStoreObserver
 *
 * PHP version 7
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-bundle
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Converter\Observers\AbstractConverterObserver;
use TechDivision\Import\Product\Utils\ColumnKeys;

/**
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-bundle
 * @link      http://www.techdivision.com
 */
class ProductMultiStoreObserver extends AbstractConverterObserver
{
    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'product-import';

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {
        // initialize the array for the artefacts
        $artefacts = array();

        $storeCodeViews = $this->getValue(ColumnKeys::STORE_VIEW_CODE, array(), array($this, 'explode'));
        $storeCodeViews = array_merge([''], $storeCodeViews);
        $iterator = 0;

        foreach ($storeCodeViews as $storeCodeView) {
            foreach (array_keys($this->getSubject()->getHeaders()) as $columnName) {
                if ($columnName == ColumnKeys::STORE_VIEW_CODE) {
                    $artefacts[$iterator][$columnName] = trim($storeCodeView);
                    continue;
                }
                $artefacts[$iterator][$columnName] = $this->getValue($columnName);
            }
            $iterator++;
        }

        $this->addArtefacts($artefacts);
    }

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param array $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Product\Media\Subjects\MediaSubject::getLastEntityId()
     */
    protected function addArtefacts(array $artefacts)
    {
        $this->getSubject()->addArtefacts(ProductMultiStoreObserver::ARTEFACT_TYPE, $artefacts, false);
    }
}
