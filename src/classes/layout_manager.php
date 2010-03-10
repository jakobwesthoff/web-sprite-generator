<?php
/**
 * wsgen sprite layout interface
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
 * Abstract interface every LayoutManager needs to implement. 
 * 
 * The LayoutManager is responsible for positioning all the given images on the
 * final sprite canvas. The used positioning magic is arbitrary and can be done
 * freely by any implementation. 
 * 
 * After the LayoutManager determined the layout to use it is responsible for
 * telling the renderer where to render which image exactly.
 */
abstract class LayoutManager 
{
    /**
     * Renderer used to determine image sizes for layouting. 
     * 
     * @var Renderer;
     */
    protected $renderer;

    /**
     * Application wide logger instance 
     * 
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor taking the render to be used as argument.
     * 
     * @param Renderer $renderer 
     */
    public function __construct( $logger, $renderer ) 
    {
        $this->logger   = $logger;
        $this->renderer = $renderer;
    }

    /**
     * Initialize the layouting process
     * 
     * This function is called before any other layouting function is called.
     * 
     * @param int $imageCount 
     */
    public abstract function init( $imageCount );
    
    /**
     * Layout the given image.
     * 
     * No action at all needs to be taken at this point. It only has to be made
     * sure that the given image is available in the final sprite image.
     *
     * This method may be called more than once with the same imagepath. The
     * layout manager should take care of this to ensure every image is only
     * rendered once.
     * 
     * @param MetaImage $image 
     * @return void
     */
    public abstract function layoutImage( MetaImage $image );

    /**
     * Finish the layouting process and return a image-layout-mapping. 
     *
     * After this method has been called it is assumed that the render has
     * completed its rendercycle and the output spriteimage has been written.
     *
     * The layout-mapping to be returned is supposed to have the following
     * structure:
     * <code>
     *   array( 'image/path/name/1.png' => array( 
     *     array( $x, $y ),
     *     array( $width, $height )
     *   ),
     *   ...
     * </code>
     * 
     * @return void
     */
    public abstract function finish();
}
