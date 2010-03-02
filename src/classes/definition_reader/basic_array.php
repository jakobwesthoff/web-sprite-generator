<?php
/**
 * wsgen array definition reader
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
namespace org\westhoffswelt\wsgen\DefinitionReader;
use org\westhoffswelt\wsgen;

/**
 * Basic definition reader to read a php array structure from a file.
 * 
 * This is a very basic reader implementation, which takes the needed array
 * structure directly from a file.
 */
class BasicArray
    extends wsgen\DefinitionReader
{
    /**
     * Definition data after it has been read from the file. 
     * 
     * @var array|null
     */
    protected $definition = null;

    /**
     * Provide the mapping table between images and css rule names 
     *
     * A mapping table has the following format:
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
     * @return array
     */
    public function getMappingTable() 
    {
        if ( $this->definition === null ) 
        {
            $this->definition = $this->readFile( $this->inputFile );
        }

        return $this->definition;
    }

    /**
     * Read a given input file and return the array structure defined in it. 
     *
     * Only rudimentary error checking will be done. It is checked that an array
     * is returned from the file. The inner structure of this array is not
     * checked in any way.
     * 
     * @param string $file 
     * @return array
     * @throws RuntimeException if the file is not readable or its inclusion
     *         did not provide us with an array.
     */
    protected function readFile( $file ) 
    {
        if ( !file_exists( $file ) || !is_readable( $file ) ) 
        {
            throw new \RuntimeException( "The provided definition file '$file' is not readable." );            
        }

        $definition = include( $file );

        if ( !is_array( $definition ) ) 
        {
            throw new \RuntimeException( "The definition files inclusion did not provide a definition array." );
        }

        return $definition;
    }
}
