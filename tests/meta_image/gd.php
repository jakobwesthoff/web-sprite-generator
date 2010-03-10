<?php
/**
 * GD metaimage tests
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

namespace org\westhoffswelt\wsgen\tests\MetaImage;
use org\westhoffswelt\wsgen;

class GD extends \PHPUnit_Framework_TestCase 
{
    public static function suite() 
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( "GD" );
        return $suite;
    }

    public static function imageListProvider() 
    {
        return array( 
            array( 'image.png' ),
            array( 'image.gif' ),
            array( 'image.jpg' ),
            array( 'image.wbmp' ),
            array( 'image.xbm' ),
        );
    }

    protected function metaImageFixture( $filename ) 
    {
        return new wsgen\MetaImage\GD( 
            __DIR__ . '/data/' . $filename
        );
    }

    /**
     * @dataProvider imageListProvider 
     */
    public function testResolutionRetrieval( $imagefile ) 
    {
        $image = $this->metaImageFixture( $imagefile );
        
        $expected = array( 128, 64 );
        $this->assertSame( 
            $expected, 
            $image->getResolution() 
        );
    }

    public function testRetrieveResolutionFromNonExistentFile() 
    {
        $this->setExpectedException( '\\RuntimeException' );
        $image = $this->metaImageFixture('some/non/existent/file');
    }

    public function testNotSupportedFormat() 
    {
        $this->setExpectedException( '\\RuntimeException' );
        $image = $this->metaImageFixture('not_supported_format.bmp');
    }
}
