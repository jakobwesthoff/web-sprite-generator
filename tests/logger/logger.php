<?php
/**
 * Abstract Logger tests
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

namespace org\westhoffswelt\wsgen\tests\Logger;
use org\westhoffswelt\wsgen;

class Logger extends \PHPUnit_Framework_TestCase 
{
    public static function suite() 
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( "(abstract) Logger" );
        return $suite;
    }

    protected function loggerMock( $serverity, $message ) 
    {
        $logger = $this->getMockForAbstractClass( 'org\\westhoffswelt\\wsgen\\Logger' );
        $logger->expects( $this->once() )
               ->method( 'logFormattedMessage' )
               ->with( 
                    $this->equalTo( $serverity ),
                    $this->equalTo( $message )
               );

        return $logger;
    }

    public function testSimpleMessage() 
    {
        $logger = $this->loggerMock( E_NOTICE, "Foobar or baz?" );
        $logger->log( E_NOTICE, "Foobar or baz?" );       
    }

    public function testSimpleFormattedMessage() 
    {
        $logger = $this->loggerMock( E_WARNING, "Foo's are out!" );
        $logger->log( E_WARNING, "%s's are out!", "Foo" );       
    }

    public function testComplexFormattedMessage() 
    {
        $logger = $this->loggerMock( E_ERROR, "21 is only half the truth! It might be written in hexadecimal as: 0x15" );
        $logger->log( E_ERROR, "%d is only %s the truth! It might be written in %s as: 0x%x", 21, "half", "hexadecimal", 21 );       
    }
}
