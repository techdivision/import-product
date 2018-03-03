<?php

/**
 * TechDivision\Import\Product\Assemblers\ProductAttributeAssembler
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
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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

        // load the datetime attributes
        foreach ($this->productDatetimeRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load the decimal attributes
        foreach ($this->productDecimalRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load the integer attributes
        foreach ($this->productIntRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load the text attributes
        foreach ($this->productTextRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // load the varchar attributes
        foreach ($this->productVarcharRepository->findAllByPrimaryKeyAndStoreId($pk, $storeId) as $attribute) {
            $attributes[$attribute[MemberNames::ATTRIBUTE_ID]] = $attribute;
        }

        // return the array with the attributes
        return $attributes;
    }
}
