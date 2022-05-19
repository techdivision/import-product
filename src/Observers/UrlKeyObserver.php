<?php

/**
 * TechDivision\Import\Product\Observers\UrlKeyObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Product\Utils\VisibilityKeys;
use TechDivision\Import\Utils\RegistryKeys;
use Laminas\Filter\FilterInterface;
use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Utils\UrlKeyUtilInterface;
use TechDivision\Import\Utils\Filter\UrlKeyFilterTrait;
use TechDivision\Import\Utils\Generators\GeneratorInterface;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Services\ProductBunchProcessorInterface;
use TechDivision\Import\Observers\ObserverFactoryInterface;
use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Observer that extracts the URL key from the product name and adds a two new columns
 * with the their values.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class UrlKeyObserver extends AbstractProductImportObserver implements ObserverFactoryInterface
{

    /**
     * The trait that provides string => URL key conversion functionality.
     *
     * @var \TechDivision\Import\Utils\Filter\UrlKeyFilterTrait
     */
    use UrlKeyFilterTrait;

    /**
     * The URL key utility instance.
     *
     * @var \TechDivision\Import\Utils\UrlKeyUtilInterface
     */
    protected $urlKeyUtil;

    /**
     * The product bunch processor instance.
     *
     * @var \TechDivision\Import\Product\Services\ProductBunchProcessorInterface
     */
    protected $productBunchProcessor;

    /**
     * The reverse sequence generator instance.
     *
     * @var \TechDivision\Import\Utils\Generators\GeneratorInterface
     */
    protected $reverseSequenceGenerator;

    /**
     * The array with the root categories.
     *
     * @var array
     */
    protected $rootCategories = array();

    /**
     * The admin row
     *
     * @var array
     */
    protected $adminRow = array();

    /**
     * Initialize the observer with the passed product bunch processor and filter instance.
     *
     * @param \TechDivision\Import\Product\Services\ProductBunchProcessorInterface $productBunchProcessor    The product bunch processor instance
     * @param \Laminas\Filter\FilterInterface                                      $convertLiteralUrlFilter  The URL filter instance
     * @param \TechDivision\Import\Utils\UrlKeyUtilInterface                       $urlKeyUtil               The URL key utility instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface             $reverseSequenceGenerator The reverse sequence generator instance
     */
    public function __construct(
        ProductBunchProcessorInterface $productBunchProcessor,
        FilterInterface $convertLiteralUrlFilter,
        UrlKeyUtilInterface $urlKeyUtil,
        GeneratorInterface $reverseSequenceGenerator
    ) {
        $this->productBunchProcessor = $productBunchProcessor;
        $this->convertLiteralUrlFilter = $convertLiteralUrlFilter;
        $this->urlKeyUtil = $urlKeyUtil;
        $this->reverseSequenceGenerator = $reverseSequenceGenerator;
    }

    /**
     * Will be invoked by the observer visitor when a factory has been defined to create the observer instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return \TechDivision\Import\Observers\ObserverInterface The observer instance
     */
    public function createObserver(SubjectInterface $subject)
    {

        // load the root categories
        $rootCategories = $subject->getRootCategories();

        // initialize the array with the root categories
        // by using the entity ID as index
        foreach ($rootCategories as $rootCategory) {
            $this->rootCategories[(int) $rootCategory[MemberNames::ENTITY_ID]] = $rootCategory;
        }

        // return the initialized instance
        return $this;
    }

    /**
     * Return's the product bunch processor instance.
     *
     * @return \TechDivision\Import\Product\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    protected function getProductBunchProcessor() : ProductBunchProcessorInterface
    {
        return $this->productBunchProcessor;
    }

    /**
     * Returns the URL key utility instance.
     *
     * @return \TechDivision\Import\Utils\UrlKeyUtilInterface The URL key utility instance
     */
    protected function getUrlKeyUtil() : UrlKeyUtilInterface
    {
        return $this->urlKeyUtil;
    }

    /**
     * Returns the reverse sequence generator instance.
     *
     * @return \TechDivision\Import\Utils\Generators\GeneratorInterface The reverse sequence generator
     */
    protected function getReverseSequenceGenerator() : GeneratorInterface
    {
        return $this->reverseSequenceGenerator;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     * @throws \Exception Is thrown, if either column "url_key" or "name" have a value set
     */
    protected function process()
    {

        // initialize the URL key, the entity and the product
        $urlKey = null;
        $product = array();

        // prepare the store view code
        $this->getSubject()->prepareStoreViewCode();

        // set the entity ID for the product with the passed SKU
        if ($entity = $this->loadProduct($sku = $this->getValue(ColumnKeys::SKU))) {
            $this->setIds($product = $entity);
        } else {
            $this->setIds(array());
            $product[MemberNames::ENTITY_ID] = $this->getReverseSequenceGenerator()->generate();
        }

        // query whether or not the URL key column has a value
        if ($this->hasValue(ColumnKeys::URL_KEY)) {
            $urlKey = $this->getValue(ColumnKeys::URL_KEY);
        } else {
            // query whether or not the existing product `url_key` should be re-created from the product name
            if (is_array($entity) && !$this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::UPDATE_URL_KEY_FROM_NAME, true)) {
                // if the product already exists and NO re-creation from the product name has to
                // be done, load the original `url_key`from the product and use that to proceed
                $urlKey = $this->loadUrlKey($this->getSubject(), $this->getPrimaryKey());
            }

            // try to load the value from column `name` if URL key is still
            // empty, because we need it to process the the rewrites later on
            if ($urlKey === null || $urlKey === '' && $this->hasValue(ColumnKeys::NAME)) {
                $urlKey = $this->convertNameToUrlKey($this->getValue(ColumnKeys::NAME));
            }
        }

        // stop processing, if no URL key is available
        if ($urlKey === null || $urlKey === '') {
            // throw an exception, that the URL key can not be
            // initialized and we're in the default store view
            if ($this->getStoreViewCode(StoreViewCodes::ADMIN) === StoreViewCodes::ADMIN) {
                throw new \Exception(sprintf('Can\'t initialize the URL key for product "%s" because columns "url_key" or "name" have a value set for default store view', $sku));
            }

            if (!isset($this->adminRow[$sku][ColumnKeys::URL_KEY])) {
                // stop processing, because we're in a store
                // view row and a URL key is not mandatory and now URL-Key from admin row exists
                return;
            }
            // set url_key from admin
            $urlKey = $this->adminRow[$sku][ColumnKeys::URL_KEY];
        }

        // remember the admin row on SKU with url_key to be safe on later process
        if ($this->getStoreViewCode(StoreViewCodes::ADMIN) === StoreViewCodes::ADMIN) {
            $this->adminRow[$sku][ColumnKeys::URL_KEY] = $urlKey;
        }

        // if header not exists create it
        if (!$this->hasHeader(ColumnKeys::URL_KEY)) {
            $this->addHeader(ColumnKeys::URL_KEY);
        }

        // If not visible or we are in we do not need unique URL key
        if ($this->hasValue(ColumnKeys::VISIBILITY) && !$this->isVisible($this->getValue(ColumnKeys::VISIBILITY))) {
            $this->setValue(ColumnKeys::URL_KEY, $urlKey);
            return;
        }
        // generate the unique URL key
        $uniqueUrlKey = $this->makeUnique($this->getSubject(), $product, $urlKey, $this->getUrlPaths());

        if ($urlKey !== $uniqueUrlKey && !$this->getSubject()->isStrictMode()) {
            $message = sprintf(
                'Generate new unique URL key "%s" for store "%s" and product with SKU "%s"',
                $uniqueUrlKey,
                $this->getStoreViewCode(StoreViewCodes::ADMIN),
                $sku
            );
            $this->getSubject()->getSystemLogger()->warning($message);
            $this->mergeStatus(
                array(
                    RegistryKeys::NO_STRICT_VALIDATIONS => array(
                        basename($this->getFilename()) => array(
                            $this->getLineNumber() => array(
                                ColumnKeys::URL_KEY => $message
                            )
                        )
                    )
                )
            );
        }

        // set the unique URL key for further processing
        $this->setValue(ColumnKeys::URL_KEY, $uniqueUrlKey);
    }

    /**
     * Query whether or not the actual entity is visible.
     *
     * @param string $visibility Value from csv
     * @return boolean TRUE if the entity is visible, else FALSE
     */
    private function isVisible($visibility)
    {
        return $this->getSubject()->getVisibilityIdByValue($visibility) !== VisibilityKeys::VISIBILITY_NOT_VISIBLE;
    }

    /**
     * Extract's the category from the comma separeted list of categories
     * in column `categories` and return's an array with their URL paths.
     *
     * @return string[] Array with the URL paths of the categories found in column `categories`
     */
    protected function getUrlPaths()
    {

        // initialize the array for the URL paths of the cateogries
        $urlPaths = array();

        // extract the categories from the column `categories`
        $paths = $this->getValue(ColumnKeys::CATEGORIES, array(), array($this, 'explode'));

        // the URL paths are store view specific, so we need
        // the store view code to load the appropriate ones
        $storeViewCode = $this->getStoreViewCode(StoreViewCodes::ADMIN);

        try {
            // iterate of the found categories, load their URL path as well as the URL path of
            // parent categories, if they have the anchor flag activated and add it the array
            foreach ($paths as $path) {
                // load the category based on the category path
                $category = $this->getCategoryByPath($path, $storeViewCode);
                // try to resolve the URL paths recursively
                $this->resolveUrlPaths($urlPaths, $category, $storeViewCode);
            }
        } catch (\Exception $ex) {
            if (!$this->getSubject()->isStrictMode()) {
                $message = sprintf('Category error on SKU "%s"! Detail: %s', $this->getValue(ColumnKeys::SKU), $ex->getMessage());
                $this->getSystemLogger()->warning($message);
                $this->mergeStatus(
                    array(
                        RegistryKeys::NO_STRICT_VALIDATIONS => array(
                            basename($this->getFilename()) => array(
                                $this->getLineNumber() => array(
                                    ColumnKeys::CATEGORIES => $message
                                )
                            )
                        )
                    )
                );
                return $urlPaths;
            }
            throw $ex;
        }


        // return the array with the recursively resolved URL paths
        // of the categories that are related with the products
        return $urlPaths;
    }

    /**
     * Recursively resolves an array with the store view specific
     * URL paths of the passed category.
     *
     * @param array  $urlPaths       The array to append the URL paths to
     * @param array  $category       The category to resolve the list with URL paths
     * @param string $storeViewCode  The store view code to resolve the URL paths for
     * @param bool   $directRelation the flag whether or not the passed category is a direct relation to the product and has to added to the list
     *
     * @return void
     */
    protected function resolveUrlPaths(array &$urlPaths, array $category, string $storeViewCode, bool $directRelation = true)
    {

        // try to resolve the parent category IDs, but only if the parent or
        // the category itself is NOT a root category. The last case is
        // possible if the column directly contains the root category only
        if (isset($this->rootCategories[(int) $category[MemberNames::ENTITY_ID]]) === false &&
            isset($this->rootCategories[(int) $category[MemberNames::PARENT_ID]]) === false
        ) {
            // load the parent category
            $parent = $this->getCategory($category[MemberNames::PARENT_ID], $storeViewCode);
            // also resolve the URL paths for the parent category
            $this->resolveUrlPaths($urlPaths, $parent, $storeViewCode, false);
        }

        // query whether or not the URL path already
        // is part of the list (to avoid duplicates)
        if (in_array($category[MemberNames::URL_PATH], $urlPaths)) {
            return;
        }

        // append the URL path, either if we've a direct relation
        // or the category has the anchor flag actvated
        if ($directRelation === true || ($category[MemberNames::IS_ANCHOR] === 1 && $directRelation === false)) {
            $urlPaths[] = $category[MemberNames::URL_PATH];
        }
    }

    /**
     * Temporarily persist's the IDs of the passed product.
     *
     * @param array $product The product to temporarily persist the IDs for
     *
     * @return void
     */
    protected function setIds(array $product)
    {
        $this->setLastEntityId(isset($product[MemberNames::ENTITY_ID]) ? $product[MemberNames::ENTITY_ID] : null);
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
     * Return's the PK to of the product.
     *
     * @return integer The PK to create the relation with
     */
    protected function getPrimaryKey()
    {
        return $this->getSubject()->getLastEntityId();
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
     * Load's and return's the url_key with the passed primary ID.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject      The subject to load the URL key
     * @param int                                                       $primaryKeyId The ID from product
     *
     * @return string|null url_key or null
     */
    protected function loadUrlKey(UrlKeyAwareSubjectInterface $subject, $primaryKeyId)
    {
        return $this->getUrlKeyUtil()->loadUrlKey($subject, $primaryKeyId);
    }

    /**
     * Return's the category with the passed path.
     *
     * @param string $path          The path of the category to return
     * @param string $storeViewCode The code of a store view, defaults to admin
     *
     * @return array The category
     */
    protected function getCategoryByPath($path, $storeViewCode = StoreViewCodes::ADMIN)
    {
        return $this->getSubject()->getCategoryByPath($path);
    }

    /**
     * Make's the passed URL key unique by adding the next number to the end.
     *
     * @param \TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface $subject  The subject to make the URL key unique for
     * @param array                                                     $entity   The entity to make the URL key unique for
     * @param string                                                    $urlKey   The URL key to make unique
     * @param array                                                     $urlPaths The URL paths to make unique
     *
     * @return string The unique URL key
     */
    protected function makeUnique(UrlKeyAwareSubjectInterface $subject, array $entity, string $urlKey, array $urlPaths = array())
    {
        return $this->getUrlKeyUtil()->makeUnique($subject, $entity, $urlKey, $urlPaths);
    }

    /**
     * Return's the array with the root categories.
     *
     * @return array The array with the root categories
     */
    public function getRootCategories()
    {
        return $this->getSubject()->getRootCategories();
    }

    /**
     * Return's the category with the passed ID.
     *
     * @param integer $categoryId    The ID of the category to return
     * @param string  $storeViewCode The code of a store view, defaults to "admin"
     *
     * @return array The category data
     */
    protected function getCategory($categoryId, $storeViewCode = StoreViewCodes::ADMIN)
    {
        return $this->getSubject()->getCategory($categoryId, $storeViewCode);
    }
}
