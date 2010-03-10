<?php
/**
 * wsgen sprite color grouped layout
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
 * LayoutManager grouping images in horizontal lines of the equal colors
 * 
 * This allows for better png compression.
 */
class ColorGroup
    extends wsgen\LayoutManager
{
    /**
     * Classifier used to find equally colored pictures 
     * 
     * @var ColorGroup\Classifier
     */
    protected $classifier = null;

    /**
     * Initialize the layouting process 
     *
     * This function is called before any other layouting function is called.
     * 
     * @param int $imageCount 
     * @return void
     */
    public function init( $imageCount ) 
    {
        $this->classifier = new ColorGroup\Classifier( $this->logger );
    }

    /**
     * Layout the given image.
     * 
     * @param wsgen\MetaImage $image 
     * @return void
     */
    public function layoutImage( wsgen\MetaImage $image ) 
    {
        $this->classifier->classify( $image );
    }

    /**
     * Finish the layouting process and return a image-layout-mapping. 
     *
     * After this method has been called it the render has completed its
     * rendercycle and the output spriteimage has been written.
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
     * @return array
     */
    public function finish() 
    {
        $classification = $this->classifier->getClassification();
        $layout = $this->calculateLayout( $classification );
        list( $width, $height ) = $this->calculateLayoutDimensions( $layout );
        $this->renderer->init( $width, $height, array( 0, 0, 0, 0 ) );
        $this->renderLayout( $layout );
        $this->renderer->finish();

        return $this->convertLayoutToExternalDefinition( $layout );
    }

    /**
     * Calculate a layout of images based on the given classification data. 
     * 
     * @param array $classification 
     * @return array
     */
    protected function calculateLayout( $classification ) 
    {
        $layout = array();
        $currentY = 0;

        foreach( $classification as $row => $class ) 
        {
            $images = $class['images'];
            $this->sortMetaImagesByHeight( $images );
            list( $rowHeight ) = $images[0]->getResolution();
            $currentX = 0;
            foreach( $images as $image ) 
            {
                $resolution = $image->getResolution();

                $layout[$row][] = array( 
                    'coords'     => array( $currentX, $currentY ),
                    'dimensions' => $resolution,
                    'image'      => $image,
                );
                $currentX += $resolution[0];
            }
            $layout[$row]['dimensions'] = array( $currentX, $rowHeight );
            $currentY += $rowHeight;
        }

        return $layout;
    }

    /**
     * Sort a list of MetaImages by their height from the heighest to the
     * lowest. 
     * 
     * The sort is executed in place and the given array is changed.
     * 
     * Array keys are not preserved during the reordering. 
     * 
     * @param array &$images 
     * @return void
     */
    protected function sortMetaImagesByHeight( &$images ) 
    {
        usort( $images, function( $a, $b ) {
            list( , $heightA ) = $a->getResolution();
            list( , $heightB ) = $b->getResolution();
            return $heightB - $heightA;
        });
    }

    /**
     * Calculate the dimensions of a given layout.
     * 
     * @param array $layout 
     * @return array
     */
    protected function calculateLayoutDimensions( $layout ) 
    {
        $width = 0;
        $height = 0;

        foreach( $layout as $row ) 
        {
            $height += $row['dimensions'][1];
            $width = max( $width, $row['dimensions'][0] );
        }

        return array( $width, $height );
    }

    /**
     * Render a given layout to a render surface. 
     * 
     * @param array $layout 
     * @return void
     */
    protected function renderLayout( $layout ) 
    {
        foreach( $layout as $row ) 
        {
            foreach( $row as $key => $image ) 
            {
                if( !is_numeric( $key ) ) 
                {
                    // Skip dimensions meta information.
                    continue;
                }

                $this->renderer->drawImage( $image['image'], $image['coords'][0], $image['coords'][1] );
            }
        }
    }

    /**
     * Convert the internal layout representation into the representation that
     * is supposed to be returned by the finish method to be used by definition
     * writers later on. 
     * 
     * @param array $layout 
     * @return array
     */
    protected function convertLayoutToExternalDefinition( $layout ) 
    {
        $external = array();
        
        foreach( $layout as $row ) 
        {
            foreach( $row as $key => $image ) 
            {
                if( !is_numeric( $key ) ) 
                {
                    // skip meta information about the row
                    continue;
                }

                $external[$image['image']->getFilename()] = array( 
                    $image['coords'],
                    $image['dimensions'],
                );
            }
        }

        return $external;
    }
}
