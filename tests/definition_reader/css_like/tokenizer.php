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

class Tokenizer extends \PHPUnit_Framework_TestCase
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( 'Tokenizer' );
        return $suite;
    }

    protected function tokenizerFixture( $file ) 
    {
        return new wsgen\DefinitionReader\CssLike\Tokenizer( 
            \file_get_contents( __DIR__ . '/data/' . $file )
        );
    }

    public static function validInputAndTokensProvider() 
    {
        return array( 
            array( 'simple_valid.cfg', 'simple_valid.tokens' ),
            array( 'complex_valid.cfg', 'complex_valid.tokens' ),
        );
    }

    /*
    public function testCreateTokens() 
    {
        $file = "complex_valid";

        $tokenizer = $this->tokenizerFixture( $file . ".cfg" );
        \file_put_contents( 
            __DIR__ . '/data/' . $file . '.tokens',
            "<?php\n return " . var_export( 
                $tokenizer->tokenize(),
                true
            ) . ";"
        );
    }
    */

    /**
     * @dataProvider validInputAndTokensProvider 
     */
    public function testValidTokenizing( $inputFile, $tokenFile ) 
    {
        $tokenizer = $this->tokenizerFixture( $inputFile );
        $expected = include( __DIR__ . '/data/' . $tokenFile );

        $this->assertEquals( $expected, $tokenizer->tokenize() );
    }
}
