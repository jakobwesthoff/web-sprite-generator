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
 * Provide all necessary means to filter a given tokenstream to eliminate 
 * certain Tokens or token combinations from the stream.
 */
class TokenFilter 
{
    /**
     * Rules to use for filtering 
     * 
     * @var array
     */
    protected $filterRules = array();
    
    /**
     * Add a rule to filter from the tokenstream.
     *
     * The rule is defined by an array of token types, which have to match in a 
     * row. If such a match is found the tokens which match the given removal 
     * indices are removed.
     *
     * If no removal indices are provided all of the matched tokens will be 
     * removed.
     *
     * Example:
     * <code>
     *   $rule = array( 
     *      Token::T_IF,
     *      Token::T_WHITESPACE,
     *      Token::ANY,
     *      Token::T_COMMA,
     *   );
     *
     *   $removal = array( 1, 2 );
     * </code>
     *
     * The provided example would scan for the sequence of an if, a whitespace 
     * followed by any token, followed by a comma. If such a sequence is found, 
     * the whitespace as well as the wildcard token is removed from the 
     * tokenstream.
     * 
     * @param array $rule 
     * @return Tokenfilter (fluent interface)
     */
    public function addRule( array $rule, array $removal = null ) 
    {
        $this->filterRules[] = array( 
            'rule'    => $rule,
            'removal' => $removal,
        );

        return $this;
    }

    /**
     * Filter a tokenstream using the currently added rules and return the 
     * filtered version of the stream.
     * 
     * @param array $tokens 
     * @return array
     */
    public function filter( array $tokens ) 
    {
        $filteredTokens = array();

        for( $i=0; $i < count( $tokens ); ++$i ) 
        {
            // Try to match against every rule
            foreach( $this->filterRules as $rule ) 
            {
                if ( $this->matchRule( $tokens, $rule['rule'], $i ) ) 
                {
                    // We found a rule which matched completely.
                           
                    // Only add the filtered version of the processed tokens
                    // This might be none if all should be discarded.                                                 
                    $this->addFilteredTokens( $filteredTokens, $tokens, $rule, $i );

                    // Jump the amount of processed matches into the stream
                    // The one is substracted because it will be added by the 
                    // next for loop cycle automatically
                    $i += count( $rule['rule'] ) - 1;
                    
                    // All further match checks will be aborted for this 
                    // token. Jump to the next token.
                    continue 2;
                }
            }
            // None of the rules matches, therefore the current token should be 
            // conserved.
            $filteredTokens[] = $tokens[$i];
        }

        return $filteredTokens;
    }

    /**
     * Match a rule agains a given Tokenstream considering the currently active 
     * index
     * 
     * @param Token $token 
     * @param int $rule 
     * @param int $index 
     * @return bool
     */
    protected function matchRule( $tokens, $rule, $index ) 
    {
        for( $i=0; $i < count( $rule ); ++$i ) 
        {
            if ( ( $rule[$i] !== Token::ANY )
              && ( $tokens[$index + $i]->type !== $rule[$i] )
            ) 
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Add the amount of scanned tokens to the stream which is filtered by the 
     * removal rule first
     *
     * In case all elements should be filtered none of them will be added.
     *
     * @param array &$target 
     * @param array $tokens
     * @param array $rule
     * @param int $index
     * @return void
     */
    protected function addFilteredTokens( &$target, $tokens, $rule, $index ) 
    {
        if ( $rule['removal'] === null ) 
        {
            // All tokens should be removed. Just return as not filter 
            // operation is needed.
            return;
        }
        
        for( $i=0; $i < count( $rule['rule'] ); ++$i ) 
        {
            if ( in_array( $i, $rule['removal'] ) === false ) 
            {
                $target[] = $tokens[$index + $i];
            }
        }
    }
}
