<?php
/**
 * wsgen definition writer interface
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
 * Abstract interface every definition writer has to implement. 
 * 
 * Definition writers are writers for any kind of definition file to use the
 * generated sprite. A generated sprite image is mostly useless if there is no
 * information where the images are positioned in it. To solve this problem is
 * the task of a definition writer. It is provided with all the available
 * information for each sprite and is supposed to write a useful representation of
 * this information to the disk.
 */
abstract class DefinitionWriter 
{
    /**
     * Filepath of the targetted output file 
     *
     * @var string
     */
    protected $targetFile;

    /**
     * Constructor taking the target file as argument 
     * 
     * @param string $target 
     */
    public function __construct( $target ) 
    {
        $this->targetFile = $target;
    }

    /**
     * Write the definition based on the given mappings to the target file on
     * disk. 
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
    public abstract function writeDefinition( $imageIdentifierMap, $imageLayoutMap );
}
