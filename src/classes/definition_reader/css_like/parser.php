<?php
/**
 * wsgen css like definition parser
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
namespace org\westhoffswelt\wsgen\DefinitionReader\CssLike;
use org\westhoffswelt\wsgen;

/**
 * Parser of the css-like file format.
 */
class Parser
{
    /**
     * Tokenstream to be parsed.
     * 
     * @var array
     */
    protected $tokens = array();

    /**
     * Construct a parser for the given input Token stream 
     * 
     * @param array $tokens 
     * @return void
     */
    public function __construct( $tokens ) 
    {
        $this->tokens = $tokens;
    }

    /**
     * Parse the given token stream and return a valid definition structure. 
     * 
     * @return array
     * @throws RuntimeException if the token stream could not be parsed
     *         properly.
     */
    public function parse() 
    {
        
    }
}
