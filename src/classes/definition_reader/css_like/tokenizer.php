<?php
/**
 * wsgen css like definition tokenizer
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
 * Tokenizer for the css-like definition format.
 */
class Tokenizer
{
    /**
     * Characters identified as whitespaces 
     */
    const WHITESPACE_CHARS = ' \\t';

    /**
     * Charaters used for newlines 
     */
    const NEWLINE_CHARS = '\\r\\n|\\r|\\n'; 

    /**
     * Currently proccessed line 
     * 
     * @var int
     */
    protected $line = 1;

    /**
     * Currently processed character in the actual line. 
     * 
     * @var int
     */
    protected $character = 1;

    /**
     * Regular expressions matching all the different tokens 
     * 
     * @var array
     */
    protected $tokens = array();

    /**
     * Supplied datastream to be tokenized. 
     * 
     * @var string
     */
    protected $data;

    /**
     * Construct a tokenizer for the given input data 
     * 
     * @param string $data 
     * @return void
     */
    public function __construct( $data ) 
    {
        $this->data = $data;

        $this->tokens = array( 
            Token::T_WHITESPACE =>
                '(\\A(?P<value>[' . self::WHITESPACE_CHARS . ']+))S',
            Token::T_NEWLINE =>
                '(\\A(?P<value>' . self::NEWLINE_CHARS . '))S',
            Token::T_CURLY_BRACE_OPEN =>
                '(\\A(?P<value>{))S',
            Token::T_CURLY_BRACE_CLOSE =>
                '(\\A(?P<value>}))S',
            Token::T_COMMA =>
                '(\\A(?P<value>,))S',
            Token::T_IMAGE_FILE =>
                '(\\Aimage:\s*(?P<value>[^;]+);)S',
            Token::T_CSS_RULE =>
                '(\\A(?P<value>[^{]+))S',
        );
    }

    /**
     * Tokenize the given input data and return a stream of tokens
     * 
     * @return array
     * @throws RuntimeException if the file could not be tokenized properly.
     */
    public function tokenize() 
    {
        $inputStream = $this->data;
        $tokenStream = array();

        while( strlen( $inputStream ) > 0 ) 
        {
            foreach( $this->tokens as $type => $pattern )
            {
                // Try to match against every token pattern.
                $matches = array();
                if ( preg_match( $pattern, $inputStream, $matches ) !== 1 ) 
                {
                    continue;
                }

                // A matching pattern has been found
                $token = new Token( 
                    $type, 
                    $this->line, 
                    $this->character
                );

                // Some tokens need a special handling for their value
                if( isset( $matches['value'] ) ) 
                {
                    switch( $type ) 
                    {
                        case Token::T_IMAGE_FILE:
                        case Token::T_CSS_RULE:
                            $token->value = trim( $matches['value'] );
                        break;
                        default:
                            $token->value = $matches['value'];
                    }
                }

                // Write the generated token to our tokenStream and shorten the
                // input stream by all matched characters
                $tokenStream[] = $token;
                $inputStream   = substr( $inputStream, strlen( $matches[0] ) );

                // Update the line and character position information.
                // Some tokens need special handling here
                switch( $type ) 
                {
                    case Token::T_NEWLINE:
                        ++$this->line;
                        $this->character = 1;                       
                    break;
                    default:
                        $this->character += strlen( $matches[0] );
                }

                // We found our token. Just start over and search for the next one.
                continue 2;
            }
            // No pattern matched. Therefore the document is invalid
            throw new \RuntimeException( 'Tokenizing failed: Invalid character sequence at line: ' . $this->line . ' character: ' . $this->character );
        }

        return $tokenStream;
    }
}
