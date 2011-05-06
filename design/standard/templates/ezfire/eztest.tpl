{foreach $error_messages as $error_message}
<div style="background-color:pink;border: 3px solid red;font-color:red;padding: 5px;">
	{$error_message}
</div>
{/foreach}

This is the test template.  You should be seeing output in the firebug console now.<br>
If you do not see output, make sure:
<ul>
<li>You opened the firebug console before you loaded the page - try reloading</li>
<li>console logging under then firebug net tab is enabled</li>
<li>firephp is enabled (click on the blue bug in the firebug window to see if "FirePHP Enabled" is checked on.)</li>
{*<li>ezfire is an active extension - wait... you wouldn't see this page if it were not</li>*}
<li>FireDebug=enabled is set in the ezfire ezfire.ini</li>
<li>if DebugByIP is set it has the appropriate IP address{if $ip}: {$ip}{/if}</li>
</ul>
{if eq($error_messages|count,0)}

	{"IN THE TEMPLATE"|ezfire('Template Label','INFO')}
	{def $defaultDepth = ezini( 'eZFireSettings', 'DefaultDepth', 'ezfire.ini' )}
	{array( array( 'column 1', 'column 2'), array( 'column 3', 'column 4'))|ezfire("ARRAY TEST","TABLE")}
	{hash( '',array( 'heading 1', 'heading 2'),'row1',array( 'column 1', 'column 2'),'row2',array( 'column 1', 'column 2'))|ezfire("HASH TEST","TABLE")}
	{array( array( 'column 1', 'column 2'), array( 'column 3', 'column 4'))|ezfire("ARRAY TEST","INFO")}

	{literal}{$testnode.node_id|ezfire("NODE ID")}{/literal}<br>
	
	{$testnode.node_id|ezfire("NODE ID")}
	
	This is not a good way of displaying a node tree because the json_parse.js will choke on the amount of data being passed, see <a href="http://www.christophdorn.com/Blog/2010/10/15/tip-firephp-data-volume-filtering/" target="_blank" />here</a> for more information on size constraints and workarounds:<br>
	
	{literal}
	{$testnode.data_map|ezfire("THIS IS THE DATAMAP FOR TESTNODE","INF0")}
	{/literal}<br>
	{$testnode.data_map|ezfire("THIS IS THE DATAMAP FOR TESTNODE","INFO")}
	</p>
	<br/>
	<p>
	If the output is an object it is better to send the output to a table like this and then look by right-clicking the console output and selecting "inspect in DOM Tab" - however make sure you have the "Click for Variable Viewer" option checked otherwise it will open if you mouseover a variable, potentially crashing your browser:<br>
	{literal}
	{$testnode.data_map|ezfire("THIS IS TESTNODE TABLE DEFAULT DEPTH LIMIT","TABLE",{/literal}{$defaultDepth}{literal})}
	{/literal}
	{$testnode|ezfire("THIS IS TESTNODE TABLE DEFAULT DEPTH LIMIT","TABLE",$defaultDepth)}
	</p>
	<br/>
	<p>
	But, if the output is an array then the inspect in DOM Tab will not show good date but, if necessary it is possible to also limit the depth until the output is small enough to deal with:<br>
	{literal}
	{$testnode.data_map|ezfire("THIS IS TESTNODE DATAMAP LIMIT 2","INFO",2)}
	{/literal}<br>
	
	{$testnode.data_map|ezfire("THIS IS TESTNODE DATAMAP LIMIT 2","INFO",2)}
	</p>
	<br/>
	<p>
	I'm really not sure how useful this is:<br>
	{literal}{ezfire("TEMPLATE TRACE","TRACE")}{/literal}<br>
	{ezfire("TEMPLATE TRACE","TRACE")}
	</p>
{/if}
