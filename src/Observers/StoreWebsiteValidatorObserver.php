<?php

/**
 * TechDivision\Import\Callbacks\ArrayValidatorCallback
 *
 * PHP version 7
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2024 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Product\Msi\Utils\ColumnKeys;
use TechDivision\Import\Product\Observers\AbstractProductImportObserver;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * storeview validator callback implementation.
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2024 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class StoreWebsiteValidatorObserver extends AbstractProductImportObserver
{
    /**
     * The store websites.
     *
     * @var array
     */
    protected $storeWebsites = array();

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /** @var array */
    protected array $entity;

    /** @var string */
    protected string $lastEntityId;

    /** @var ImportProcessorInterface */
    protected $importProcessor;

    /**
     * @param ImportProcessorInterface $importProcessor
     * @param StateDetectorInterface|null $stateDetector
     */
    public function __construct(
        ProductBunchProcessorInterface $productBunchProcessor,
        ImportProcessorInterface $importProcessor,
        StateDetectorInterface $stateDetector = null
    ) {
        // initialize the bunch processor instance
        $this->productBunchProcessor = $productBunchProcessor;
        $this->importProcessor = $importProcessor;
        // pass the processor and the state detector to the parent constructor
        parent::__construct($stateDetector);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function process()
    {
        $storeViewCodesByWebsiteCode = array();
        $storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE);
        $websiteCodes = $this->getValue(ColumnKeys::PRODUCT_WEBSITES, array(), array($this, 'explode'));

        $this->setLastEntityRowId();
        $this->getStoreWebsites();

        if ($this->isNullable($storeViewCode)) {
            $storeViewCode = $this->getSubject()->getDefaultStoreViewCode();
        }

        // If website_code null that mean the website code from default row
        if ($this->isNullable($websiteCodes) && $this->entity[MemberNames::ENTITY_ID] === (int)$this->lastEntityId ) {
            $websiteCodes = $this->storeWebsites;
            foreach ($websiteCodes as $websiteCode) {
                if ($websiteCode['is_default'] === 1) {
                    $storeViewCodesByWebsiteCode = $this->getStoreViewCodesByWebsiteCode($websiteCode['code']);
                }

            }
        } else {
            foreach ($websiteCodes as $websiteCode) {
                $storeViewCodesByWebsiteCode = array_merge($storeViewCodesByWebsiteCode, $this->getStoreViewCodesByWebsiteCode($websiteCode));
            }
        }

        if (!in_array($storeViewCode, $storeViewCodesByWebsiteCode)) {
            $message = sprintf(
                'The store "%s" does not belong to the website "%s" . Please check your data.',
                $attributeValue,
                $productWebsite
            );

            $this->getSubject()
                ->getSystemLogger()
                ->warning($this->getSubject()->appendExceptionSuffix($message));
            $this->getSubject()->mergeStatus(
                array(
                    RegistryKeys::NO_STRICT_VALIDATIONS => array(
                        basename($this->getSubject()->getFilename()) => array(
                            $this->getSubject()->getLineNumber() => array(
                                $attributeCode  => $message
                            )
                        )
                    )
                )
            );
        }
    }

    /**
     * Query whether or not the passed value IS empty and empty values are allowed.
     *
     * @param string $attributeValue The attribute value to query for
     *
     * @return boolean TRUE if empty values are allowed and the passed value IS empty
     */
    protected function isNullable($attributeValue)
    {
        return $attributeValue === '' || $attributeValue === null || empty($attributeValue);
    }

    /**
     * Returns an array with the codes of the store views related with the passed website code.
     *
     * @param string $websiteCode The code of the website to return the store view codes for
     *
     * @return array The array with the matching store view codes
     */
    protected function getStoreViewCodesByWebsiteCode($websiteCode)
    {
        return $this->getSubject()->getStoreViewCodesByWebsiteCode($websiteCode);
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

    /**
     * Return's the ID of the product that has been created recently.
     *
     * @return string The entity Id
     */
    protected function getLastEntityId()
    {
        return $this->getSubject()->getLastEntityId();
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
     * @return void
     */
    public function setLastEntityRowId(): void
    {
        if (!$this->hasBeenProcessed($this->getValue(ColumnKeys::SKU))) {
            $this->entity = $this->loadProduct($this->getValue(MemberNames::SKU));
            $this->setLastEntityId($this->entity[MemberNames::ENTITY_ID]);
            $this->lastEntityId = $this->getLastEntityId();
        }
    }

    /**
     * @return void
     */
    protected function getStoreWebsites()
    {
        // initialize the array with the store websites
        foreach ($this->importProcessor->getStoreWebsites() as $storeWebsite) {
            $this->storeWebsites[$storeWebsite[MemberNames::CODE]] = $storeWebsite;
        }
    }
}
