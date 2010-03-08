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

/**
 * Include all the needed test files/suites
 */
include( __DIR__ . '/token_filter.php' );
include( __DIR__ . '/tokenizer.php' );
include( __DIR__ . '/parser.php' );
include( __DIR__ . '/css_like.php' );

class CssLikeSuite extends \PHPUnit_Framework_TestSuite 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setName( 'CssLike' );

        $this->addTest( TokenFilter::suite() );        
        $this->addTest( Tokenizer::suite() );        
        $this->addTest( Parser::suite() );        
        $this->addTest( CssLike::suite() );        
    }

    public static function suite() 
    {
        return new CssLikeSuite( __CLASS__ );
    }
}
