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

require_once('autoload.php');
include_once( 'kernel/common/template.php' );


$tpl = eZTemplate::factory();
$error_messages = array();

/* This is pointless since if the extension isn't loaded we'll never get here anyway. D'oh.
if (!in_array( "ezfire",eZExtension::activeExtensions() )) {
	$error_messages[] = "FirePHP is not in the AutoLoadPathList, did you remember to add it to the site.ini?";
}
*/
if (!class_exists( "eZFire")) {
	$error_messages[] = "FirePHP is not in the AutoLoadPathList, did you remember to run ezpgenerateautoloads.php?";
}
@include_once('FirePHPCore/FirePHP.class.php');
if (!class_exists( "FirePHP", false )) {
	$error_messages[] = "FirePHP core library not found.  Are you sure you have the FirePHP library installed and in your php path?  To install, you can run:<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pear channel-discover pear.firephp.org<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pear install firephp/FirePHPCore</p>See <a href='http://www.firephp.org/HQ/Install.htm'>http://www.firephp.org/HQ/Install.htm</a> for more information.";
} else {
	$FirePHP = new FirePHP;
	if (!$FirePHP->detectClientExtension()) {
		$error_messages[] = "FirePHP not found in the User-Agent string of your browser.  Are you sure you have it installed as a firefox extension and it is enabled?  Go to <a href='http://www.firephp.org'>www.firephp.org</a> for more information.  Note: if this is the first page you are visiting after starting your browser, it could be a false positive so reload this page.";
	}
}

if (!$error_messages) {
	eZFire::debug(basename(__FILE__),"WE ARE HERE");
	$timestamp = date('Y-m-d h:i:s') ;
	eZFire::debug(array("timestamp" => $timestamp));
	eZFire::debug('This has no icon','Plain Message','LOG');
	eZFire::debug('This has a blue "I" icon','Info Message','INFO');
	eZFire::debug('This has a yellow "!" icon','Warn Message','WARN');
	eZFire::debug('This has a red "x" icon','Error Message','ERROR');
	eZFire::debug('The default is the INFO icon','Optional Label');
	eZFire::debug("Don't need a label",'','ERROR');
	eZFire::debug("Don't need a level either");
	
	$table   = array();
	$table[] = array('Col 1 Heading','Col 2 Heading');
	$table[] = array('Row 1 Col 1','Row 1 Col 2');
	$table[] = array('Row 2 Col 1','Row 2 Col 2');
	$table[] = array('Row 3 Col 1','Row 3 Col 2');
	
	eZFire::debug($table,'Table Label','TABLE');
	eZFire::trace("TRACE FROM PHP");
	$fireINI = eZINI::instance( 'ezfire.ini' );
	$DefaultTestNodeID = $fireINI->variable( 'eZFireSettings', 'DefaultTestNode' );
	if (!ctype_digit($DefaultTestNodeID)) $DefaultTestNodeID = 54;
	$testnode = eZContentObjectTreeNode::fetch( $DefaultTestNodeID, false );

	eZFire::debug($testnode,'Table Node '.$DefaultTestNodeID,'TABLE');
	eZFire::debug($testnode,'Table Node '.$DefaultTestNodeID,'INFO');

	$params['AsObject']     = false;
	$params['LoadDataMap']  = true;
	$subtree = eZContentObjectTreeNode::subTreeByNodeID( $params, 2 );

	eZFire::debug($subtree,'Subtree','INFO');

	$identifier = eZContentClass::fetchByIdentifier( 'folder', true );
	eZFire::debug($identifier,'Folder Class Object','TABLE');

	//If you want the output to go to the ez debug file too
        eZFireDebug::writeDebug('THIS IS ezFireDebug write');
	eZFire::debug("END DEBUG FROM PHP CODE - THE REST IS FROM THE TEMPLATE");
}

$tpl->setVariable( 'error_messages',$error_messages );
$tpl->setVariable( 'testnode',$testnode );
$tpl->setVariable( 'ip', $_SERVER['REMOTE_ADDR'] );

$Result = array();
$Result["pagelayout"] = true;
$Result['content'] = $tpl->fetch( 'design:ezfire/eztest.tpl' );

$Result['path'] = array(
                         array( 'url' => '/',
                                'text' => ezpI18n::tr( 'design/ezfire', 'Home' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'design/ezfire', 'ezfire' ) ),
                         array( 'url' => false,
                                'text' =>  ezpI18n::tr( 'design/ezfire', 'firetest' )
 ) );
?>
