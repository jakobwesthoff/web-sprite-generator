<?php
/**
 * wsgen meta-image for the gd renderer
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
namespace org\westhoffswelt\wsgen\MetaImage;
use org\westhoffswelt\wsgen;

/**
 * MetaImage for the GD renderer

 * This metaimage uses the GD library to provide all requested meta
 * information. Furthermore is able supply a gd image resource for every of its
 * instances.
 */
class GD 
    extends wsgen\MetaImage
{
    /**
     * GD Resource associated to the provided image 
     * 
     * @var resource
     */
    protected $resource = null;


    /**
     * Construct the object taking the image to provide information for as
     * argument.
     * 
     * @param string $filename 
     */
    public function __construct( $filename ) 
    {
        parent::__construct( $filename );

        $this->resource = $this->loadImage( $filename );
    }

    /**
     * Destroy the underlaying image resource after the object is detroyed. 
     */
    public function __destruct() 
    {
        imagedestroy( $this->resource );
    }

    /**
     * Load the given image and return its GD image handle. 
     * 
     * The image format is identified automatically. If the image format could
     * not be loaded a RuntimeException is thrown.
     *
     * @param string $image 
     * @return resource
     * @throws RuntimeException if the given image could not be loaded.
     */
    protected function loadImage( $image ) 
    {
        $typeToFunction = array( 
            IMAGETYPE_GIF  => 'imagecreatefromgif',
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG  => 'imagecreatefrompng',
            IMAGETYPE_WBMP => 'imagecreatefromwbmp',
            IMAGETYPE_XBM  => 'imagecreatefromxbm',
        );

        // There is no other way to silence gd
        if ( ( $info = @getimagesize( $image ) ) === false ) 
        {
            throw new \RuntimeException( "The image format of '$image' could not be identified." );
        }

        if ( !isset( $typeToFunction[$info[2]] ) ) 
        {
            throw new \RuntimeException( "The image format {$info['mime']} is not supported by this renderer." );
        }

        // GD creation errors can not be silenced any other way :(
        if( !( $handle = @$typeToFunction[$info[2]]( $image ) ) ) 
        {
            throw new \RuntimeException( "The image '$image' could not be opened." );
        }

        return $handle;
    }

    /**
     * Calculate the resolution current image and return it. 
     * 
     * The resultion is returned as tuple:
     * <code>
     *   array( $width, $height )
     * </code>
     * 
     * @return array
     * @throws RuntimeException if the resolution of the given file could not
     *         be determined.
     */
    public function getResolution() 
    {
        $width = imagesx( $this->resource );
        $height = imagesy( $this->resource );

        if ( $width === false || $height === false )
        {
            throw new \RuntimeException( "The resolution of '{$this->filename}' could not be determined." );
        }

        return array( $width, $height );
    }
    /**
     * Provide the color value of the pixel at coordinates $x, $y. 
     * 
     * The returned color is a tuple containing the byte value of each
     * channel:
     * <code>
     *   array( 
     *      $red,
     *      $blue,
     *      $green,
     *      $alpha
     *   );
     * </code>
     * As all values are the actual byte values each of them is represented by
     * an integer between 0 and 255.
     * 
     * @param int $x 
     * @param int $y 
     * @return array
     */

    public function getColorAt( $x, $y ) 
    {
        $color = imagecolorat( $this->resource, $x, $y );

        // The alpha is represented in a 7bit fassion only by gd. Therefore it
        // needs normalization.
        $alpha = round( ( (int)( ( $color >> 24 ) & 0xFF ) / 127 ) * 255);

        return array( 
            ( $color >> 16 ) & 0xFF,
            ( $color >> 8 ) & 0xFF,
            ( $color ) & 0xFF,
            (int)$alpha
        );
    }

    /**
     * Provide the image resource of the loaded image 
     * 
     * @return resource
     */
    public function getResource() 
    {
        return $this->resource;
    }
}
