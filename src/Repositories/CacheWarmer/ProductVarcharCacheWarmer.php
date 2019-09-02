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
use TechDivision\Import\Product\Utils\ParamNames;
use TechDivision\Import\Product\Utils\SqlStatementKeys;
use TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface;
use TechDivision\Import\Repositories\CacheWarmer\CacheWarmerInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Repositories\StoreRepositoryInterface;

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
     * The repository with the cache that has to be warmed.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface
     */
    protected $repository;

    /**
     * The store repository.
     *
     * @var \TechDivision\Import\Repositories\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * Initialize the cache warmer with the repository that has to be warmed.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductVarcharRepositoryInterface $repository      The repository to warm
     * @param \TechDivision\Import\Repositories\StoreRepositoryInterface                  $storeRepository The store repository
     */
    public function __construct(
        ProductVarcharRepositoryInterface $repository,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->repository = $repository;
        $this->storeRepository = $storeRepository;
    }

    /**
     * Warms the cache for the passed repository.
     *
     * @return void
     */
    public function warm()
    {

        // load the cache adapter
        /** @var \TechDivision\Import\Cache\CacheAdapterInterface $cacheAdapter */
        $cacheAdapter = $this->repository->getCacheAdapter();

        // load the available stores
        $stores = $this->storeRepository->findAll();

        // iterate over the stores to prepare the cache
        foreach ($stores as $store) {
            // load the product varchar values we want to cache
            $productVarchars = $this->repository->findAllByAttributeCodeAndEntityTypeIdAndStoreId(
                $attributeCode = MemberNames::URL_KEY,
                $entityTypeId = 4,
                $store[MemberNames::STORE_ID]
            );

            // prepare the caches for the statements
            foreach ($productVarchars as $productVarchar) {
                // (re-)sinitialize the array for the cache keys
                $cacheKeys = array();

                // prepare the unique cache key for the product
                $uniqueKey = array(CacheKeys::PRODUCT_VARCHAR => $productVarchar[$this->repository->getPrimaryKeyName()]);

                // prepare the cache key and add the URL key value to the cache
                $cacheKeys[$cacheAdapter->cacheKey(
                    array(
                        SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE =>
                        array(
                            ParamNames::ATTRIBUTE_CODE => $attributeCode,
                            ParamNames::ENTITY_TYPE_ID => $entityTypeId,
                            ParamNames::STORE_ID       => $productVarchar[MemberNames::STORE_ID],
                            ParamNames::VALUE          => $productVarchar[MemberNames::VALUE]
                        )
                    )
                )] = $uniqueKey;

                // add the EAV attribute option value to the cache
                $cacheAdapter->toCache($uniqueKey, $productVarchar, $cacheKeys);
            }
        }
    }
}
