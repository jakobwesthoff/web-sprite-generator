<?php
/**
 * wsgen color group classifier
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
namespace org\westhoffswelt\wsgen\LayoutManager\ColorGroup;
use org\westhoffswelt\wsgen;

/**
 * Group classifier for pictures based on their color.
 * 
 * This classifier a metric to sort the given images into groups of the same
 * color. The grouping can be fine-tuning providing a threshold of color
 * differences allowed for images in one group.
 */
class Classifier
{
    /**
     * Quadratic threshold which mapped colors between pictures may have and
     * still be considered equal colored.
     */
    const INTER_PICTURE_THRESHOLD = 512;

    /**
     * Quadratic threshold which different colors in one picture may have and
     * still be considered the same color.
     */
    const INTER_COLOR_THRESHOLD = 512;

    /**
     * Logger used to inform the application of executed tasks. 
     * 
     * @var wsgen\Logger
     */
    protected $logger;

    /**
     * Images and groups, which have already been classified and sorted. 
     * 
     * @var array
     */
    protected $classification = array();

    /**
     * Construct a new classifier providing a logger. 
     * 
     * @param wsgen\Logger $logger 
     * @return void
     */
    public function __construct( wsgen\Logger $logger ) 
    {
        $this->logger = $logger;
    }

    /**
     * Add a certain image to the classification. 
     * 
     * @param wsgen\MetaImage $image 
     */
    public function classify( wsgen\MetaImage $image )
    {
        $this->logger->log( E_NOTICE, "Classifying image '%s'.", $image->getFilename() );
        $colortable = array();
        list( $width, $height ) = $image->getResolution();

        for ( $y = 0; $y < $height; ++$y ) 
        {
            for( $x = 0; $x < $width; ++$x ) 
            {
                $color = $image->getColorAt( $x, $y );

                if ( $color[3] > 127 ) 
                {
                    // Skip all transparent pixels with a transparency of more
                    // than 50%
                    continue;
                }

                if ( ( $matchIndex = $this->findMatch( $color, $colortable, self::INTER_COLOR_THRESHOLD ) ) === false ) 
                {
                    $colortable[] = array( 
                        'color'   => $color, 
                        'matches' => 1 
                    );
                }
                else 
                {
                    ++$colortable[$matchIndex]['matches'];
                    $this->updateMatch( $matchIndex, $colortable, $color );
                }
            }
        }

        usort( $colortable, function( $a, $b ) {
            return $b['matches'] - $a['matches'];
        });

        $primaryColor = $colortable[0]['color'];

        // Search for an already defined color in the classification, which
        // matches the primary color of our image.
        if ( ( $matchIndex = $this->findMatch( $primaryColor, $this->classification, self::INTER_PICTURE_THRESHOLD ) ) === false ) 
        {
            $this->classification[] = array( 
                'color'   => $primaryColor, 
                'images' => array( $image ) 
            );
        }
        else 
        {
            $this->classification[$matchIndex]['images'][] = $image;
            $this->updateMatch( $matchIndex, $this->classification, $primaryColor );
        }
    }

    /**
     * Return the classification data generated so far. 
     *
     * The classification is provided in the following structure:
     * <code>
     *   array( 
     *     array( 
     *       'color' => array( ... ),
     *       'images' => array( ... )
     *     ),
     *     ...
     *   )
     * </code>
     *
     * @return array
     */
    public function getClassification() 
    {
        return $this->classification;
    }

    /**
     * Try to find a color in the colortable, which matches the given one.
     * 
     * These method uses a certain fuzziness for its operation. Which is
     * indicated by the quadratic distance threshold supplied
     * 
     * If a match could be found its index in the colortable is returned. If no
     * match could be found false is returned.
     * 
     * @param array $color 
     * @param array $colortable 
     * @param mixed $threshold 
     * @return int|false
     */
    protected function findMatch( $color, $colortable, $threshold ) 
    {
        foreach( $colortable as $index => $mappedColor ) 
        {
            $quadraticDistance = $this->calculateQuadraticColorDistance( $color, $mappedColor['color'] );
            if ( $quadraticDistance <= self::INTER_COLOR_THRESHOLD ) 
            {
                return $index;
            }
        }

        return false;
    }

    /**
     * Update a given colortable index using a newly found color.
     * 
     * The new stored color will be the average of the initial color and the
     * new color.
     * 
     * @param index $index 
     * @param array &$colortable 
     * @param array $newColor 
     * @return void
     */
    protected function updateMatch( $index, &$colortable, $newColor ) 
    {
        $oldColor = $colortable[$index]['color'];
        $colortable[$index]['color'] = $this->calculateColorAverage( 
            $oldColor, 
            $newColor
        );        
    }

    /**
     * Calculate the average between two colors 
     * 
     * @param array $color1 
     * @param array $color2 
     * @return array
     */
    protected function calculateColorAverage( $color1, $color2 ) 
    {
        $avgColor = array();

        for( $i = 0; $i < 4; ++$i ) 
        {
            $avgColor[$i] = (int)round( 
                ( $color1[$i] + $color2[$i] ) / 2 
            );
        }
        
        return $avgColor;
    }

    /**
     * Calculate the quadratic distance between to colors 
     * 
     * @param array $color1 
     * @param array $color2 
     * @return int
     */
    protected function calculateQuadraticColorDistance( $color1, $color2 ) 
    {
        $distance = 0;
        for( $i = 0; $i < 4; ++$i ) 
        {
            $difference = $color1[$i] - $color2[$i];
            $distance += ( $difference * $difference );
        }

        return $distance;
    }
}
