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
//

require_once( 'autoload.php' );

class eZFire
{ 
    static function trace( $label = "Trace", $depth = 6)
    {
      $fireINI = eZINI::instance( 'ezfire.ini' );
      if ( $fireINI->variable( 'eZFireSettings', 'FireDebug' ) == "enabled" && eZFire::debugbyip() ) 
      {
	$firephp = FirePHP::getInstance(true);
	$options = array('maxObjectDepth' => $depth, 'maxArrayDepth' => $depth, 'useNativeJsonEncode' => true, 'includeLineNumbers' => false);
	$firephp->setOptions($options);
	$firephp->trace($label);
      }
    }
    static function dump( $label = "Dump", $depth = 6)
    {
      $fireINI = eZINI::instance( 'ezfire.ini' );
      if ( $fireINI->variable( 'eZFireSettings', 'FireDebug' ) == "enabled" && eZFire::debugbyip() ) 
      {
	$firephp = FirePHP::getInstance(true);
	$options = array('maxObjectDepth' => $depth, 'maxArrayDepth' => $depth, 'useNativeJsonEncode' => true, 'includeLineNumbers' => false);
	$firephp->setOptions($options);
	$firephp->dump($label);
      }
    }
    
    static function recursiveDump($obj, $maxdepth = 6, $depth = 1)
    {
    	$depth++;
	if ( $depth > ($maxdepth + 1) ) return "Reached max depth";
	if (is_object($obj)) {
		$new[]= get_class($obj);
		$obj = get_object_vars($obj);
		foreach( $obj as $key => $value) {
			$new[$key] = ezfire::recursivedump($value,$maxdepth,$depth);
		}
		return $new;
	  } elseif(is_array($obj)) {
		foreach($obj as $key => $value) {
			$new[$key] = ezfire::recursivedump($value,$maxdepth,$depth);
		}
		return $new;

	} else {
		return $obj; 
	}
    }

    static function debug($output , $label = "eZFire", $level = 1, $depth = NULL )
    {
      $fireINI = eZINI::instance( 'ezfire.ini' );
      if ( $fireINI->variable( 'eZFireSettings', 'FireDebug' ) == "enabled" && eZFire::debugbyip() ) 
      {
	  $debug_level = $fireINI->variable( 'eZFireSettings', 'DebugLevel' );
	  if ( !$depth ) {
		$depth = $fireINI->variable( 'eZFireSettings', 'DefaultDepth' );
	  }
	  if ( !ctype_digit($depth) ) $depth = 6; 
	  if ( !ctype_digit($debug_level) ) $debug_level = 1;
	  
	  if ( eZFire::setlevel($level,TRUE) >= eZFire::setlevel($debug_level,TRUE) )  {
         
	      //Send output to the FirePHP console     
	      $firephp = @FirePHP::getInstance(true);
	      if ( $firephp->detectClientExtension() )
	      { //No point in sending info if extension is not there
		    $options = array('maxObjectDepth' => $depth, 'maxArrayDepth' => $depth, 'useNativeJsonEncode' => true, 'includeLineNumbers' => false);

		    $firephp->setOptions($options);

		    if ($level == "TABLE" ) {
			    if ( is_array( $output ) || is_object( $output) ) {
				    $txt = eZFire::recursiveDump($output,$depth);
			    } else {
				    $txt = $output;
			    }
			    $firephp->table( $label, $txt );
		    } else {
			    $firephp->fb($output,$label,eZFire::setlevel($level));
		    }
	      }
	      
	      //Send output to Screen - there's really no reason to have to have this
	      //Just use attribute show
	      if ($fireINI->variable( 'eZFireSettings', 'DumpToScreen' ) == "enabled" ) {
		  echo '<div class="debug">';
		  if (is_object($output) OR is_array($output)) {
		    echo var_dump($output)." ";
		  }else{
		    echo $output." ";
		  }
		  echo $label.' DEBUG<br></div>';
	      }

	      //Send output to file
	      if ($fireINI->variable( 'eZFireSettings', 'DumpToFile' ) == "enabled" ) {
		  $debug_file = $fireINI->variable( 'eZFireSettings', 'DebugFile' );
		  if (!$debug_file) $debug_file = "/tmp/ezfire.txt";
		  $fp = fopen($debug_file,'a');
		  if ($fp) {
		    fwrite($fp,"\n[".date('Y-m-d h:i:s.u')."]\n");
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
        /* debug notice warning error */
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
		$firelevel=FirePHP::TABLE;$numlevel=5;
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
