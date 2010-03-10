<?php
/**
 * wsgen sprite renderer interface
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
 * Abstract interface every sprite renderer has to implement. 
 *
 * Sprite renderers do the actual work of combining single images into one big
 * sprite image. Furthermore they are supposed to provide metainformation about
 * the single images to help the Layouters calculate their final position.
 */
abstract class Renderer 
{
    /**
     * Filepath to the image written after rendering finished. 
     * 
     * @var string
     */
    protected $targetFile;

    /**
     * Application wide logger instance 
     * 
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor taking the target filepath as argument. 
     * 
     * @param string $target 
     */
    public function __construct( $logger, $target ) 
    {
        $this->logger     = $logger;
        $this->targetFile = $target;
    }

    /**
     * Initialization method called before the rendering is supposed to happen. 
     *
     * The background color is specified as a four-tuple:
     * <code>
     *    array( $r, $g, $b, $a )
     * </code>
     * The used values have to be normalized to lie 0 and 1. Alpha values are
     * provided as opacity, where 0 means completely invisble, while 1 means
     * completely visible.
     * 
     * All Renderer methods can assume to be only called after a call to
     * init.
     *
     * @param int $width 
     * @param int $height 
     * @param array $background
     */
    public abstract function init( $width, $height, $background );

    /**
     * Draw an image to the provided coordinates. 
     *
     * @param MetaImage $image 
     * @param int $x 
     * @param int $y 
     * @throws RuntimeException if the image could not be drawn.
     */
    public abstract function drawImage( MetaImage $image, $x, $y );

    /**
     * Finish the rendering and write the target image to disk. 
     *
     * After a call to finish any rendering call will be prepended by new call
     * to init
     */
    public abstract function finish();

    /**
     * Create a MetaImage associated with this renderer.
     * 
     * Every renderer is supposed to implement its own MetaImage, therefore it
     * is supposed to return a new MetaImage of its type for the given filename
     * here.
     * 
     * @param string $filename 
     * @return MetaImage
     */
    public abstract function createMetaImage( $filename );
}
