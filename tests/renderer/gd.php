<?php
/**
 * GD Renderertests
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

namespace org\westhoffswelt\wsgen\tests\Renderer;
use org\westhoffswelt\wsgen;

class GD extends \PHPUnit_Framework_TestCase 
{
    protected $tmp = null;

    public static function suite() 
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( "GD" );
        return $suite;
    }

    public function setUp() 
    {
        $this->tmp = tempnam( sys_get_temp_dir(), "wsgen_test_" );
    }

    public function tearDown() 
    {
        unlink( $this->tmp );
    }

    protected function rendererFixture() 
    {
        return new wsgen\Renderer\GD( 
            $this->tmp
        );
    }

    public function testResolutionRetrieval() 
    {
        $renderer = $this->rendererFixture();
        
        $expected = array( 128, 64 );
        $this->assertSame( 
            $expected, 
            $renderer->retrieveResolution( __DIR__ . '/data/result_alpha_horizontal.png') 
        );
    }

    public function testBackgroundColorInit() 
    {
        $renderer = $this->rendererFixture();
        $renderer->init( 64, 64, array( 1, 0, 1, 1 ) );
        $renderer->finish();

        $this->assertFileEquals( 
            __DIR__ . '/data/gd_init_background_color.png',
            $this->tmp
        );
    }

    public function testBackgroundAlphaInit() 
    {
        $renderer = $this->rendererFixture();
        $renderer->init( 64, 64, array( 0, 0, 0, 0 ) );
        $renderer->finish();

        $this->assertFileEquals( 
            __DIR__ . '/data/gd_init_background_alpha.png',
            $this->tmp
        );
    }

    public function testDrawImageAlpha() 
    {
        $renderer = $this->rendererFixture();
        $renderer->init( 128, 64, array( 0, 0, 0, 0 ) );
        $renderer->drawImage( __DIR__ . '/data/alpha_image.png', 0, 0 );
        $renderer->drawImage( __DIR__ . '/data/alpha_image.png', 64, 0 );
        $renderer->finish();

        $this->assertFileEquals( 
            __DIR__ . '/data/gd_result_alpha_horizontal.png',
            $this->tmp
        );
    }

    public function testDrawImageColor() 
    {
        $renderer = $this->rendererFixture();
        $renderer->init( 128, 64, array( 0, 0, 0, 1 ) );
        $renderer->drawImage( __DIR__ . '/data/alpha_image.png', 0, 0 );
        $renderer->drawImage( __DIR__ . '/data/alpha_image.png', 64, 0 );
        $renderer->finish();

        $this->assertFileEquals( 
            __DIR__ . '/data/gd_result_black_horizontal.png',
            $this->tmp
        );
    }

    public function testDrawImageSemiTransparent() 
    {
        $renderer = $this->rendererFixture();
        $renderer->init( 128, 64, array( 0, 1, 0, .5 ) );
        $renderer->drawImage( __DIR__ . '/data/alpha_image.png', 0, 0 );
        $renderer->drawImage( __DIR__ . '/data/alpha_image.png', 64, 0 );
        $renderer->finish();

        $this->assertFileEquals( 
            __DIR__ . '/data/gd_result_semi_horizontal.png',
            $this->tmp
        );
    }

    public function testRetrieveResolutionFromNonExistentFile() 
    {
        $renderer = $this->rendererFixture();

        $this->setExpectedException( '\\RuntimeException' );

        $renderer->retrieveResolution('some/non/existent/file' );
    }

    public function testDrawNonExistentImageFile() 
    {
        $renderer = $this->rendererFixture();
        $renderer->init( 64, 64, array( 0, 0, 0, 0 ) );

        $this->setExpectedException( '\\RuntimeException' );
        
        $renderer->drawImage( 'some/non/existent/file', 0, 0 );
    }

    public function testNonExistentBackgroundColor() 
    {
        $renderer = $this->rendererFixture();
        
        $this->setExpectedException( '\\RuntimeException' );
      
        $renderer->init( 64, 64, array( 423, 423, 423, 235 ) );
    }
}
