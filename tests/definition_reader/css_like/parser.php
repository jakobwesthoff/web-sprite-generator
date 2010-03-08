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

class Parser extends \PHPUnit_Framework_TestCase
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( 'Parser' );
        return $suite;
    }

    protected function parserFixture( $file ) 
    {
        return new wsgen\DefinitionReader\CssLike\Parser( 
            include( __DIR__ . '/data/' . $file )
        );
    }

    public static function validInputAndResultProvider() 
    {
        return array( 
            array( 'simple_valid.tokens', 'simple_valid.result' ),
            array( 'complex_valid.tokens', 'complex_valid.result' ),
            array( 'multiple_rules.tokens', 'multiple_rules.result' ),
        );
    }

    public static function invalidInputAndExceptionProvider() 
    {
        return array( 
            array( 
                'missing_closing_brace.tokens',
                "Parse error: Expected a closed curly brace in line 4 at position 1, but found a CSS rule"
            ),
            array( 
                'missing_semicolon_invalid.tokens',
                "Parse error: Expected a image file in line 2 at position 2, but found a CSS rule"
            ),
            array( 
                'missing_css_rule.tokens',
                "Parse error: Expected a CSS rule in line 1 at position 1, but found a opened curly brace"
            ),
        );
    }
/*
    public function testCreateParseResult() 
    {
        $file = "missing_semicolon_invalid";

        $parser = $this->parserFixture( $file . ".tokens" );
        \file_put_contents( 
            __DIR__ . '/data/' . $file . '.result',
            "<?php\n return " . var_export( 
                $parser->parse(),
                true
            ) . ";"
        );
    }
*/
    /**
     * @dataProvider validInputAndResultProvider 
     */
    public function testValidParse( $tokenFile, $resultFile ) 
    {
        $parser = $this->parserFixture( $tokenFile );
        $expected = include( __DIR__ . '/data/' . $resultFile );

        $this->assertEquals( $expected, $parser->parse() );
    }

    /**
     * @dataProvider invalidInputAndExceptionProvider 
     */
    public function testInvalidParse( $tokenFile, $exception ) 
    {
        $parser = $this->parserFixture( $tokenFile );
        try 
        {
            $parser->parse();
            $this->fail( "$tokenFile was parsed correctly, eventhough it should produce a parse error." );
        }
        catch( \RuntimeException $e ) 
        {
            $this->assertEquals( $exception, $e->getMessage() );
        }
    }
}
