<?php

/**
 * TechDivision\Import\Product\Listeners\RegisterSkuToPkMappingListener
 *
 * PHP version 7
 *
 * @author    Klaas-Tido Rühl <kr@refusion.com>
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 REFUSiON GmbH <info@refusion.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      https://www.techdivision.com
 * @link      https://www.refusion.com
 */

namespace TechDivision\Import\Product\Listeners;

use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Product\Utils\SkuToPkMappingUtilInterface;
use TechDivision\Import\Product\Subjects\SkuToPkMappingAwareSubjectInterface;

/**
 * After the subject has finished it's processing, this listener to add the SKU => PK mapping to the status in the registry processor.
 *
 * @author    Klaas-Tido Rühl <kr@refusion.com>
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 REFUSiON GmbH <info@refusion.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      https://www.techdivision.com
 * @link      https://www.refusion.com
 */
class RegisterSkuToPkMappingListener extends \League\Event\AbstractListener
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * The invoking tier price processor instance.
     *
     * @var \TechDivision\Import\Product\Utils\SkuToPkMappingUtilInterface
     */
    protected $skuToPkMappingUtil;

    /**
     * Initializes the listener with the tier price processor.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface       $registryProcessor  The registry processor instance
     * @param \TechDivision\Import\Product\Utils\SkuToPkMappingUtilInterface $skuToPkMappingUtil The observer instance
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        SkuToPkMappingUtilInterface $skuToPkMappingUtil
    ) {
        $this->registryProcessor = $registryProcessor;
        $this->skuToPkMappingUtil = $skuToPkMappingUtil;
    }

    /**
     * Returns the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }

    /**
     * Returns the tier price processor instance.
     *
     * @return \TechDivision\Import\Product\Utils\SkuToPkMappingUtilInterface The processor instance
     */
    protected function getSkuToPkMappingUtil()
    {
        return $this->skuToPkMappingUtil;
    }

    /**
     * Handle the event.
     *
     * Deletes the tier prices for all the products, which have been touched by the import,
     * and which were not part of the tier price import.
     *
     * @param \League\Event\EventInterface                                                   $event   The event that triggered the listener
     * @param \TechDivision\Import\Product\Subjects\SkuToPkMappingAwareSubjectInterface|null $subject The plugin that triggered the listener
     *
     * @return void
     */
    public function handle(\League\Event\EventInterface $event, SkuToPkMappingAwareSubjectInterface $subject = null)
    {
        if ($subject != null) {
            $this->getSkuToPkMappingUtil()->setSkuToPkMapping($this->getRegistryProcessor(), $subject->getSerial(), $subject->getSkuToPkMappings());
        }
    }
}
