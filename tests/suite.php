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
namespace org\westhoffswelt\wsgen\tests;

/**
 * Include the basic testcase environment
 */
include( __DIR__ . '/../src/config/config.php' );

/**
 * Load the phpt test extension 
 */
// include( 'PHPUnit/Extensions/PhptTestSuite.php' );

/**
 * Include all the needed test files/suites
 */
include( __DIR__ . '/definition_reader/suite.php' );
include( __DIR__ . '/meta_image/suite.php' );
include( __DIR__ . '/renderer/suite.php' );
include( __DIR__ . '/layout_manager/suite.php' );
include( __DIR__ . '/definition_writer/suite.php' );
include( __DIR__ . '/logger/suite.php' );

class WSGenSuite extends \PHPUnit_Framework_TestSuite 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setName( 'WSGen' );

        $this->addTest( DefinitionReader\DefinitionReaderSuite::suite() );        
        $this->addTest( MetaImage\MetaImageSuite::suite() );        
        $this->addTest( Renderer\RendererSuite::suite() );        
        $this->addTest( LayoutManager\LayoutManagerSuite::suite() );        
        $this->addTest( DefinitionWriter\DefinitionWriterSuite::suite() );        
        $this->addTest( Logger\LoggerSuite::suite() );        
    }

    public static function suite() 
    {
        return new WSGenSuite( __CLASS__ );
    }
}
