<?php

/**
 * TechDivision\Import\Product\Repositories\CacheWarmer\ProductCacheWarmer
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

use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Repositories\ProductRepositoryInterface;
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
class ProductCacheWarmer implements CacheWarmerInterface
{

    /**
     * The repository with the cache that has to be warmed.
     *
     * @var \TechDivision\Import\Product\Repositories\ProductRepositoryInterface
     */
    protected $repository;

    /**
     * Initialize the cache warmer with the repository that has to be warmed.
     *
     * @param \TechDivision\Import\Product\Repositories\ProductRepositoryInterface $repository The repository to warm
     */
    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
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

        // prepare the caches for the statements
        foreach ($this->repository->findAll() as $product) {
            // prepare the unique cache key for the product
            $uniqueKey = $cacheAdapter->cacheKey(
                ProductRepositoryInterface::class,
                array($product[$this->repository->getPrimaryKeyName()])
            );
            // add the EAV attribute option value to the cache, register the cache key reference as well
            $cacheAdapter->toCache($uniqueKey, $product, array($product[MemberNames::SKU] => $uniqueKey));
        }
    }
}
