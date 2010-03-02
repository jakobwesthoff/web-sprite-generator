<?php
/**
 * wsgen base class
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
 * WSGen base class
 * 
 * This class contains all needed methods to handle the basic needs of wsgen,
 * like autoloading classes
 */
class Base 
{
    /**
     * Autoload mapping retrieved from the filestorage autoload file
     * 
     * @var array
     */
    protected static $autoloadMapping = null;
    
    /**
     * Try to autoload the provided classname using filestorage the autoload
     * definition file 
     * 
     * @param mixed $classname 
     * @return bool
     */
    public static function autoload( $classname ) 
    {
        if ( self::$autoloadMapping == null ) 
        {
            // Load the autoload definition file
            self::$autoloadMapping = include( 'config/autoload.php' );
        }

        if ( isset( self::$autoloadMapping[$classname] ) ) 
        {
            // Try to load the mapped class
            include( self::$autoloadMapping[$classname] );
            return true;
        }
        return false;
    }
}
