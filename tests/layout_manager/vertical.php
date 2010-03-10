<?php
/**
 * Vertical LayoutManager tests
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

namespace org\westhoffswelt\wsgen\tests\LayoutManager;
use org\westhoffswelt\wsgen;

class Vertical extends \PHPUnit_Framework_TestCase 
{
    public static function suite() 
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( "Vertical" );
        return $suite;
    }

    protected function rendererMock( $width, $height ) 
    {
        $logger = $this->getMockForAbstractClass( 
            'org\\westhoffswelt\\wsgen\\Logger'
        );

        $m = $this->getMockForAbstractClass( '\\org\\westhoffswelt\\wsgen\\Renderer', array( $logger, 'foobar.png' ) );
        $m->expects( $this->once() )
          ->method( 'init' )
          ->with( 
                $this->equalTo( $width ), 
                $this->equalTo( $height ), 
                $this->equalTo( array( 0, 0, 0, 0 ) )
          );

        $m->expects( $this->once() )
          ->method( 'finish' );

        return $m;
    }

    protected function metaImageMock( $filename, $resolution ) 
    {
        $m = $this->getMockForAbstractClass( '\\org\\westhoffswelt\\wsgen\\MetaImage', array( $filename ) );
        $m->expects( $this->any() )
          ->method( 'getResolution' )
          ->will( $this->returnValue( $resolution ) );
        
        return $m;
    }

    protected function layoutFixture( $renderer ) 
    {
        $logger = $this->getMockForAbstractClass( 
            'org\\westhoffswelt\\wsgen\\Logger'
        );
        
        return new wsgen\LayoutManager\Vertical( 
            $logger,
            $renderer
        );
    }

    public function testOneImageLayout() 
    {
        $layout = $this->layoutFixture( 
            $this->rendererMock( 64, 64 )
        );
        
        $layout->init( 1 );
        $layout->layoutImage( 
            $this->metaImageMock( "foobar.png", array( 64, 64 ) ) 
        );
        $layoutTable = $layout->finish();

        $expected = array( 
            "foobar.png" => array( 
                array( 0, 0 ),
                array( 64, 64 )
            )
        );

        $this->assertSame( $expected, $layoutTable );
    }

    public function testTwoImagesWithSameSize() 
    {
        $layout = $this->layoutFixture( 
            $this->rendererMock( 64, 128 )
        );
        
        $layout->init( 1 );
        $layout->layoutImage( 
            $this->metaImageMock( "foo.png", array( 64, 64 ) ) 
        );
        $layout->layoutImage( 
            $this->metaImageMock( "bar.png", array( 64, 64 ) ) 
        );
        $layoutTable = $layout->finish();

        $expected = array( 
            "foo.png" => array( 
                array( 0, 0 ),
                array( 64, 64 )
            ),
            "bar.png" => array( 
                array( 0, 64 ),
                array( 64, 64 )
            )
        );

        $this->assertSame( $expected, $layoutTable );
    }

    public function testTwoImagesSecondOneIsBigger() 
    {
        $layout = $this->layoutFixture( 
            $this->rendererMock( 96, 128 )
        );
        
        $layout->init( 1 );
        $layout->layoutImage( 
            $this->metaImageMock( "foo.png", array( 64, 64 ) ) 
        );
        $layout->layoutImage( 
            $this->metaImageMock( "bar.png", array( 96, 64 ) ) 
        );
        $layoutTable = $layout->finish();

        $expected = array( 
            "foo.png" => array( 
                array( 0, 0 ),
                array( 64, 64 )
            ),
            "bar.png" => array( 
                array( 0, 64 ),
                array( 96, 64 )
            )
        );

        $this->assertSame( $expected, $layoutTable );
    }

    public function testTwoImagesSecondOneIsSmaller() 
    {
        $layout = $this->layoutFixture( 
            $this->rendererMock( 96, 128 )
        );
        
        $layout->init( 1 );
        $layout->layoutImage( 
            $this->metaImageMock( "foo.png", array( 96, 64 ) ) 
        );
        $layout->layoutImage( 
            $this->metaImageMock( "bar.png", array( 64, 64 ) ) 
        );
        $layoutTable = $layout->finish();

        $expected = array( 
            "foo.png" => array( 
                array( 0, 0 ),
                array( 96, 64 )
            ),
            "bar.png" => array( 
                array( 0, 64 ),
                array( 64, 64 )
            )
        );

        $this->assertSame( $expected, $layoutTable );
    }

    public function testTwoIdenticalImages() 
    {
        $layout = $this->layoutFixture( 
            $this->rendererMock( 
                64, 64,
                array( 
                    array( 64, 64 )
                )
            )
        );
        
        $layout->init( 1 );
        $metaImage = $this->metaImageMock( "foo.png", array( 64, 64 ) );
        $layout->layoutImage( $metaImage );
        $layout->layoutImage( $metaImage );
        $layoutTable = $layout->finish();

        $expected = array( 
            "foo.png" => array( 
                array( 0, 0 ),
                array( 64, 64 )
            )
        );

        $this->assertSame( $expected, $layoutTable );
    }
}
