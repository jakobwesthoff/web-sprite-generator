<?php
/**
 * wsgen css like definition reader
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
 * Definition reader parsing css like definition files.
 * 
 * This reader is capable of reading definition files in a css-like format:
 * <code>
 *   some.css:rule,
 *   more.than:one > rule#is.possible {
 *     /path/to/image/file.png
 *   }
 *   ...
 * </code>
 */
class CssLike
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
     * Read the given input file.
     * 
     * The returned definition structure equals the one requested by the
     * getMappingTable method. 
     * 
     * @param string $file 
     * @return array
     * @throws RuntimeException if the file could not be read.
     */
    protected function readFile( $file ) 
    {
        if ( !file_exists( $file ) || !is_readable( $file ) ) 
        {
            throw new \RuntimeException( "The provided definition file '$file' is not readable." );            
        }

        $this->logger->log( E_NOTICE, "Reading definition file '%s'.", $file );

        $tokenizer = new CssLike\Tokenizer( 
            file_get_contents( $file )
        );

        $parser = new CssLike\Parser( 
            $tokenizer->tokenize()
        );
        
        return $parser->parse();
    }
}
