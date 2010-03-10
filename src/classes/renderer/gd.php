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
        $this->logger->log( E_NOTICE, "Initializing sprite render surface." );

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
     * Draw an image to the provided coordinates. 
     *
     * @param wsgen\MetaImage\GD $image 
     * @param int $x 
     * @param int $y 
     * @throws RuntimeException if the image could not be drawn.
     */
    public function drawImage( wsgen\MetaImage $image, $x, $y ) 
    {
        $this->logger->log( E_NOTICE, "Drawing image '%s' to sprite.", basename( $image->getFilename() ) );
        
        $srcDimensions = $image->getResolution();
        imagecopy( 
            $this->image,
            $image->getResource(),
            $x, $y,
            0, 0,
            $srcDimensions[0], $srcDimensions[1]
        );
    }

    /**
     * Finish the rendering and write the target image to disk. 
     *
     * After a call to finish any rendering call will be prepended by new call
     * to init
     */
    public function finish() 
    {
        $this->logger->log( E_NOTICE, "Writing sprite to disk: %s.", $this->targetFile );

        imagepng( $this->image, $this->targetFile );
        imagedestroy( $this->image );
        $this->image = null;
    }

    /**
     * Create a MetaImage associated with this renderer.
     * 
     * @param string $filename 
     * @return wsgen\MetaImage\GD
     */
    public function createMetaImage( $filename ) 
    {
        return new wsgen\MetaImage\GD( $filename );
    }
}
