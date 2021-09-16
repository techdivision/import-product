<?php

/**
 * TechDivision\Import\Product\Assemblers\ProductAttributeAssembler
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Assemblers;

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Repositories\ProductIntRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductTextRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductDatetimeRepositoryInterface;

/**
 * Assembler implementation that provides functionality to assemble product attribute data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductAttributeAssembler implements ProductAttributeAssemblerInterface
{

    /**
     * The product datetime repository instance.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductDatetimeRepositoryInterface
     */
    protected $productDatetimeRepository;

    /**
     * The product decimal repository instance.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface
     */
    protected $productDecimalRepository;

    /**
     * The product integer repository instance.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductIntRepositoryInterface
     */
    protected $productIntRepository;

    /**
     * The product text repository instance.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductTextRepositoryInterface
     */
    protected $productTextRepository;

    /**
     * The product varchar repository instance.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface
     */
    protected $productVarcharRepository;

    /**
     * Initializes the assembler with the necessary repositories.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductDatetimeRepositoryInterface $productDatetimeRepository The product datetime repository instance
     * @param \TechDivision\Import\Product\Repositories\ProductDecimalRepositoryInterface  $productDecimalRepository  The product decimal repository instance
     * @param \TechDivision\Import\Product\Repositories\ProductIntRepositoryInterface      $productIntRepository      The product integer repository instance
     * @param \TechDivision\Import\Product\Repositories\ProductTextRepositoryInterface     $productTextRepository     The product text repository instance
     * @param \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface  $productVarcharRepository  The product varchar repository instance
     */
    public function __construct(
        ProductDatetimeRepositoryInterface $productDatetimeRepository,
        ProductDecimalRepositoryInterface $productDecimalRepository,
        ProductIntRepositoryInterface $productIntRepository,
        ProductTextRepositoryInterface $productTextRepository,
        ProductVarcharRepositoryInterface $productVarcharRepository
    ) {
        $this->productDatetimeRepository = $productDatetimeRepository;
        $this->productDecimalRepository = $productDecimalRepository;
        $this->productIntRepository = $productIntRepository;
        $this->productTextRepository = $productTextRepository;
        $this->productVarcharRepository = $productVarcharRepository;
    }

    /**
     * Intializes the existing attributes for the entity with the passed primary key.
     *
     * @param string  $pk      The primary key of the entity to load the attributes for
     * @param integer $storeId The ID of the store view to load the attributes for
     *
     * @return array The entity attributes
     */
    public function getProductAttributesByPrimaryKeyAndStoreId($pk, $storeId)
    {

        // initialize the array for the attributes
        $attributes = array();

        // load and prepare the datetime attributes
        $productDatetimes = $this->productDatetimeRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId);
        foreach ($productDatetimes as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load and prepare the decimal attributes
        $productDecimals = $this->productDecimalRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId);
        foreach ($productDecimals as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load and prepare the integer attributes
        $productIntegers = $this->productIntRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId);
        foreach ($productIntegers as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load and prepare the text attributes
        $productTexts = $this->productTextRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId);
        foreach ($productTexts as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load and prepare the varchar attributes
        $productVarchars = $this->productVarcharRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId);
        foreach ($productVarchars as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // return the array with the attributes
        return $attributes;
    }
}
