<?php

/**
 * TechDivision\Import\Product\Observers\AbstractProductImportObserver
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Observers;

use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Observers\AbstractObserver;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
abstract class AbstractProductImportObserver extends AbstractObserver implements ProductImportObserverInterface
{

    /**
     * The actual row, that has to be processed.
     *
     * @var array
     */
    protected $row = array();

    /**
     * Set's the actual row, that has to be processed.
     *
     * @param array $row The row
     *
     * @return void
     */
    public function setRow(array $row)
    {
        $this->row = $row;
    }

    /**
     * Return's the actual row, that has to be processed.
     *
     * @return array The row
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param array $row The row to handle
     *
     * @return array The modified row
     * @see \TechDivision\Import\Product\Observers\ImportObserverInterface::handle()
     */
    public function handle(array $row)
    {

        // initialize the row
        $this->setRow($row);

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

    }

    /**
     * Set's the array containing header row.
     *
     * @param array $headers The array with the header row
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->getSubject()->setHeaders($headers);
    }

    /**
     * Return's the array containing header row.
     *
     * @return array The array with the header row
     */
    protected function getHeaders()
    {
        return $this->getSubject()->getHeaders();
    }

    /**
     * Queries whether or not the header with the passed name is available.
     *
     * @param string $name The header name to query
     *
     * @return boolean TRUE if the header is available, else FALSE
     */
    protected function hasHeader($name)
    {
        return $this->getSubject()->hasHeader($name);
    }

    /**
     * Return's the header value for the passed name.
     *
     * @param string $name The name of the header to return the value for
     *
     * @return mixed The header value
     * \InvalidArgumentException Is thrown, if the header with the passed name is NOT available
     */
    protected function getHeader($name)
    {
        return $this->getSubject()->getHeader($name);
    }

    /**
     * Add's the header with the passed name and position, if not NULL.
     *
     * @param string $name The header name to add
     *
     * @return integer The new headers position
     */
    protected function addHeader($name)
    {
        return $this->getSubject()->addHeader($name);
    }

    /**
     * Return's TRUE if the passed SKU is the actual one.
     *
     * @param string $sku The SKU to check
     *
     * @return boolean TRUE if the passed SKU is the actual one
     */
    protected function isLastSku($sku)
    {
        return $this->getSubject()->getLastSku() === $sku;
    }

    /**
     * Return's the ID of the product that has been created recently.
     *
     * @return string The entity Id
     */
    protected function getLastEntityId()
    {
        return $this->getSubject()->getLastEntityId();
    }

    /**
     * Return's the source date format to use.
     *
     * @return string The source date format
     */
    protected function getSourceDateFormat()
    {
        return $this->getSubject()->getSourceDateFormat();
    }

    /**
     * Cast's the passed value based on the backend type information.
     *
     * @param string $backendType The backend type to cast to
     * @param mixed  $value       The value to be casted
     *
     * @return mixed The casted value
     */
    protected function castValueByBackendType($backendType, $value)
    {
        return $this->getSubject()->castValueByBackendType($backendType, $value);
    }

    /**
     * Set's the store view code the create the product/attributes for.
     *
     * @param string $storeViewCode The store view code
     *
     * @return void
     */
    protected function setStoreViewCode($storeViewCode)
    {
        $this->getSubject()->setStoreViewCode($storeViewCode);
    }

    /**
     * Return's the store view code the create the product/attributes for.
     *
     * @param string|null $default The default value to return, if the store view code has not been set
     *
     * @return string The store view code
     */
    protected function getStoreViewCode($default = null)
    {
        return $this->getSubject()->getStoreViewCode($default);
    }

    /**
     * Prepare's the store view code in the subject.
     *
     * @return void
     */
    protected function prepareStoreViewCode()
    {

        // re-set the store view code
        $this->setStoreViewCode(null);

        // initialize the store view code
        if ($storeViewCode = $this->getValue(ColumnKeys::STORE_VIEW_CODE)) {
            $this->setStoreViewCode($storeViewCode);
        }
    }

    /**
     * Tries to format the passed value to a valid date with format 'Y-m-d H:i:s'.
     * If the passed value is NOT a valid date, NULL will be returned.
     *
     * @param string|null $value The value to format
     *
     * @return string The formatted date
     */
    protected function formatDate($value)
    {

        // create a DateTime instance from the passed value
        if ($dateTime = \DateTime::createFromFormat($this->getSourceDateFormat(), $value)) {
            return $dateTime->format('Y-m-d H:i:s');
        }

        // return NULL, if the passed value is NOT a valid date
        return null;
    }

    /**
     * Extracts the elements of the passed value by exploding them
     * with the also passed separator.
     *
     * @param string $value     The value to extract
     * @param string $separator The separator used to extrace the elements
     *
     * @return array The exploded values
     */
    protected function explode($value, $separator = ',')
    {
        return explode($separator, $value);
    }

    /**
     * Query whether or not a value for the column with the passed name exists.
     *
     * @param string $name The column name to query for a valid value
     *
     * @return boolean TRUE if the value is set, else FALSE
     */
    protected function hasValue($name)
    {

        // query whether or not the header is available
        if ($this->hasHeader($name)) {
            // load the key for the row
            $headerValue = $this->getHeader($name);

            // query whether the rows column has a vaild value
            return (isset($this->row[$headerValue]) && $this->row[$headerValue] != '');
        }

        // return FALSE if not
        return false;
    }

    /**
     * Set the value in the passed column name.
     *
     * @param string $name  The column name to set the value for
     * @param mixed  $value The value to set
     *
     * @return void
     */
    protected function setValue($name, $value)
    {
        $this->row[$this->getHeader($name)] = $value;
    }

    /**
     * Resolve's the value with the passed colum name from the actual row. If a callback will
     * be passed, the callback will be invoked with the found value as parameter. If
     * the value is NULL or empty, the default value will be returned.
     *
     * @param string        $name     The name of the column to return the value for
     * @param mixed|null    $default  The default value, that has to be returned, if the row's value is empty
     * @param callable|null $callback The callback that has to be invoked on the value, e. g. to format it
     *
     * @return mixed|null The, almost formatted, value
     */
    protected function getValue($name, $default = null, callable $callback = null)
    {

        // initialize the value
        $value = null;

        // query whether or not the header is available
        if ($this->hasHeader($name)) {
            // load the header value
            $headerValue = $this->getHeader($name);
            // query wheter or not, the value with the requested key is available
            if ((isset($this->row[$headerValue]) && $this->row[$headerValue] != '')) {
                $value = $this->row[$headerValue];
            }
        }

        // query whether or not, a callback has been passed
        if ($value != null && is_callable($callback)) {
            $value = call_user_func($callback, $value);
        }

        // query whether or not
        if ($value == null && $default !== null) {
            $value = $default;
        }

        // return the value
        return $value;
    }

    /**
     * Initialize's and return's a new entity with the status 'create'.
     *
     * @param array $attr The attributes to merge into the new entity
     *
     * @return array The initialized entity
     */
    protected function initializeEntity(array $attr = array())
    {
        return array_merge(array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE), $attr);
    }

    /**
     * Merge's and return's the entity with the passed attributes and set's the
     * status to 'update'.
     *
     * @param array $entity The entity to merge the attributes into
     * @param array $attr   The attributes to be merged
     *
     * @return array The merged entity
     */
    protected function mergeEntity(array $entity, array $attr)
    {
        return array_merge($entity, $attr, array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_UPDATE));
    }
}
