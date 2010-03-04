<?php
/**
 * wsgen console logger
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
namespace org\westhoffswelt\wsgen\Logger;
use org\westhoffswelt\wsgen;

/**
 * Logger printing messages directly to the console (STDOUT)
 */
class Console
    extends wsgen\Logger
{
    /**
     * Flag to switch between output with and without timestamps. 
     * 
     * @var boolean
     */
    protected $timestamp;

    /**
     * Handle to use for outputting the messages 
     * 
     * @var int
     */
    protected $outputHandle;

    /**
     * Mapping of serverity error levels to strings used for output. 
     * 
     * @var array
     */
    protected $serverityMapping = array( 
        E_NOTICE  => 'Message',
        E_WARNING => 'Warning',
        E_ERROR   => 'Error',
    );

    /**
     * Constructor taking some logging options as optional arguments.
     *
     * If $timestamp is set to boolean true timestamps are printed before every
     * logged message.
     * 
     * If $stderr is boolean true all messages will be printed to stderr
     * instead of stdout.
     * 
     * @param boolean $timestamp 
     * @param bookean $stderr 
     */
    public function __construct( $timestamp = true, $stderr = false ) 
    {       
        $this->timestamp = $timestamp;

        if ( $stderr === true ) 
        {
            $this->outputHandle = STDERR;            
        }
        else 
        {
            $this->outputHandle = STDOUT;
        }
    }

    /**
     * Log the formatted message to the console (stdout/stderr) 
     * 
     * @param int $serverity 
     * @param string $message 
     */
    protected function logFormattedMessage( $serverity, $message ) 
    {
        $line = "";

        if ( $this->timestamp ) 
        {
            $date = new \DateTime();
            $line .= $date->format( '[Y-m-d][H:i:s]' );
        }

        $line .= '[' . $this->serverityMapping[$serverity] . '] ' . $message;

        $this->writeToConsole( $this->outputHandle, $line );
    }

    /**
     * Write message to the output console stream and flush it afterwards.
     *
     * A newline character is automatically prepended to the message
     * 
     * @param int $handle 
     * @param string $message 
     */
    protected function writeToConsole( $handle, $message ) 
    {
        fwrite( $handle, $message . "\n" );
        fflush( $handle );
    }
}
