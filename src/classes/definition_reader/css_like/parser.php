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
        $filter = new TokenFilter();
        $filter->addRule( array( Token::T_NEWLINE ) )
               ->addRule( array( Token::T_WHITESPACE ) );

        $filteredTokens = $filter->filter( $this->tokens );

        $imageRuleMapping = array();

        while( count( $filteredTokens ) > 0 ) 
        {
            $blockMapping = $this->parseCssBlock( $filteredTokens );
            $imageRuleMapping[$blockMapping['image']] = $blockMapping['rules'];
        }

        return $imageRuleMapping;
    }

    /**
     * Parse a full css block and return a image-rule mapping 
     * 
     * @param array $tokens 
     * @return array
     */
    protected function parseCssBlock( &$tokens ) 
    {
        return array( 
            'rules' =>
                $this->parseCssRules( $tokens ),
            'image' => 
                $this->parseInnerBlock( $tokens ),
        );
    }

    /**
     * Parse a list of one or more css-rules 
     * 
     * @param array $tokens 
     * @return array
     */
    protected function parseCssRules( &$tokens ) 
    {       
        $this->lookahead( $tokens, Token::T_CSS_RULE );
        $ruleToken = array_shift( $tokens );
        $rules = array( $ruleToken->value );
        
        while( $this->lookahead( $tokens, Token::T_COMMA, true ) === true ) 
        {
            array_shift( $tokens );
            $this->lookahead( $tokens, Token::T_CSS_RULE );
            $ruleToken = array_shift( $tokens );
            $rules[] = $ruleToken->value;
        }

        return $rules;
    }

    /**
     * Parse an inner block. 
     * 
     * @param array $tokens 
     * @return string
     */
    protected function parseInnerBlock( &$tokens ) 
    {
        $this->lookahead( $tokens, Token::T_CURLY_BRACE_OPEN );
        array_shift( $tokens );
        $this->lookahead( $tokens, Token::T_IMAGE_FILE );
        $imageToken = array_shift( $tokens );
        $this->lookahead( $tokens, Token::T_CURLY_BRACE_CLOSE );
        array_shift( $tokens );

        return $imageToken->value;
    }

    /**
     * Ensure the next token is of the given type.
     * 
     * @param array $tokens 
     * @param int $type 
     * @return bool
     * @throws RuntimeException if the lookahead did not match and the
     *         checkOnly argument is false.
     */
    protected function lookahead( $tokens, $type, $checkOnly = false ) 
    {
        $tokennames = array( 
            Token::T_CSS_RULE => "CSS rule",
            Token::T_CURLY_BRACE_OPEN => "opened curly brace",
            Token::T_CURLY_BRACE_CLOSE => "closed curly brace",
            Token::T_IMAGE_FILE => "image file",
            Token::T_COMMA => "comma",
            Token::T_WHITESPACE => "whitespace",
            Token::T_NEWLINE => "newline",
        );

        if ( $tokens[0]->type !== $type ) 
        {
            if ( $checkOnly === true ) 
            {
                return false;
            }
            else 
            {
                throw new \RuntimeException( 
                    "Parse error: Expected a " . $tokennames[$type] . ' in line ' . $tokens[0]->line . ' at position ' . $tokens[0]->character . ', but found a ' . $tokennames[$tokens[0]->type]
                 );
            }
        }

        return true;
    }
}
