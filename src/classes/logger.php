<?php
/**
 * wsgen logger interface
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
namespace org\westhoffswelt\wsgen;

/**
 * Abstract interface every Logger has to implement. 
 * 
 * Loggers are responsible for making information of the application available
 * to the user in an arbitrary form. This ranges from simple tasks like simply
 * outputting the messages to stdout or stderr, to more sophisticated tasks like
 * sending mail or using other forms of communication.
 */
abstract class Logger
{
    /**
     * Log a message with the given serverity and format.
     * 
     * The log method needs to be called with at least two arguments, the
     * severity level as well as a format string. The format string is supplied
     * in the format used by sprintf and may be followed by an arbitrary amount
     * of arguments mapped to the placeholders in this string.
     * 
     * The severity level ist supposed to be one of the php error levels:  
     * - E_ERROR
     * - E_NOTICE
     * - E_WARNING
     *
     * @return void
     * 
     * @param int $serverity 
     * @param string $format 
     * @return void
     */
    public function log( $serverity, $format /*, ... */ ) 
    {
        $formatArguments = array_slice( func_get_args(), 1 );
        $message = call_user_func_array( "sprintf", $formatArguments );

        $this->logFormattedMessage( $serverity, $message );
    }

    /**
     * Log the given message in an arbitrary way. 
     *
     * The severity level ist supplied as one of the following php error
     * levels:  
     * - E_ERROR
     * - E_NOTICE
     * - E_WARNING
     * 
     * @param int $serverity 
     * @param string $message 
     * @return void
     */
    protected abstract function logFormattedMessage( $serverity, $message );
}
