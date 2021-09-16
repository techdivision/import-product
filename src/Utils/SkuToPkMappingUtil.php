<?php

/**
 * TechDivision\Import\Product\Utils\SkuToPkMappingUtil
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Utils;

use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Utils\EditionNamesInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * Utility class for edition based primary key handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class SkuToPkMappingUtil implements SkuToPkMappingUtilInterface
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The mapping for the edition for SKU => entity/row ID mapping.
     *
     * @var array
     */
    protected $editionSkuToPkMappings = array(
        EditionNamesInterface::EE => RegistryKeys::SKU_ROW_ID_MAPPING,
        EditionNamesInterface::CE => RegistryKeys::SKU_ENTITY_ID_MAPPING
    );

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns the primary key member name for the actual Magento edition.
     *
     * @return string The primary key member name
     * @throws \Exception Is thrown if the edition is not supported/available
     */
    public function getSkuToPkMappingKey()
    {

        // make sure the edition name is in upper cases
        $editionName = strtoupper($this->configuration->getMagentoEdition());

        // return the primary key member name for the actual edition
        if (isset($this->editionSkuToPkMappings[$editionName])) {
            return $this->editionSkuToPkMappings[$editionName];
        }

        // throw an exception if the edition is NOT supported/available
        throw new \Exception(sprintf('Found not supported/available Magento edition name "%s"', $editionName));
    }

    /**
     * Returns the array with the SKU => PK mapping for the given serial and registry processor.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor with the actual import status
     * @param string                                                   $serial            The serial of the actual import
     *
     * @return array The array with the SKU => PK mapping
     */
    public function getSkuToPkMapping(RegistryProcessorInterface $registryProcessor, $serial)
    {

        // load the status for the import of the passed subject
        $status = $registryProcessor->getAttribute(RegistryKeys::STATUS);

        // query whether or not the SKU => PK mapping is available
        if (isset($status[$this->getSkuToPkMappingKey()])) {
            return $status[$this->getSkuToPkMappingKey()];
        }

        // return an empty array
        return array();
    }

    /**
     * Adds the passed SKU => PK mapping to the passed registry processor.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor with the actual import status
     * @param string                                                   $serial            The serial of the actual import
     * @param array                                                    $skuToPkMapping    The SKU => PK mapping to add
     *
     * @return void
     */
    public function setSkuToPkMapping(RegistryProcessorInterface $registryProcessor, $serial, array $skuToPkMapping)
    {
        $registryProcessor->mergeAttributesRecursive(RegistryKeys::STATUS, array($this->getSkuToPkMappingKey() => $skuToPkMapping));
    }

    /**
     * Returns the array with the SKU => PK mapping for the given serial and registry processor.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor with the actual import status
     * @param string                                                   $serial            The serial of the actual import
     *
     * @return array The array with the PK => SKU mapping
     * @see \TechDivision\Import\Product\Utils\SkuToPkMappingUtilInterface::getSkuToPkMapping()
     */
    public function getInvertedSkuToPkMapping(RegistryProcessorInterface $registryProcessor, $serial)
    {
        return array_flip($this->getSkuToPkMapping($registryProcessor, $serial));
    }
}
