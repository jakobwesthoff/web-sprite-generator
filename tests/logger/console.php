<?php
/**
 * Console Logger tests
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

class Console extends \PHPUnit_Framework_TestCase 
{
    public static function suite() 
    {
        $suite = new \PHPUnit_Framework_TestSuite( __CLASS__ );
        $suite->setName( "Console" );
        return $suite;
    }

    protected function loggerMock( $handle, $message, $timestamp = true, $stderr = false ) 
    {
        $logger = $this->getMock( 
            'org\\westhoffswelt\\wsgen\\Logger\\Console',
            array( 'writeToConsole' ), 
            array( $timestamp, $stderr )
        );
        $logger->expects( $this->once() )
               ->method( 'writeToConsole' )
               ->with( 
                    $this->equalTo( $handle ),
                    $this->equalTo( $message )
               );

        return $logger;
    }

    protected function getFormattedTimestamp() 
    {
        $date = new \DateTime();
        return $date->format( "[Y-m-d][H:i:s]" );
    }

    public function testMessageWithDefaults() 
    {
        $logger = $this->loggerMock( 
            STDOUT, 
            $this->getFormattedTimestamp() . '[Message] Yipieh!'  
        );
        $logger->log( E_NOTICE, "Yipieh!" );       
    }

    public function testMessageToStderr() 
    {
        $logger = $this->loggerMock( 
            STDERR, 
            $this->getFormattedTimestamp() . '[Warning] Yipieh!',
            true,
            true
        );
        $logger->log( E_WARNING, "Yipieh!" );       
    }

    public function testMessageWithoutTimestamp() 
    {
        $logger = $this->loggerMock( 
            STDOUT, 
            '[Error] Yipieh!',
            false
        );
        $logger->log( E_ERROR, "Yipieh!" );       
    }

    public function testMessageWithoutTimestampToStderr() 
    {
        $logger = $this->loggerMock( 
            STDERR, 
            '[Error] Yipieh!',
            false,
            true
        );
        $logger->log( E_ERROR, "Yipieh!" );       
    }
}
