<?php
/**
 * wsgen css like definition token
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
 * Token created during the parsing cycle of a css-like definition file.
 */
class Token
{
    const ANY = 0;
    const T_CSS_RULE = 1;
    const T_CURLY_BRACE_OPEN = 2;
    const T_CURLY_BRACE_CLOSE = 3;
    const T_IMAGE_FILE = 4;
    const T_COMMA = 5;
    const T_WHITESPACE = 6;
    const T_NEWLINE = 7;

    /**
     * Token type 
     * 
     * @var int
     */
    public $type;

    /**
     * Line number the token has been scanned in. 
     * 
     * @var int
     */
    public $line; 

    /**
     * Character position the token has been scanned in. 
     * 
     * @var int
     */
    public $character;

    /**
     * Arbitrary value of the token 
     * 
     * @var mixed
     */
    public $value;

    /**
     * Construct a new token of the given type.
     *
     * Optionally an arbitrary value may be associated with a token.
     * 
     * @param int $type 
     * @param int $line 
     * @param int $character 
     * @param mixed $value 
     * @return void
     */
    public function __construct( $type, $line, $character, $value = null ) 
    {
        $this->type = $type;
        $this->line = $line;
        $this->character = $character;
        $this->value = $value;
    }

    /**
     * Allow import of Tokens exported by using var_export.
     * 
     * @param array $properties 
     * @return Token
     */
    public static function __set_state( $properties ) 
    {
        return new self(
            $properties['type'],
            $properties['line'],
            $properties['character'],
            $properties['value']
        );
    }
}
