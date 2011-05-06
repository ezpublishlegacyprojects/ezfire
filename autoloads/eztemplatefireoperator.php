<?php
//
// Definition of eZTemplateFireOperator class
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

/*!
  \class eZTemplateFireOperator eztemplatefireoperator.php
  \ingroup eZTemplateOperators
  \brief Display of variable attributes using operator "ezfire"

  This class allows for displaying template variable attributes. The display
  is recursive and the number of levels can be maximized.

  The operator can take three parameters. The first is the label, the second is the type of display,
  the third is the maximum levels to recurse, if not set it will use the defaultDepth ini setting.

\code
// Example template code

// Display attributes of $myvar
{$myvar|ezfire}
// Display attributes of $myvar with a label
{$tree|ezfire("TREE")}
// Display attributes of $myvar with a label and a debug level
{$tree|ezfire("TREE","INFO")}
// Display attributes of $myvar with a label and a maximum recursion level
{$tree|ezfire("TREE",WARN,2)}
\endcode

*/

class FireOperator
{
    /*!
     Initializes the object with the name $name, default is "ezfire".
    */
    function FireOperator( $name = "ezfire" )
    {
        $this->AttributeName = $name;
        $this->Operators = array( $name );
    }

    /*!
     Returns the template operators.
    */
    function operatorList()
    {
        return array( 'fire', 'ezfire' );
    }

    function operatorTemplateHints()
    {
        return array( $this->AttributeName => array( 'input' => true,
                                                     'output' => false,
                                                     'parameters' => 3 ) );
    }

    /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {

	$fireINI = eZINI::instance( 'ezfire.ini' );
        $depth = $fireINI->variable( 'eZFireSettings', 'DefaultDepth' );
	$depth = $depth ? $depth : 2; //if ini not set set it low
        return array( "label" => array( "type" => "string",
                                              "required" => false,
                                              "default" => "DEBUG" ),
                      "level" => array( "type" => "string",
                                          "required" => false,
                                          "default" => "LOG" ),
                      "depth" => array( "type" => "numerical",
                                        "required" => false,
                                        "default" => $depth )
			);
    }

    /*!
     Display the variable.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $max = $namedParameters["depth"];
        //$as_html = $namedParameters["as_html"];
        $label = $namedParameters["label"];
        $level = $namedParameters["level"];
	if ($level == "TRACE" ) {
        	eZFire::trace($label,$max);
	} elseif ($level == "DUMP" ) {
        	eZFire::dump($operatorValue,$max);
	} else {
        	eZFire::debug($operatorValue,$label,$level,$max);
	}
	//This is to keep the type from going back to the screen.
	$operatorValue=null;
	return;
    }

    /// The array of operators, used for registering operators
    public $Operators;
}

?>
