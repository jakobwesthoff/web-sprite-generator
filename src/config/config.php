<?php

/**
 * Determine and set the wsgen base path
 */
define( 'WSGEN_BASE_PATH', realpath( __DIR__ . '/..' ) );

/**
 * Add the application base path to the include_path
 */
ini_set( 'include_path', 
    ini_get( 'include_path' )
  . PATH_SEPARATOR
  . __DIR__ . '/..'
);

/**
 * Include the wsgen base class
 */
 include( 'classes/base.php' );

/**
 * Magic autoload function dispatching to the autoloader class
 * 
 * @param string $name 
 * @return void
 */
function __autoload( $name )
{
    \org\westhoffswelt\wsgen\Base::autoload( $name );
}

