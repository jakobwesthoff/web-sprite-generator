<?php
/**
 * wsgen css background image definition writer
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
namespace org\westhoffswelt\wsgen\DefinitionWriter;
use org\westhoffswelt\wsgen;

/**
 * Definition writer using css background rules to map the sprite image.
 *
 * The writer assumes the identifiers used in the definition process are valid
 * css rules. These rules are associated with background-image properties using
 * the offset parameters to select the correct part of the sprite image for the
 * given rule.
 */
class CssBackground
    extends wsgen\DefinitionWriter
{
    /**
     * File handle of the opened target file while writing is in progress. 
     * 
     * @var resource
     */
    protected $targetHandle = null;

    /**
     * Prefixed sprite filepath. 
     * 
     * @var string
     */
    protected $prefixedSprite = "";

    /**
     * Construct the writer taking the target filename as well a prefix to
     * attach to the sprite image name.
     * 
     * The prefix is an optional argument allowing a certain prefix to
     * be added to the sprite image filepath.
     * 
     * @param wsgen\Logger $logger 
     * @param string $sprite 
     * @param string $target 
     * @param string $prefix 
     * @return void
     */
    public function __construct( $logger, $sprite, $target, $prefix = "" ) 
    {
        $this->prefixedSprite = $prefix . $sprite;

        parent::__construct( $logger, $sprite, $target );
    }
    
    /**
     * Write the definition based on the given mappings as css rules to the
     * given target file. 
     * 
     * The format of the given maps is identical to the ones provided by the
     * DefinitionReader method getMappingTable and the LayoutManager finish
     * method.
     * 
     * Therefore the supposed format of the $imageIdentifierMap is the
     * following:
     * <code>
     *   array( 
     *     'image/file/1.png' => array( 
     *        'identifier',
     *        'another identifier',
     *        ...
     *     ),
     *     ...
     *   )
     * </code>
     * 
     * In most cases the identifiers will be css rules.
     *
     * The $imageLayoutMap is supposed to be of the following format:
     * <code>
     *   array( 'image/path/name/1.png' => array( 
     *     array( $x, $y ),
     *     array( $width, $height )
     *   ),
     *   ...
     * </code>
     *
     * @param array $imageIdentifierMap 
     * @param array $imageLayoutMap 
     * @return void
     * @throws RuntimeException if the writing process could not finish.
     */
    public function writeDefinition( $imageIdentifierMap, $imageLayoutMap ) 
    {
        $this->openTargetFile();

        foreach( $imageLayoutMap as $image => $info ) 
        {
            $this->logger->log( E_NOTICE, "Writing definition for sprite: %s.", basename( $image ) );
            // First we need to isolate the css rules for the given image file.
            $rules = $imageIdentifierMap[$image];
            $this->write(
                implode( ",\n", $rules )
            );
            $this->writeLine( " {" );

            $this->writeLine( 
                sprintf( 
                    "    background: transparent url( '%s' ) -%dpx -%dpx;",
                    $this->prefixedSprite,
                    $info[0][0],
                    $info[0][1]
                )
            );
            $this->writeLine( "}" );
            $this->writeLine( "" );
        }

        $this->closeTargetFile();
    }

    /**
     * Open the target file for writing
     * 
     * The handle of the targetfile is stored in the $targetHandle property.
     * 
     * @return void
     * @throws RuntimeException if file could not be opened.
     */
    protected function openTargetFile() 
    {
        if ( ( $this->targetHandle = fopen( $this->targetFile, "w+" ) ) === false ) 
        {
            throw new \RuntimeException( "Definition target file '{$this->targetFile}' could not be opened for writing." );
        }
    }

    /**
     * Write the given text to the targetFile 
     * 
     * The text is written as it is without a newline attached.
     * 
     * @param string $text 
     */
    protected function write( $text ) 
    {
        fwrite( $this->targetHandle, $text );
    }

    /**
     * Write the given text as line to the targetFile 
     * 
     * A new line character is automatically attached to the provided text.
     * 
     * @param string $text 
     */
    protected function writeLine( $text ) 
    {
        fwrite( $this->targetHandle, $text . "\n" );
    }

    /**
     * Close the handle to the opened target file 
     */
    protected function closeTargetFile() 
    {
        fclose( $this->targetHandle );
    }
}
