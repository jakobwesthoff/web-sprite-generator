<?php
/**
 * wsgen sprite vertical layout
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
namespace org\westhoffswelt\wsgen\LayoutManager;
use org\westhoffswelt\wsgen;

/**
 * Simple LayoutManager for vertical alignment of the given images
 * 
 * All given images are simply aligned in a vertical strip. One image below
 * another.
 */
class Vertical
    extends wsgen\LayoutManager
{
    /**
     * Storage for already layouted images. 
     * 
     * @var array
     */
    protected $layout = array();

    /**
     * Y-Coordinate position for the next image.
     * 
     * @var int
     */
    protected $currentY = 0;

    /**
     * Current width produced by the layout map 
     * 
     * @var int
     */
    protected $width = 0;

    /**
     * Current height produced by the layout map 
     * 
     * @var int
     */
    protected $height = 0;

    /**
     * Initialize the layouting process
     * 
     * This function is called before any other layouting function is called.
     * 
     * @param int $imageCount 
     */
    public function init( $imageCount ) 
    {
        $this->layout = array();
        $this->currentY = 0;
        $this->width = 0;
        $this->height = 0;
    }
    
    /**
     * Layout the given image.
     * 
     * This method may be called more than once with the same imagepath. This 
     * layout manager takes care of this to ensure every image is only rendered
     * once.
     * 
     * @param string $image 
     * @return void
     */
    public function layoutImage( $image ) 
    {
        if ( array_key_exists( $image, $this->layout ) ) 
        {
            // The image has already been processed.
            return;
        }
        
        $resolution = $this->renderer->retrieveResolution( $image );

        $this->layout[$image] = array( 
            array( 0, $this->currentY ),
            $resolution
        );

        $this->width = max( $this->width, $resolution[0] );
        $this->height += $resolution[1];
        $this->currentY += $resolution[1];
    }

    /**
     * Finish the layouting process and return a image-layout-mapping. 
     *
     * The layout-mapping to be returned has the following structure:
     * <code>
     *   array( 'image/path/name/1.png' => array( 
     *     array( $x, $y ),
     *     array( $width, $height )
     *   ),
     *   ...
     * </code>
     * 
     * After this method has been called it is assumed that the render has
     * completed its rendercycle and the output spriteimage has been written.
     *
     * @return void
     */
    public function finish() 
    {
        // Render the given images to their calculated sprite positions.
        $this->renderer->init( $this->width, $this->height, array( 0, 0, 0, 0 ) );
        foreach( $this->layout as $image => $layout ) 
        {
            $this->renderer->drawImage( $image, $layout[0][0], $layout[0][1] );
        }
        $this->renderer->finish();

        return $this->layout;
    }
}
