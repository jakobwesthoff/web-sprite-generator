<?php
/**
 * wsgen sprite gd renderer
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
namespace org\westhoffswelt\wsgen\Renderer;
use org\westhoffswelt\wsgen;

/**
 * Sprite renderer using the GD lib
 *
 * This renderer might not be the fastest or the most accurate, but using the
 * GD library makes it usable with nearly any php environment out of the box.
 */
class GD 
    extends wsgen\Renderer
{
    /**
     * GD Image handle used for drawing 
     * 
     * @var resource
     */
    protected $image = null;

    /**
     * Allocate a GD color based on the given 4-tuple color value as it is
     * defined by the init method as background color.
     * 
     * @param resource $image 
     * @param array $color 
     * @return resource
     * @throws RuntimeException if color allocation fails
     */
    protected function allocateColor( $image, $color ) 
    {
        $r = round( $color[0] * 255 );
        $g = round( $color[1] * 255 );
        $b = round( $color[2] * 255 );
        $a = round( ( 1.0 - $color[3] ) * 127 );

        if ( ( $color = imagecolorallocatealpha( $image, $r, $g, $b, $a ) ) === false ) 
        {
            throw new \RuntimeException( "GD color allocation failed." );
        }

        return $color;
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
            IMAGETYPE_XBM  => 'imagecreatefromwxb',
        );

        if ( ( $info = getimagesize( $image ) ) === false ) 
        {
            throw new \RuntimeException( "The image format of '$image' could not be identified." );
        }

        // GD creation errors can not be silenced any other way :(
        if( !( $handle = @$typeToFunction[$info[2]]( $image ) ) ) 
        {
            throw new \RuntimeException( "The image '$image' could not be opened." );
        }

        return $handle;
    }

    /**
     * Calculate the resolution of the given image file and return it. 
     * 
     * The resultion is returned as tuple:
     * <code>
     *   array( $width, $height )
     * </code>
     * 
     * This method may be called before the init method has been invoked.
     * 
     * @param string $file 
     * @return array
     * @throws RuntimeException if the resolution of the given file could not
     *         be determined.
     */
    public function retrieveResolution( $file ) 
    {
        $info = getimagesize( $file );
        if ( $info === false ) 
        {
            throw new \RuntimeException( "The resolution of the given image '$file' could not be determined." );
        }

        return array_slice( $info, 0, 2 );
    }

    /**
     * Initialization method called before the rendering does happen. 
     *
     * The background color is specified as a four-tuple:
     * <code>
     *    array( $r, $g, $b, $a )
     * </code>
     * The used values have to be normalized to lie 0 and 1. Alpha values are
     * provided as opacity, where 0 means completely invisble, while 1 means
     * completely visible.
     * 
     * Mostly all Renderer methods can assume to be only called after a call to
     * init. Methods which should not assume this fact are marked as such.
     *
     * @param int $width 
     * @param int $height 
     * @param array $background
     * @throws RuntimeException if the image could not be created.
     */
    public function init( $width, $height, $background ) 
    {
        if( ( $this->image = imagecreatetruecolor( $width, $height ) ) === false )
        {
            throw new \RuntimeException( "The GD image could not be initialized for drawing." );
        }

        // Ensure a true alpha channel is stored
        imagealphablending( $this->image, true );
        imagesavealpha( $this->image, true );

        $bg = $this->allocateColor( $this->image, $background );

        imagefill( $this->image, 0, 0, $bg );
    }

    /**
     * Draw the image stored at the given filepath to the provided coordinates. 
     *
     * @param string $image 
     * @param int $x 
     * @param int $y 
     * @throws RuntimeException if the image could not be drawn.
     */
    public function drawImage( $image, $x, $y ) 
    {
        $src = $this->loadImage( $image );
        imagecopy( 
            $this->image,
            $src,
            $x, $y,
            0, 0,
            imagesx( $src ), imagesy( $src )
        );
        imagedestroy( $src );
    }

    /**
     * Finish the rendering and write the target image to disk. 
     *
     * After a call to finish any rendering call will be prepended by new call
     * to init
     */
    public function finish() 
    {
        imagepng( $this->image, $this->targetFile );
        imagedestroy( $this->image );
        $this->image = null;
    }
}
