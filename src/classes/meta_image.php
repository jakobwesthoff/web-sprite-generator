<?php
/**
 * wsgen render aware image meta-information
 *
 * This file is part of wsgen.
 *
 * wsgen is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * wsgen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wsgen; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
namespace org\westhoffswelt\wsgen;

/**
 * Abstract interface describing an image and all its needed meta information.
 * 
 * One of these implemenentation is supposed to exist for every renderer.
 * Renderers may depend on features or data providided by there own MetaImage.
 * 
 * A certain subset of functionallity is however always expected to be
 * implemented, as it may be needed by LayoutManagers and such.
 */
abstract class MetaImage 
{
    /**
     * Filename of the image the image this instance represents. 
     * 
     * @var string
     */
    protected $filename;

    /**
     * Construct the meta information object with the filename to take as input
     * as argument.
     * 
     * @param string $filename 
     */
    public function __construct( $filename ) 
    {
        $this->filename = $filename;
    }

    /**
     * Retrieve the filename of the opened image. 
     * 
     * @return string
     */
    public function getFilename() 
    {
        return $this->filename;
    }

    /**
     * Calculate the resolution of the image and return it. 
     * 
     * The resultion is supposed to be returned as tuple:
     * <code>
     *   array( $width, $height )
     * </code>
     * 
     * @return array
     * @throws RuntimeException if the resolution of the given file could not
     *         be determined.
     */
    public abstract function getResolution();

    /**
     * Provide the color value of the pixel at coordinates $x, $y. 
     * 
     * The returned color is supposed to be a tuple containing the byte value
     * of each channel:
     * <code>
     *   array( 
     *      $red,
     *      $blue,
     *      $green,
     *      $alpha
     *   );
     * </code>
     * As all values are supposed to be the actual byte values all are
     * represented by an integer between 0 and 255.
     * 
     * @param int $x 
     * @param int $y 
     * @return array
     */
    public abstract function getColorAt( $x, $y );
}
