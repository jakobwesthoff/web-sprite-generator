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
 * Basic definition reader to read a directory
 */
class Directory
    extends wsgen\DefinitionReader
{
    /**
     * As this reader uses a directory structure to load the input definition
     * it is impossible to use php://STDIN 
     * 
     * @return bool
     */
    public function isStdinCapable() 
    {
        return false;
    }

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
        $files = array();
        foreach ( glob( $this->inputFile ) as $file )
        {
            // The filename needs to be normalized to fit the requirement to be
            // a valid CSS Selector. Unfortunately they will be harder to read
            // in this case.
            $files[$file] = array( 
                '.' . preg_replace( '([^A-Za-z0-9-]+)', '-', basename( $file ) ) 
            );
        }

        return $files;
    }
}
