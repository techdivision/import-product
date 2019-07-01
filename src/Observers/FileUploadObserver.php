<?php

/**
 * TechDivision\Import\Product\Observers\FileUploadObserver
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

use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\ConfigurationKeys;

/**
 * Observer that uploads the product image files specified in a CSV file to a
 * configurable directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class FileUploadObserver extends AbstractProductImportObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not we've the flag to upload the image files has been set
        if ($this->getSubject()->getConfiguration()->hasParam(ConfigurationKeys::COPY_IMAGES)) {
            // query whether or not we've to upload the image files
            if ($this->getSubject()->getConfiguration()->getParam(ConfigurationKeys::COPY_IMAGES)) {
                // load the subject
                $subject = $this->getSubject();

                // initialize the array for the actual images
                $actualImageNames = array();

                // iterate over the available image fields
                foreach (array_keys($this->getImageTypes()) as $imageColumnName) {
                    // do nothing if the column is empty
                    if (!$this->hasValue($imageColumnName)) {
                        continue;
                    }

                    // override the image path with the new one, if the image has already been processed
                    if (isset($actualImageNames[$imageName = $this->getValue($imageColumnName)])) {
                        $this->setValue($imageColumnName, $actualImageNames[$imageName]);
                        continue;
                    }

                    try {
                        // upload the file and set the new image path
                        $imagePath = $this->getSubject()->uploadFile($imageName);
                        // override the image path with the new one
                        $this->setValue($imageColumnName, $imagePath);
                        // add the image to the list with processed images
                        $actualImageNames[$imageName] = $imagePath;

                        // log a message that the image has been copied
                        $this->getSubject()
                             ->getSystemLogger()
                             ->debug(
                                 sprintf(
                                     'Successfully copied image type %s with name %s => %s',
                                     $imageColumnName,
                                     $imageName,
                                     $imagePath
                                 )
                             );
                    } catch (\Exception $e) {
                        // query whether or not debug mode has been enabled
                        if ($subject->isDebugMode()) {
                            $subject->getSystemLogger()->warning($subject->appendExceptionSuffix($e->getMessage()));
                        } else {
                            throw $subject->wrapException(array($imageColumnName), $e);
                        }
                    }
                }

                // query whether or not, we've additional images
                if ($additionalImages = $this->getValue(ColumnKeys::ADDITIONAL_IMAGES, null, array($this, 'explode'))) {
                    // process all the additional images
                    foreach ($additionalImages as $key => $additionalImageName) {
                        // query whether or not the image has already been processed
                        if (isset($actualImageNames[$additionalImageName])) {
                            // if yes, override the image path
                            $additionalImages[$key] = $imagePath;
                            // and override the image path with the new one
                            $this->setValue($imageColumnName, $actualImageNames[$additionalImageName]);
                            continue;
                        }

                        try {
                            // upload the file and set the new image path
                            $imagePath = $this->getSubject()->uploadFile($additionalImageName);
                            // override the image path
                            $additionalImages[$key] = $imagePath;
                            // add the image to the list with processed images
                            $actualImageNames[$additionalImageName] = $imagePath;

                            // log a message that the image has been copied
                            $this->getSubject()
                                ->getSystemLogger()
                                 ->debug(
                                     sprintf(
                                         'Successfully copied additional image wth name %s => %s',
                                         $additionalImageName,
                                         $imagePath
                                     )
                                 );
                        } catch (\Exception $e) {
                            // query whether or not debug mode has been enabled
                            if ($subject->isDebugMode()) {
                                $subject->getSystemLogger()->warning($subject->appendExceptionSuffix($e->getMessage()));
                            } else {
                                throw $subject->wrapException(array(ColumnKeys::ADDITIONAL_IMAGES), $e);
                            }
                        }
                    }

                    // override the image paths with the new one
                    $this->setValue(ColumnKeys::ADDITIONAL_IMAGES, implode(',', $additionalImages));
                }
            }
        }
    }

    /**
     * Return's the array with the available image types and their label columns.
     *
     * @return array The array with the available image types
     */
    protected function getImageTypes()
    {
        return $this->getSubject()->getImageTypes();
    }
}
