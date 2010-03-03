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
namespace org\westhoffswelt\wsgen\tests\LayoutManager;

/**
 * Include all the needed test files/suites
 */
include( __DIR__ . '/vertical.php' );

class LayoutManagerSuite extends \PHPUnit_Framework_TestSuite 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setName( 'LayoutManager' );

        $this->addTest( Vertical::suite() );        
    }

    public static function suite() 
    {
        return new LayoutManagerSuite( __CLASS__ );
    }
}
