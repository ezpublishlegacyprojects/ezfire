<?php
// SOFTWARE NAME: 
// SOFTWARE RELEASE: 1.0.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2007 eZ Systems AS
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
//

@include_once('FirePHPCore/FirePHP.class.php');
if (!class_exists( "FirePHP" ))
   require_once (dirname(__FILE__).'/../lib/FirePHPCore/FirePHP.class.php');

class eZFire
{
    static function debug($output , $label = "eZFire", $level = 1, $depth = 2)
    {
      $moduleINI = eZINI::instance( 'module.ini' );
      if ( $moduleINI->variable( 'ModuleSettings', 'FireDebug' ) == "enabled" && eZFire::debugbyip() ) 
      {
         $debug_level = $moduleINI->variable( 'ModuleSettings', 'DebugLevel' );

         if ( !$debug_level ) $debug_level = 1;
         if ( eZFire::setlevel($level,TRUE) >= eZFire::setlevel($debug_level,TRUE) )  {

            //Send output to the FirePHP console

            $headers = apache_request_headers();

            //if ( FirePHP::detectClientExtension() )
            if (preg_match("/FirePHP/i",$headers['User-Agent']))
            { //No point in sending info if extension is not there
                $firephp = FirePHP::getInstance(true);
		$options = array('maxObjectDepth' => $depth, 'maxArrayDepth' => $depth, 'useNativeJsonEncode' => true, 'includeLineNumbers' => false);
 
		$firephp->setOptions($options);
                $firephp->fb($output,$label,eZFire::setlevel($level));
            }
            //Send output to Screen - there's really no reason to have to have this - just use attribute show
            if ($moduleINI->variable( 'ModuleSettings', 'DumpToScreen' ) == "enabled" ) {
/*
            $headers = "<th align=\"left\">Attribute</th>\n<th align=\"left\">Type</th>\n";
            if ( $show_values )
                $headers .= "<th align=\"left\">Value</th>\n";
            $operatorValue = "<table><tr>$headers</tr>\n$txt</table>\n";
*/
               echo '<div class="debug">';
               if (is_object($output) OR is_array($output)) {
                  echo var_dump($output)." ";
               }else{
                  echo $output." ";
               }
               echo $label.' DEBUG<br></div>';
            }

	    //Send output to file
            if ($moduleINI->variable( 'ModuleSettings', 'DumpToFile' ) == "enabled" ) {
               $debug_file = $moduleINI->variable( 'ModuleSettings', 'DebugFile' );
               if (!$debug_file) $debug_file = "/tmp/ezfire.txt";
               $fp = fopen($debug_file,'a');
               if ($fp) {
                  fwrite($fp,"\n".date('Y-m-d'). time()."\n");
                  if (is_object($output) OR is_array($output)) {
                     ob_start();
                     var_dump($output);
                     $buffer = ob_get_contents();
                     fwrite($fp,$buffer." ");
                     ob_end_clean();
                  }else{
                     fwrite($fp,$output." ");
                  }
                  fwrite($fp,$label.' DEBUG');
                  fclose($fp);
               } /* if fp */
            } /* To file */
         } /* if level >= debug level set in in file */
      } /* if debug_mode true */
    }
    static function setlevel($level,$numflag = FALSE) {
	/* Returns the FirePHP debug level if numflag is false 
           Otherwise returns the debug level number  */
	switch($level) {
	case 1:
	case 'INFO':
		 $firelevel=FirePHP::INFO;$numlevel=1;
		break;
	case 2:
	case 'WARN':
		$firelevel=FirePHP::WARN;$numlevel=2;
		break;
	case 3:
	case 'ERROR':
		$firelevel=FirePHP::ERROR;$numlevel=3;
		break;
	case 5:
	case 'TABLE':
		//$firelevel=FirePHP::TABLE;$numlevel=5;
		//This has to be firephp::log otherwise ezdebug::DEBUG comes out as a table - so only the first character is displayed.  Gotta be numlevel one - otherwise you'll always see the debug messages.
		$firelevel=FirePHP::LOG;$numlevel=1;
		break;
	case 4:
	case 6:
	case 'DUMP':
		$firelevel=FirePHP::DUMP;$numlevel=1;
		break;
	case 'LOG':
	default: $firelevel=FirePHP::LOG;$numlevel=1;
	}
	if ($numflag == TRUE)
		return($numlevel);
	else
		return($firelevel);
    }
    static function debugbyip ()
    {
      //If DebugByIP is enabled, then we only want output going to the valid IPs
      $moduleINI = eZINI::instance( 'site.ini' );
      if ( $moduleINI->variable( 'DebugSettings', 'DebugByIP' ) == "enabled" ) 
        {
            $ipAddress = eZSys::serverVariable( 'REMOTE_ADDR', true );
            if ( $ipAddress )
            {
                foreach( $moduleINI->variable( 'DebugSettings', 'DebugIPList' ) as $itemToMatch )
                {
                    if ( preg_match("/^(([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+))(\/([0-9]+)$|$)/", $itemToMatch, $matches ) )
                    {
                        if ( $matches[6] )
                        {
                            if ( eZDebug::isIPInNet( $ipAddress, $matches[1], $matches[7]))
                            {
                               return TRUE;
                            }
                        }
                        else
                        {
                            if ( $matches[1] == $ipAddress )
                            {
                                return TRUE;
                                break;
                            }
                        }
                    }
                } //IP is not in list if we get here
            } //Enabled but no IP address returned by REMOTE_ADDR 
            return FALSE;
   } else  //Not enabled
	return TRUE;
   }
}
?>
