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


/*! \file ezinfo.php


  \class ezfire ezinfo.php
  \brief The ezfire class can be used to send debug output to the firefox firebug console without disturbing the html output. 

*/

class ezfireInfo
{
    static function info()
    {
        return array(
            'Name' => "ezfire",
            'Version' => "2.0",
            'Copyright' => "Copyright (c) 2011 Leiden Tech",
            'Info_url' => "http://www.leidentech.com",
            'License' => "GNU General Public License v2.0"
	);
    }
}

?>

