<?php
//
// Definition of eZTemplateFireOperator class
//
// Created on: <01-Mar-2002 13:50:09 amos>
//
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 4.1.0
// BUILD VERSION: 21995
// COPYRIGHT NOTICE: Copyright (C) 1999-2008 eZ Systems AS
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

/*!
  \class eZTemplateFireOperator eztemplatefireoperator.php
  \ingroup eZTemplateOperators
  \brief Display of variable attributes using operator "fire"

  This class allows for displaying template variable attributes. The display
  is recursive and the number of levels can be maximized.

  The operator can take three parameters. The first is the maximum number of
  levels to recurse, if blank or omitted the maxium level is infinity.
  The second is the type of display, if set to "text" the output is as pure text
  otherwise as html.
  The third is whether to show variable values or not, default is to not show.

\code
// Example template code

// Display attributes of $myvar
{$myvar|fire}
// Display attributes of $myvar with a label
{$tree|fire("TREE")}
// Display attributes of $myvar with a label and a debug level
{$tree|fire("TREE","INFO")}
// Display attributes of $myvar with a label and a maximum recursion level
{$tree|fire("TREE",WARN,4)}
\endcode

*/
@include_once('FirePHPCore/FirePHP.class.php');
if (!class_exists( "FirePHP" ))
   require_once (dirname(__FILE__).'/../lib/FirePHPCore/FirePHP.class.php');
include_once('extension/ezfire/classes/ezfire.php');
class FireOperator
{
    /*!
     Initializes the object with the name $name, default is "fire".
    */
    function FireOperator( $name = "fire" )
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
        return array( "label" => array( "type" => "string",
                                              "required" => false,
                                              "default" => "DEBUG" ),
                      "level" => array( "type" => "string",
                                          "required" => false,
                                          "default" => "LOG" ),
                      "depth" => array( "type" => "numerical",
                                        "required" => false,
                                        "default" => 4 ) );
    }

    /*!
     Display the variable.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
//eZFire::debug(__FUNCTION__,"WE ARE HERE");
        $max = $namedParameters["depth"];
        //$as_html = $namedParameters["as_html"];
        $label = $namedParameters["label"];
        $level = $namedParameters["level"];

        if ( is_array( $operatorValue ) || is_object( $operatorValue) ) {
//eZFire::debug($operatorValue,"IS ARRAY OR OBJECT");
        	$txt = array();
        	$this->displayVariable( $operatorValue, $max, 0, &$txt );
	        $operatorValue = $txt;
	}

        eZFire::debug($operatorValue,$label,$level,$max);
	//This is to keep the type from going back to the screen.
	$operatorValue=null;
	return;
    }

    /*!
     \private
     Helper function for recursive display of attributes.
     $value is the current variable, $as_html is true if display as html,
     $max is the maximum number of levels, $cur_level the current level
     and $txt is the output text which the function adds to.
    */
    function displayVariable( &$value, $max, $cur_level, &$txt )
    {
//eZFire::debug(__FUNCTION__,"WE ARE HERE");
if ($cur_level != 0)
	//eZFire::debug($cur_level,"CUR LEVEL");

        if ( $max !== false and $cur_level >= $max )
            return;
        if ( is_array( $value ) )
        {
            foreach( $value as $key => $item )
            {
                $type = gettype( $item );
                if ( is_object( $item ) )
                    $type .= "[" . get_class( $item ) . "]";

                if ( is_bool( $item ) )
                    $itemValue = $item ? "true" : "false";
                else if ( is_array( $item ) )
                    $itemValue = 'Array(' . count( $item ) . ')';
                else if ( is_string( $item ) )
                    $itemValue = "'" . $item . "'";
                else if ( is_object( $item ) )
                    $itemValue = method_exists( $item, '__toString' ) ? (string)$item : 'Object';
                else
                    $itemValue = $item;

                $spacing = str_repeat( ">", $cur_level );
		if(!$item)
                   $null = NULL;//$txt[$spacing.$key] = array($type);
		else
		   $txt[$spacing.$key] = array($item);

                $this->displayVariable( $item, $max, $cur_level + 1, $txt );
            } /*foreach */
        }
        else if ( is_object( $value ) )
        {		
            if ( !method_exists( $value, "attributes" ) or !method_exists( $value, "attribute" ) )
                return;
            $attrs = $value->attributes();
            foreach ( $attrs as $key )
            {
                $item = $value->attribute( $key );
                $type = gettype( $item );
                if ( is_object( $item ) )
                    $type .= "[" . get_class( $item ) . "]";

                if ( is_bool( $item ) )
                    $itemValue = $item ? "true" : "false";
                else if ( is_array( $item ) )
                    $itemValue = 'Array(' . count( $item ) . ')';
                else if ( is_numeric( $item ) )
                    $itemValue = $item;
                else if ( is_string( $item ) )
                    $itemValue = "'" . $item . "'";
                else if ( is_object( $item ) )
                    $itemValue = method_exists( $item, '__toString' ) ? (string)$item : 'Object';
                else
                    $itemValue = $item;

                $spacing = str_repeat( ">", $cur_level );
		if(!$itemValue)
                   $null = NULL;//$txt[$spacing.$key] = array($type);
		else
                   $txt[$spacing.$key] = $itemValue;

                $this->displayVariable( $item, $max, $cur_level + 1, $txt );
            }
        }  /* is_array, is_object */
 //echo "IS NOT AN ARRAY AND NOT AN OBJECT<br>";
    }

    /// The array of operators, used for registering operators
    public $Operators;
}

?>
