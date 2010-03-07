<?php
/**
 * wsgen test suite
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
namespace org\westhoffswelt\wsgen\tests\DefinitionReader\CssLike;
use org\westhoffswelt\wsgen;
use org\westhoffswelt\wsgen\DefinitionReader\CssLike\Token as T;

class TokenFilter extends \PHPUnit_Framework_TestCase
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( 'TokenFilter' );
        return $suite;
    }
   
    // Just a little internal helper function to make arrays more readable
    protected static function T( $type ) 
    {
        return new T( $type, 0, 0 );            
    }


    public static function provideTokens() 
    {
        return array( 
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::T_CSS_RULE ),
                array( self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::T_WHITESPACE ),
                array( self::T( T::T_CSS_RULE ), self::T( T::T_NEWLINE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::T_NEWLINE ),
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::T_CURLY_BRACE_OPEN ),
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::ANY ),
                array(),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::T_CSS_RULE, T::T_WHITESPACE ),
                array( self::T( T::T_NEWLINE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::T_WHITESPACE, T::T_NEWLINE ),
                array( self::T( T::T_CSS_RULE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::T_CSS_RULE, T::T_NEWLINE ),
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_CSS_RULE ), self::T( T::T_NEWLINE ) ),
                array( T::T_CSS_RULE, T::T_NEWLINE ),
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_WHITESPACE ), self::T( T::T_NEWLINE ) ),
                array( T::ANY, T::T_NEWLINE ),
                array( self::T( T::T_CSS_RULE ) ),
            ),
            array( 
                array( self::T( T::T_CSS_RULE ), self::T( T::T_CSS_RULE ), self::T( T::T_CSS_RULE ), self::T( T::T_IMAGE_FILE ) ),
                array( T::T_CSS_RULE, T::T_CSS_RULE, T::T_IMAGE_FILE ),
                array( self::T( T::T_CSS_RULE ) ),
            ),
        );
    }   

    protected function getTokenFilterFixture() 
    {
        return new wsgen\DefinitionReader\CssLike\TokenFilter();
    }

    /**
     * @dataProvider provideTokens
     */
    public function testMatching( $tokens, $rule, $expectedResult ) 
    {
        $filter = $this->getTokenFilterFixture();
        $filter->addRule( $rule );
        $result = $filter->filter( $tokens );

        $this->assertEquals( $expectedResult, $result );
    }

    public function testPartialRemovalInside() 
    {
        $filter = $this->getTokenFilterFixture();
        $filter->addRule( 
            array( T::T_CSS_RULE, T::T_CSS_RULE, T::ANY, T::T_CURLY_BRACE_OPEN ),
            array( 1, 2 )
        );
        $result = $filter->filter( 
            array( self::T( T::T_CSS_RULE ), self::T( T::T_CSS_RULE ), self::T( T::T_COMMA ), self::T( T::T_CURLY_BRACE_OPEN ) )
        );

        $this->assertEquals(
            array( self::T( T::T_CSS_RULE ), self::T( T::T_CURLY_BRACE_OPEN ) ),
            $result
        );
    }

    public function testPartialRemovalBorders() 
    {
        $filter = $this->getTokenFilterFixture();
        $filter->addRule( 
            array( T::T_CSS_RULE, T::T_CSS_RULE, T::ANY, T::T_CURLY_BRACE_OPEN ),
            array( 0, 3 )
        );
        $result = $filter->filter( 
            array( self::T( T::T_CSS_RULE ), self::T( T::T_CSS_RULE ), self::T( T::T_COMMA ), self::T( T::T_CURLY_BRACE_OPEN ) )
        );

        $this->assertEquals(
            array( self::T( T::T_CSS_RULE ), self::T( T::T_COMMA ) ),
            $result
        );
    }

    public function testMultipleRules() 
    {
        $filter = $this->getTokenFilterFixture();
        $filter->addRule( array( T::T_CSS_RULE ) );
        $filter->addRule( array( T::T_COMMA ) );
        $result = $filter->filter( 
            array( self::T( T::T_CSS_RULE ), self::T( T::T_CSS_RULE ), self::T( T::T_COMMA ), self::T( T::T_CURLY_BRACE_OPEN ) )
        );

        $this->assertEquals(
            array( self::T( T::T_CURLY_BRACE_OPEN ) ),
            $result
        );
    }

    public function testMultipleOverlappingRules() 
    {
        $filter = $this->getTokenFilterFixture();
        $filter->addRule( array( T::T_CSS_RULE, T::T_CSS_RULE ) );
        $filter->addRule( array( T::T_CSS_RULE, T::T_COMMA ) );
        $result = $filter->filter( 
            array( self::T( T::T_CSS_RULE ), self::T( T::T_CSS_RULE ), self::T( T::T_COMMA ), self::T( T::T_CURLY_BRACE_OPEN ) )
        );

        $this->assertEquals(
            array( self::T( T::T_COMMA ), self::T( T::T_CURLY_BRACE_OPEN ) ),
            $result
        );
    }
}
