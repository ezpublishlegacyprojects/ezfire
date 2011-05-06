ezfire is a debugging extension to use the firephp firefox extension to output
debug data to the firebug firefox extension console.  This allows debugging
output without modifying the output the end user sees in their web browser.

It is comprised of a template operator and a php class to display debug output
from templates or from php code.  A modified debug.php file is also supplied to
display ezpublish debug output.

Several switches exist in module.ini.append.php to determine where the output
is displayed.  It can be displayed just to the firebug console, also to a file
and also to the browser.  The output can also be set to display at different
levels - INFO, WARN, ERROR, LOG.  It also respects the DebugByIP if that is set.

The syntax for the template operator is:

{$node|ezfire("THIS IS THE LABEL","DEBUG",2)}

The syntax for the php class is:
eZFire::debug($variable,"THIS IS THE LABEL","INFO",2);

Check the ezfire module and the eztest.tpl for examples of use.

For questions/suggestions go to http://projects.ez.no/ezfire/forum. For professional support, write to info@leidentech.com