<?php
/**
 * wsgen definition reader interface
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
 * Abstract interface every definition reader has to implement. 
 * 
 * Definition readers are like configuration readers. They read some sort of
 * definition file, which contains information which css definitions should be
 * associated with which images later after the process has finished.
 * 
 * Therefore definition readers are supposed to provide a mapping between image
 * filepaths and css definitions. One image may be mapped to multiple css
 * definitions.
 */
abstract class DefinitionReader 
{
    /**
     * Filepath to the definition to read 
     * 
     * @var string
     */
    protected $inputFile;

    /**
     * Application wide logger instance. 
     * 
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor taking the definition filepath to read as an argument. 
     * 
     * @param string $definition 
     */
    public function __construct( $logger, $definition ) 
    {
        $this->logger    = $logger;
        $this->inputFile = $definition;

        if ( !$this->isStdinCapable() && $definition === "php://STDIN" ) 
        {
            throw new \RuntimeException( "The used definition reader does not support input from STDIN." );
        }
    }
    
    /**
     * Returns wheter the reader is capable of accepting php://STDIN as file 
     * input.
     *
     * There may be readers, which aren't capable of handling this for 
     * technical reasons. If it is somehow possible to handle this it should be 
     * done. 
     */
    public abstract function isStdinCapable(); 

    /**
     * Provide the mapping table between images and css definitions 
     *
     * A mapping table is supposed to be of the following format:
     * <code>
     *   array( 
     *     'image/file/1.png' => array( 
     *        '#css .rule',
     *        '#optionally another.css:rule',
     *        ...
     *     ),
     *     ...
     *   )
     * </code>
     *
     * The css rules are only examples. Actually they represent arbitrary
     * string identifiers. However if a css based definition writer is used later
     * on the identifiers should be valid css rules for it to output correct css
     * definitions.
     * 
     * @return array
     */
    public abstract function getMappingTable();
}
