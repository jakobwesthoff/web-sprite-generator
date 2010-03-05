<?php
/**
 * Css-Background Definition writer tests
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

namespace org\westhoffswelt\wsgen\tests\DefinitionWriter;
use org\westhoffswelt\wsgen;

class CssBackground extends \PHPUnit_Framework_TestCase 
{
    protected $tmp;

    public static function suite() 
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( "CssBackground" );
        return $suite;
    }

    public function setUp() 
    {
        $this->tmp = tempnam( sys_get_temp_dir(), "wsgen_definition_writer_test_" );
    }

    public function tearDown() 
    {
        unlink( $this->tmp );
    }

    protected function writerFixture( $file = null, $removalPrefix = "", $additionPrefix = "" ) 
    {
        if ( $file === null ) 
        {
            $file = $this->tmp;
        }

        $logger = $this->getMockForAbstractClass( 
            'org\\westhoffswelt\\wsgen\\Logger'
        );

        return new wsgen\DefinitionWriter\CssBackground( 
            $logger,
            $file,
            $removalPrefix,
            $additionPrefix
        );
    }

    protected function imageIdentifierMapFixture() 
    {
        return array( 
            "foo/bar.png" => array( 
                ".identifier1",
                ".identifier2"
            ),
            "baz/foo/blub.png" => array( 
                "#identifier3"
            )
        );
    }

    protected function imageLayoutMapFixture() 
    {
        return array( 
            "foo/bar.png" => array( 
                array( 0, 0 ),
                array( 64, 64 )
            ),
            "baz/foo/blub.png" => array( 
                array( 0, 64 ),
                array( 128, 128 )
            )
        );
    }

    public function testSimpleOutput() 
    {
        $writer = $this->writerFixture();
        $writer->writeDefinition( 
            $this->imageIdentifierMapFixture(),
            $this->imageLayoutMapFixture()
        );
        $this->assertFileEquals(
            __DIR__ . '/data/css_background_simple_output.css',
            $this->tmp
        );
    }

    public function testPrefixRemoval() 
    {
        $writer = $this->writerFixture(
            null, 
            'foo/'
        );
        $writer->writeDefinition( 
            $this->imageIdentifierMapFixture(),
            $this->imageLayoutMapFixture()
        );
        $this->assertFileEquals(
            __DIR__ . '/data/css_background_prefix_removal.css',
            $this->tmp
        );
    }

    public function testPrefixAddition() 
    {
        $writer = $this->writerFixture(
            null, 
            "",
            "../images/"
        );
        $writer->writeDefinition( 
            $this->imageIdentifierMapFixture(),
            $this->imageLayoutMapFixture()
        );
        $this->assertFileEquals(
            __DIR__ . '/data/css_background_prefix_addition.css',
            $this->tmp
        );
    }

    public function testPrefixRemovalAndAddition() 
    {
        $writer = $this->writerFixture(
            null, 
            "foo/",
            "../images/"
        );
        $writer->writeDefinition( 
            $this->imageIdentifierMapFixture(),
            $this->imageLayoutMapFixture()
        );
        $this->assertFileEquals(
            __DIR__ . '/data/css_background_prefix_removal_and_addition.css',
            $this->tmp
        );
    }
}
