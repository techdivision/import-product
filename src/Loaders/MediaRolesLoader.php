<?php

/**
 * TechDivision\Import\Product\Loaders\MediaRolesLoader
 *
 * @author    Marcus Döllerer <m.doellerer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      https://www.techdivision.com
 */

namespace TechDivision\Import\Product\Loaders;

use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Services\ImportProcessorInterface;

/**
 * Loader for media roles.
 *
 * @author    Marcus Döllerer <m.doellerer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @link      https://www.techdivision.com
 */
class MediaRolesLoader implements LoaderInterface
{

    /**
     * The media roles array (default: ['base', 'small', 'thumbnail', 'swatch']).
     *
     * @var array
     */
    protected $mediaRoles = array();

    /**
     * The import processor.
     *
     * @var ImportProcessorInterface
     */
    protected $importProcessor;

    /**
     * ImageMediaRolesLoader constructor
     *
     * @param ImportProcessorInterface $importProcessor The import processor.
     */
    public function __construct(ImportProcessorInterface $importProcessor)
    {

        // initialize the import processor instance
        $this->importProcessor = $importProcessor;

        // initialize the media roles
        $this->mediaRoles = $this->createMediaRoles();
    }

    /**
     * Loads and returns the media roles.
     *
     * @return array The array with the media roles
     */
    public function load()
    {
        return $this->mediaRoles;
    }

    /**
     * Creates media roles from available image types.
     *
     * @return array
     */
    public function createMediaRoles()
    {

        // initialize default values
        $mediaRoles = array();

        // derive media roles form image types
        foreach ($this->importProcessor->getImageTypes() as $imageColumnName => $imageLabelColumnName) {
            // create the role based prefix for the image columns
            $role = str_replace('_image', '', $imageColumnName);

            // initialize the values for the corresponding media role
            $mediaRoles[$role] = array(
                ColumnKeys::IMAGE_PATH     => $imageColumnName,
                ColumnKeys::IMAGE_LABEL    => $imageLabelColumnName,
                ColumnKeys::IMAGE_POSITION => sprintf('%s_image_position', $role)
            );
        }

        // return the array with the media roles
        return $mediaRoles;
    }
}
