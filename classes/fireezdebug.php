<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: ezfire
// SOFTWARE RELEASE: 2.0.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2011 Leiden Tech
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##

class eZFireDebug extends eZDebug
{
   /*!
      \private
      Writes a debug log message.
    */

    static function writeDebug( $string, $label = "", $backgroundClass = "" )
    {
        $alwaysLog = eZDebug::alwaysLogMessage( self::LEVEL_DEBUG );
        $enabled = eZDebug::isDebugEnabled();
        if ( !$alwaysLog and !$enabled )
            return;

        $show = eZDebug::showMessage( self::SHOW_DEBUG );
        if ( !$alwaysLog and !$show )
            return;

        if ( is_object( $string ) || is_array( $string ) )
            $string = eZDebug::dumpVariable( $string );

        $GLOBALS['eZDebugDebug'] = true;
        if ( !isset( $GLOBALS['eZDebugDebugCount'] ) )
            $GLOBALS['eZDebugDebugCount'] = 0;
        ++$GLOBALS['eZDebugDebugCount'];

        $debug = eZDebug::instance();
        if ( $debug->HandleType == self::HANDLE_TO_PHP )
        {
            // If we get here only because of $alwaysLog we should not trigger a PHP error
            if ( $enabled and $show )
            {
                if ( $label )
                    $string = "$label: $string";
                trigger_error( $string, E_USER_NOTICE );
            }
        }
        else
        {
            $debug->write( $string, self::LEVEL_DEBUG, $label, $backgroundClass, $alwaysLog );
            eZFire::debug( __FUNCTION__,"Get here" );
            eZFire::debug( $string, $label );
        }
    }
}
