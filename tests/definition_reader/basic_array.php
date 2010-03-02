<?php
/**
 * BasicArray Definition reader tests
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

namespace org\westhoffswelt\wsgen\tests\DefinitionReader;
use org\westhoffswelt\wsgen;

class BasicArray extends \PHPUnit_Framework_TestCase 
{
    public static function suite() 
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( "BasicArray" );
        return $suite;
    }

    protected function readerFixture( $file ) 
    {
        return new wsgen\DefinitionReader\BasicArray( 
            __DIR__ . '/data/basic_array_' . $file
        );
    }

    public function testValidRead() 
    {
        $reader = $this->readerFixture( 'valid.php' );

        $expected = array( 
            'foo.png' => array( 
                'some.css:rule'
            ),
            'bar.png' => array( 
                '#another .rule',
                '#the > third.rule'
            ),
        );

        $this->assertSame( $expected, $reader->getMappingTable() );
    }

    public function testInvalidRead() 
    {
        $reader = $this->readerFixture( 'invalid.php' );
        
        try 
        {
            $reader->getMappingTable();
            $this->fail( 'Expected RuntimeException because of invalid array inclusion not thrown.' );
        }
        catch( \RuntimeException $e ) 
        {
            /* Expected */
        }
    }

    public function testNonExistentFileRead() 
    {
        $reader = $this->readerFixture( 'some_non_existent_file.php' );
        
        try 
        {
            $reader->getMappingTable();
            $this->fail( 'Expected RuntimeException because of non existent file not thrown.' );
        }
        catch( \RuntimeException $e ) 
        {
            /* Expected */
        }
    }
}
