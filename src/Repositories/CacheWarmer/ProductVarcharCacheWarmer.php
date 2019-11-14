<?php

/**
 * TechDivision\Import\Product\Repositories\CacheWarmer\ProductVarcharCacheWarmer
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Repositories\CacheWarmer;

use TechDivision\Import\Product\Utils\CacheKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Product\Repositories\ProductRepositoryInterface;
use TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface;
use TechDivision\Import\Cache\CacheAdapterInterface;
use TechDivision\Import\Repositories\EavAttributeRepositoryInterface;
use TechDivision\Import\Repositories\CacheWarmer\CacheWarmerInterface;

/**
 * Cache warmer implementation that pre-load the available products.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class ProductVarcharCacheWarmer implements CacheWarmerInterface
{

    /**
     * The cache adapter instance.
     *
     * @var \TechDivision\Import\Cache\CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * The repository with the cache that has to be warmed.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface
     */
    protected $repository;

    /**
     * The product repository instance.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * The EAV attribute repository instance.
     *
     * @var \TechDivision\Import\Repositories\EavAttributeRepositoryInterface
     */
    protected $eavAttributeRepository;

    /**
     * Initialize the cache warmer with the repository that has to be warmed.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface $repository             The repository to warm
     * @param \TechDivision\Import\Product\Repositories\ProductRepositoryInterface        $productRepository      The product repository
     * @param \TechDivision\Import\Repositories\EavAttributeRepositoryInterface           $eavAttributeRepository The EAV attribute repository
     * @param \TechDivision\Import\Cache\CacheAdapterInterface                            $cacheAdapter           The cache adapter instance
     */
    public function __construct(
        ProductVarcharRepositoryInterface $repository,
        ProductRepositoryInterface $productRepository,
        EavAttributeRepositoryInterface $eavAttributeRepository,
        CacheAdapterInterface $cacheAdapter
    ) {
        $this->repository = $repository;
        $this->cacheAdapter = $cacheAdapter;
        $this->productRepository = $productRepository;
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    /**
     * Warms the cache for the passed repository.
     *
     * @return void
     */
    public function warm()
    {

        // initialize the array for the unique keys
        $uniqueKeys = array();

        // load all available attributes
        foreach ($this->repository->findAll() as $attr) {
            // (re-)sinitialize the array for the cache keys
            $cacheKeys = array();
            // prepare the unique cache key for the attribue
            $uniqueKey = array(CacheKeys::PRODUCT_VARCHAR => $attr[$this->repository->getPrimaryKeyName()]);
            // load the EAV attribute
            $eavAttribute = $this->eavAttributeRepository->load($attr[MemberNames::ATTRIBUTE_ID]);

            // prepare the cache key and add the URL key value to the cache
            $cacheKeys[$this->cacheAdapter->cacheKey(
                array(
                    SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE =>
                    array(
                        MemberNames::ATTRIBUTE_CODE => $eavAttribute[MemberNames::ATTRIBUTE_CODE],
                        MemberNames::ENTITY_TYPE_ID => 4,
                        MemberNames::STORE_ID       => $attr[MemberNames::STORE_ID],
                        MemberNames::VALUE          => $attr[MemberNames::VALUE]
                    )
                )
            )] = $uniqueKey;

            // add the EAV attribute option value to the cache
            $this->cacheAdapter->toCache($uniqueKey, $attr, $cacheKeys);

            // prepare the params
            $params = array(
                MemberNames::PK       => $attr[$this->productRepository->getPrimaryKeyName()],
                MemberNames::STORE_ID => $attr[MemberNames::STORE_ID]
            );

            // append the unique key to the array
            $uniqueKeys[$this->cacheAdapter->cacheKey(array(SqlStatementKeys::PRODUCT_VARCHARS_BY_PK_AND_STORE_ID => $params))][] = $uniqueKey;
        }

        // add the unique keys => UIDs mapping to the cache
        foreach ($uniqueKeys as $cacheKey => $uids) {
            $this->cacheAdapter->toCache($cacheKey, $uids);
        }
    }
}
