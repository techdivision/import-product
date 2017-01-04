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
    public function getHeaders()
    {
        return $this->getSubject()->getHeaders();
    }

    /**
     * Return's TRUE if the passed SKU is the actual one.
     *
     * @param string $sku The SKU to check
     *
     * @return boolean TRUE if the passed SKU is the actual one
     */
    public function isLastSku($sku)
    {
        return $this->getSubject()->getLastSku() === $sku;
    }

    /**
     * Return's the ID of the product that has been created recently.
     *
     * @return string The entity Id
     */
    public function getLastEntityId()
    {
        return $this->getSubject()->getLastEntityId();
    }

    /**
     * Return's the source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat()
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
    public function castValueByBackendType($backendType, $value)
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
    public function setStoreViewCode($storeViewCode)
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
    public function getStoreViewCode($default = null)
    {
        return $this->getSubject()->getStoreViewCode($default);
    }

    /**
     * Prepare's the store view code in the subject.
     *
     * @return void
     */
    public function prepareStoreViewCode()
    {

        // load the headers
        $row = $this->getRow();
        $headers = $this->getHeaders();

        // initialize the store view code
        $this->setStoreViewCode(null);

        // initialize the store view code
        if (isset($row[$headers[ColumnKeys::STORE_VIEW_CODE]])) {
            $storeViewCode = $row[$headers[ColumnKeys::STORE_VIEW_CODE]];
            if (!empty($storeViewCode)) {
                $this->setStoreViewCode($storeViewCode);
            }
        }
    }

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
     * Tries to format the passed value to a valid date with format 'Y-m-d H:i:s'.
     * If the passed value is NOT a valid date, NULL will be returned.
     *
     * @param string|null $value The value to format
     *
     * @return string The formatted date
     */
    public function formatDate($value)
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
    public function explode($value, $separator = ',')
    {
        return explode($separator, $value);
    }

    /**
     * Query whether or not the value with the passed key exists.
     *
     * @param string $key The key of the value to query
     *
     * @return boolean TRUE if the value is set, else FALSE
     */
    public function hasValue($key)
    {

        // load row and headers
        $row = $this->getRow();
        $headers = $this->getHeaders();

        // query whether or not the value exists
        return isset($row[$headers[$key]]);
    }

    /**
     * Resolve's the value with the passed key from the actual row. If a callback will
     * be passed, the callback will be invoked with the found value as parameter. If
     * the value is NULL or empty, the default value will be returned.
     *
     * @param string        $key      The key of the value to return
     * @param mixed|null    $default  The default value, that has to be returned, if the row's value is empty
     * @param callable|null $callback The callback that has to be invoked on the value, e. g. to format it
     *
     * @return mixed|null The, almost formatted, value
     */
    public function getValue($key, $default = null, callable $callback = null)
    {

        // load row and headers
        $row = $this->getRow();
        $headers = $this->getHeaders();

        // initialize the value
        $value = null;

        // query wheter or not, the value with the requested key is available
        if (isset($headers[$key]) && isset($row[$headers[$key]])) {
            $value = $row[$headers[$key]];
        }

        // query whether or not, a callback has been passed
        if ($value != null && is_callable($callback)) {
            $value = call_user_func($callback, $value);
        }

        // query whether or not
        if ($value == null && $default != null) {
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
    public function initializeEntity(array $attr = array())
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
    public function mergeEntity(array $entity, array $attr)
    {
        return array_merge($entity, $attr, array(EntityStatus::MEMBER_NAME => EntityStatus::STATUS_UPDATE));
    }
}
