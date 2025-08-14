<?php

/**
 * TechDivision\Import\Product\Observers\StoreWebsiteValidatorObserver
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

use Exception;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Product\Msi\Utils\ColumnKeys;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\StoreViewCodes;

/**
 * Store view validator implementation.
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
     * The admin row
     *
     * @var array
     */
    protected $adminRow = array();

    /**
     * The product bunch processor instance.
     *
     * @var ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /** @var array */
    protected array $entity;

    /** @var string */
    protected string $lastEntityId;

    /** @var ImportProcessorInterface */
    protected $importProcessor;

    /**
     * @param ProductBunchProcessorInterface $productBunchProcessor
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
        $sku = $this->getValue(ColumnKeys::SKU);
        $storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE);
        $productWebsites = $this->getValue(ColumnKeys::PRODUCT_WEBSITES, [], [$this, 'explode']);

        // Initialize the store view code
        $this->getSubject()->prepareStoreViewCode();

        // Handle product websites for admin store view
        if ($this->isAdminStoreView()) {
            $this->setAdminProductWebsites($sku, $productWebsites);
        }
        // Init data
        $this->setLastEntityRowId($sku);
        $this->setStoreWebsites();

        // if the value is null or empty, it is not processed
        if ($this->isNullable($storeViewCode)) {
           return;
        }

        // Get or resolve product websites
        if ($this->isNullable($productWebsites)) {
            $productWebsitesToCheck = $this->resolveProductWebsites($productWebsites, $sku);
        }
        else {
            $productWebsitesToCheck = $productWebsites;
        }

        // Validate store view codes by website code
        $noStoreviewOnWebsite=[];
        $storeViewCodesByWebsiteCode = $this->getStoreViewCodesForWebsites($productWebsitesToCheck);
        foreach ($productWebsitesToCheck as $productWebsite) {
            if (!in_array($storeViewCode, $storeViewCodesByWebsiteCode[$productWebsite])) {
                $noStoreviewOnWebsite[] = $productWebsite;
            }
        }

        // if the defined website in "$productWebsitesToCheck" are identical to the unassigned ones,
        // then the "$storeViewCode" is incorrectly defined for the website
        if (count($noStoreviewOnWebsite) == count($productWebsitesToCheck)) {
            $message = sprintf(
                'The store "%s" for SKU "%s" does not belong to the website "%s". Please check your data.',
                $storeViewCode,
                $sku,
                \implode('" or "', $noStoreviewOnWebsite)
            );

            $this->getSubject()
                ->getSystemLogger()
                ->warning($this->getSubject()->appendExceptionSuffix($message));

            $this->getSubject()->mergeStatus([
                RegistryKeys::NO_STRICT_VALIDATIONS => [
                    basename($this->getSubject()->getFilename()) => [
                        $this->getSubject()->getLineNumber() => [
                            ColumnKeys::STORE_VIEW_CODE => $message
                        ]
                    ]
                ]
            ]);
        }
    }

    /**
     * @return array|mixed|null
     */
    private function resolveStoreViewCode()
    {
        $storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE);
        if ($this->isNullable($storeViewCode)) {
            $storeViewCode = $this->getSubject()->getDefaultStoreViewCode();
        }
        return $storeViewCode;
    }

    /**
     * @return bool
     */
    private function isAdminStoreView()
    {
        return $this->getStoreViewCode(StoreViewCodes::ADMIN) === StoreViewCodes::ADMIN;
    }

    /**
     * @param $sku
     * @param $productWebsites
     * @return void
     */
    private function setAdminProductWebsites($sku, $productWebsites)
    {
        $this->adminRow[$sku][ColumnKeys::PRODUCT_WEBSITES] = $productWebsites;
    }

    /**
     * @param $productWebsites
     * @param $sku
     * @return mixed
     */
    private function resolveProductWebsites($productWebsites, $sku)
    {
        if ($this->isNullable($productWebsites) && $this->entity[MemberNames::ENTITY_ID] === (int)$this->lastEntityId) {
            return $this->adminRow[$sku][ColumnKeys::PRODUCT_WEBSITES];
        }
        return $productWebsites;
    }

    /**
     * @param $productWebsites
     * @return array
     */
    private function getStoreViewCodesForWebsites($productWebsites)
    {
        $storeViewCodesByWebsiteCode = [];
        foreach ($productWebsites as $productWebsite) {
            if (isset($this->storeWebsites[$productWebsite])) {
                $websiteCode = $this->storeWebsites[$productWebsite]['code'];

                if (!isset($storeViewCodesByWebsiteCode[$productWebsite])) {
                    $storeViewCodesByWebsiteCode[$productWebsite] = [];
                }

                // Merge the store view codes
                $storeViewCodesByWebsiteCode[$productWebsite] = array_merge(
                    $storeViewCodesByWebsiteCode[$productWebsite],
                    $this->getStoreViewCodesByWebsiteCode($websiteCode)
                );
            }
        }
        return $storeViewCodesByWebsiteCode;
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
     * @return ProductBunchProcessorInterface The product bunch processor instance
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
    public function setLastEntityRowId($sku): void
    {
        if (!$this->hasBeenProcessed($sku)) {
            $this->entity = $this->loadProduct($sku);
            $this->setLastEntityId($this->entity[MemberNames::ENTITY_ID]);
            $this->lastEntityId = $this->getLastEntityId();
        }
    }

    /**
     * @return void
     */
    protected function setStoreWebsites()
    {
        // initialize the array with the store websites
        foreach ($this->importProcessor->getStoreWebsites() as $storeWebsite) {
            $this->storeWebsites[$storeWebsite[MemberNames::CODE]] = $storeWebsite;
        }
    }
}
