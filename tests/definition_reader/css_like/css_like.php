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

class CssLike extends \PHPUnit_Framework_TestCase
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( 'CssLike' );
        return $suite;
    }

    protected function readerFixture( $file )  
    {
        $logger = $this->getMockForAbstractClass( 
            'org\\westhoffswelt\\wsgen\\Logger'
        );

        return new wsgen\DefinitionReader\CssLike( 
            $logger,
            __DIR__ . '/data/' . $file
        );
    }

    public static function inputAndMappingProvider() 
    {
        return array( 
            array( 'simple_valid.cfg', 'simple_valid.result' ),
            array( 'complex_valid.cfg', 'complex_valid.result' ),
            array( 'multiple_rules.cfg', 'multiple_rules.result' ),
        );
    }

    /**
     * @dataProvider inputAndMappingProvider 
     */
    public function testRead( $inputFile, $resultFile ) 
    {
        $reader = $this->readerFixture( $inputFile );
        $expected = include( __DIR__ . '/data/' . $resultFile );

        $this->assertEquals( $expected, $reader->getMappingTable() );
    }

    public function testNonExistentFile() 
    {
        $reader = $this->readerFixture( __DIR__ . '/data/some/non/existent/file' );
        try 
        {
            $reader->getMappingTable();
            $this->fail( "Expected RuntimeException due to unreadable file not thrown." );
        }
        catch( \RuntimeException $e ) 
        {
            // Expected
        }
    }
}
