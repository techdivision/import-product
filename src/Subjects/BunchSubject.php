<?php

/**
 * TechDivision\Import\Product\Subjects\BunchSubject
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Subjects;

use Doctrine\Common\Collections\Collection;
use League\Event\EmitterInterface;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Utils\Generators\GeneratorInterface;
use TechDivision\Import\Utils\Mappings\MapperInterface;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Utils\RegistryKeys;
use TechDivision\Import\Product\Utils\VisibilityKeys;
use TechDivision\Import\Subjects\ExportableTrait;
use TechDivision\Import\Subjects\FileUploadTrait;
use TechDivision\Import\Subjects\ExportableSubjectInterface;
use TechDivision\Import\Subjects\FileUploadSubjectInterface;
use TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface;
use TechDivision\Import\Subjects\CleanUpColumnsSubjectInterface;
use TechDivision\Import\Utils\FileUploadConfigurationKeys;

/**
 * The subject implementation that handles the business logic to persist products.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class BunchSubject extends AbstractProductSubject implements ExportableSubjectInterface, FileUploadSubjectInterface, UrlKeyAwareSubjectInterface, CleanUpColumnsSubjectInterface
{

    /**
     * The trait that implements the export functionality.
     *
     * @var \TechDivision\Import\Subjects\ExportableTrait
     */
    use ExportableTrait;

    /**
     * The trait that provides file upload functionality.
     *
     * @var \TechDivision\Import\Subjects\FileUploadTrait
     */
    use FileUploadTrait;

    /**
     * The array with the pre-loaded entity IDs.
     *
     * @var array
     */
    protected $preLoadedEntityIds = array();

    /**
     * Mappings for the table column => CSV column header.
     *
     * @var array
     */
    protected $headerStockMappings = array(
        'qty'                         => array('qty', 'float'),
        'min_qty'                     => array('out_of_stock_qty', 'float'),
        'use_config_min_qty'          => array('use_config_min_qty', 'int'),
        'is_qty_decimal'              => array('is_qty_decimal', 'int'),
        'backorders'                  => array('allow_backorders', 'int'),
        'use_config_backorders'       => array('use_config_backorders', 'int'),
        'min_sale_qty'                => array('min_cart_qty', 'float'),
        'use_config_min_sale_qty'     => array('use_config_min_sale_qty', 'int'),
        'max_sale_qty'                => array('max_cart_qty', 'float'),
        'use_config_max_sale_qty'     => array('use_config_max_sale_qty', 'int'),
        'is_in_stock'                 => array('is_in_stock', 'int'),
        'notify_stock_qty'            => array('notify_on_stock_below', 'float'),
        'use_config_notify_stock_qty' => array('use_config_notify_stock_qty', 'int'),
        'manage_stock'                => array('manage_stock', 'int'),
        'use_config_manage_stock'     => array('use_config_manage_stock', 'int'),
        'use_config_qty_increments'   => array('use_config_qty_increments', 'int'),
        'qty_increments'              => array('qty_increments', 'float'),
        'use_config_enable_qty_inc'   => array('use_config_enable_qty_inc', 'int'),
        'enable_qty_increments'       => array('enable_qty_increments', 'int'),
        'is_decimal_divided'          => array('is_decimal_divided', 'int'),
    );

    /**
     * The array with the available visibility keys.
     *
     * @var array
     */
    protected $availableVisibilities = array(
        'Not Visible Individually' => VisibilityKeys::VISIBILITY_NOT_VISIBLE,
        'Catalog'                  => VisibilityKeys::VISIBILITY_IN_CATALOG,
        'Search'                   => VisibilityKeys::VISIBILITY_IN_SEARCH,
        'Catalog, Search'          => VisibilityKeys::VISIBILITY_BOTH
    );

    /**
     * The default callback mappings for the Magento standard product attributes.
     *
     * @var array
     */
    protected $defaultCallbackMappings = array(
        'visibility'           => array('import_product.callback.visibility'),
        'tax_class_id'         => array('import_product.callback.tax.class'),
        'bundle_price_type'    => array('import_product_bundle.callback.bundle.type'),
        'bundle_sku_type'      => array('import_product_bundle.callback.bundle.type'),
        'bundle_weight_type'   => array('import_product_bundle.callback.bundle.type'),
        'bundle_price_view'    => array('import_product_bundle.callback.bundle.price.view'),
        'bundle_shipment_type' => array('import_product_bundle.callback.bundle.shipment.type')
    );

    /**
     * The available entity types.
     *
     * @var array
     */
    protected $entityTypes = array();

    /**
     * The media roles loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $mediaRolesLoader;

    /**
     * The entity type code mapper instance.
     *
     * @var \TechDivision\Import\Utils\Mappings\MapperInterface
     */
    protected $entityTypeCodeMapper;

    /**
     * BunchSubject constructor
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor          The registry processor instance
     * @param \TechDivision\Import\Utils\Generators\GeneratorInterface $coreConfigDataUidGenerator The UID generator for the core config data
     * @param \Doctrine\Common\Collections\Collection                  $systemLoggers              The array with the system loggers instances
     * @param \League\Event\EmitterInterface                           $emitter                    The event emitter instance
     * @param \TechDivision\Import\Loaders\LoaderInterface             $mediaRolesLoader           The media type loader instance
     * @param \TechDivision\Import\Utils\Mappings\MapperInterface      $entityTypeCodeMapper       The entity type code mapper instance
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        GeneratorInterface $coreConfigDataUidGenerator,
        Collection $systemLoggers,
        EmitterInterface $emitter,
        LoaderInterface $mediaRolesLoader,
        MapperInterface $entityTypeCodeMapper
    ) {

        // set the loader for the media roles and the entity type code mapper
        $this->mediaRolesLoader = $mediaRolesLoader;
        $this->entityTypeCodeMapper = $entityTypeCodeMapper;

        // pass the other instances to the parent constructor
        parent::__construct($registryProcessor, $coreConfigDataUidGenerator, $systemLoggers, $emitter);
    }

    /**
     * Intializes the previously loaded global data for exactly one bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {

        // load the status of the actual import
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // load the global data we've prepared initially
        $this->entityTypes = $status[RegistryKeys::GLOBAL_DATA][RegistryKeys::ENTITY_TYPES];

        // initialize the flag whether to copy images or not
        if ($this->getConfiguration()->hasParam(FileUploadConfigurationKeys::COPY_IMAGES)) {
            $this->setCopyImages($this->getConfiguration()->getParam(FileUploadConfigurationKeys::COPY_IMAGES));
        }

        // initialize the flag whether to override images or not
        if ($this->getConfiguration()->hasParam(FileUploadConfigurationKeys::OVERRIDE_IMAGES)) {
            $this->setOverrideImages($this->getConfiguration()->getParam(FileUploadConfigurationKeys::OVERRIDE_IMAGES));
        }

        // initialize media directory => can be absolute or relative
        if ($this->getConfiguration()->hasParam(FileUploadConfigurationKeys::MEDIA_DIRECTORY)) {
            try {
                $this->setMediaDir($this->resolvePath($this->getConfiguration()->getParam(FileUploadConfigurationKeys::MEDIA_DIRECTORY)));
            } catch (\InvalidArgumentException $iae) {
                // only if we wanna copy images we need directories
                if ($this->hasCopyImages()) {
                    $this->getSystemLogger()->warning($iae->getMessage());
                }
            }
        }

        // initialize images directory => can be absolute or relative
        if ($this->getConfiguration()->hasParam(FileUploadConfigurationKeys::IMAGES_FILE_DIRECTORY)) {
            try {
                $this->setImagesFileDir($this->resolvePath($this->getConfiguration()->getParam(FileUploadConfigurationKeys::IMAGES_FILE_DIRECTORY)));
            } catch (\InvalidArgumentException $iae) {
                // only if we wanna copy images we need directories
                if ($this->hasCopyImages()) {
                    $this->getSystemLogger()->warning($iae->getMessage());
                }
            }
        }

        // invoke the parent method
        parent::setUp($serial);
    }

    /**
     * Clean up the global data after importing the bunch.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function tearDown($serial)
    {

        // invoke the parent method
        parent::tearDown($serial);

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the status
        $registryProcessor->mergeAttributesRecursive(
            RegistryKeys::STATUS,
            array(
                RegistryKeys::PRE_LOADED_ENTITY_IDS => $this->preLoadedEntityIds,
            )
        );
    }

    /**
     * Return's the default callback mappings.
     *
     * @return array The default callback mappings
     */
    public function getDefaultCallbackMappings()
    {
        return $this->defaultCallbackMappings;
    }

    /**
     * Return's the mappings for the table column => CSV column header.
     *
     * @return array The header stock mappings
     */
    public function getHeaderStockMappings()
    {
        return $this->headerStockMappings;
    }

    /**
     * Return's the visibility key for the passed visibility string.
     *
     * @param string $visibility The visibility string to return the key for
     *
     * @return integer The requested visibility key
     * @throws \Exception Is thrown, if the requested visibility is not available
     */
    public function getVisibilityIdByValue($visibility)
    {

        // query whether or not, the requested visibility is available
        if (isset($this->availableVisibilities[$visibility])) {
            // load the visibility ID, add the mapping and return the ID
            return $this->availableVisibilities[$visibility];
        }

        // throw an exception, if not
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Found invalid visibility %s', $visibility)
            )
        );
    }

    /**
     * Pre-load the entity ID for the passed product.
     *
     * @param array $product The product to be pre-loaded
     *
     * @return void
     */
    public function preLoadEntityId(array $product)
    {
        $this->preLoadedEntityIds[$product[MemberNames::SKU]] = $product[MemberNames::ENTITY_ID];
    }

    /**
     * Return's the entity type for the configured entity type code.
     *
     * @return array The requested entity type
     * @throws \Exception Is thrown, if the requested entity type is not available
     */
    public function getEntityType()
    {

        // query whether or not the entity type with the passed code is available
        if (isset($this->entityTypes[$entityTypeCode = $this->getEntityTypeCode()])) {
            return $this->entityTypes[$entityTypeCode];
        }

        // throw a new exception
        throw new \Exception(
            $this->appendExceptionSuffix(
                sprintf('Requested entity type "%s" is not available', $entityTypeCode)
            )
        );
    }

    /**
     * Loads and returns the media roles.
     *
     * @return array The array with the media roles
     */
    public function getMediaRoles(): array
    {
        return $this->mediaRolesLoader->load();
    }

    /**
     * Return's the entity type code to be used.
     *
     * @return string The entity type code to be used
     */
    public function getEntityTypeCode()
    {
        return $this->entityTypeCodeMapper->map(parent::getEntityTypeCode());
    }
}
