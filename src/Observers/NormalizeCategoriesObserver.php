<?php

/**
 * TechDivision\Import\Product\Observers\NormalizeCategoriesObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Observers\StateDetectorInterface;
use TechDivision\Import\Observers\ObserverFactoryInterface;
use TechDivision\Import\Serializer\SerializerFactoryInterface;

/**
 * Observer that normalizes the category path.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class NormalizeCategoriesObserver extends AbstractProductImportObserver implements ObserverFactoryInterface
{

    /**
     * The serializer used to serializer/unserialize the categories from the path column.
     *
     * @var \TechDivision\Import\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * The serializer factory instance.
     *
     * @var \TechDivision\Import\Serializer\SerializerFactoryInterface
     */
    protected $serializerFactory;

    /**
     * Initializes the observer with the state detector instance.
     *
     * @param \TechDivision\Import\Serializer\SerializerFactoryInterface $serializerFactory The serializer factory instance
     * @param \TechDivision\Import\Observers\StateDetectorInterface      $stateDetector     The state detector instance
     */
    public function __construct(
        SerializerFactoryInterface $serializerFactory,
        StateDetectorInterface $stateDetector = null
    ) {

        // initialize the serializer factory
        $this->serializerFactory = $serializerFactory;

        // pass the state detector to the parent constructor
        parent::__construct($stateDetector);
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

        // initialize the serializer instance
        $this->serializer = $this->serializerFactory->createSerializer($subject->getConfiguration()->getImportAdapter());

        // return the initialized instance
        return $this;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // do nothing if the categories column is not specified
        if (!$this->hasHeader(ColumnKeys::CATEGORIES)) {
            return;
        }

        // normalize the category path, if categories are availble
        $this->setValue(ColumnKeys::CATEGORIES, $this->getValue(ColumnKeys::CATEGORIES, null, function ($value) {
            return $this->serializer->normalize($value);
        }));
    }
}
