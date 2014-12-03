#VR Trace WP debugging tool

The VR Trace interface displays important server side data to make debugging your php programs a snap. The plugin allows you to send additional data to the interface by calling the vr_trace() function within your program. This allows you to keep your debugging data out of the main html flow, which prevents your printed data from breaking the layout. So stop using print_r and echo and start using vr_trace instead.

VR Trace also has a default global set. View all the stored data from $GLOBALS, $_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_SESSION and more from a convenient dropdown.

Right now VR Trace is WordPress only, but I will be working on the PHP only version soon.

####What Now

First off, Don't Panic! - The VR Trace interface displays important server side data to make debugging your php programs a snap. The plugin allows you to send additional data to the interface by calling the ```vr_trace()``` function within your program. This allows you to keep your debugging data out of the main html flow, which prevents your printed data from breaking the layout. So stop using ```print_r``` and echo and start using ```vr_trace``` instead.

Take a look at the instructions below -> Enable this awesome weapon -> Defeat The OSG's

####Warning
VR Trace only sends data to the browser if an admin user is logged in. Do not change this or allow any non admin users to view your server data. This data can be easily used to attack your server!

This plugin accounts for many data types, but sometimes you may notice images or other unknown data within a trace. Sometimes pieces of data can break the css layout of the interface or cause odd browser behavior. To avoid browser issues you should try to prevent tracing huge amounts of data, especially if calling a trace within a 'for' loop.

*(OSG) Overnight server gremlin that breaks your code late at night while you are asleep.*


##Step 1 - Setup

Trace Action:
Use the 'Trace Action' dropdown above to select the WP action that will initiate your trace. So if you want to get front end data use ```wp_footer```. For the admin side use ```admin_footer```.

To create a trace action elsewhere within the execution timeline, select 'vr_print_trace' from the dropdown, then paste this snippet below all the ```vr_trace()``` calls. Placing said snippet at the point in the script where the trace data is to be collected. Do not add more than one do_action to your script.

```php
<?php do_action('vr_print_trace') ?>
```

**Ajax Note:** When tracing within a dynamically loaded file you will need to load Wordpress in the php file, and may need to force the trace using ```do_action('vr_print_trace')``` - One exception is when you have a Wordpress "Loop" running in the dynamic file. It this case the loop related actions will print out your trace.
Step 2 - Tracing

####Basic Trace:
Upon adding a trace action from the step above you have access to the superglobal variables via a dropdown menu in the header of the VR Trace interface. These variables are relative to the scope they were loaded in, so the location of your trace action may change the results.

####Single Trace:
To trace the value of a single item, simply pass that item as the argument for the vr_trace() function. Below are a few usage examples.

```php
<?php vr_trace(array('value1'=>'1','value2'=>'2')) ?>
```

```php
<?php vr_trace($my_var) ?>
```

```php
<?php vr_trace($my_obj) ?>
```

####Advanced Trace:
To create an automated trace insert one of the snippets below into a template file. The trace will display all variables that are available in the present scope. The present scope is relative to where you placed the ```vr_trace()``` function call. Function, constant and class traces are also available. Multiple vr_trace() calls are allowed, results stack up in the trace interface.

```php
<?php vr_trace(get_defined_vars()) ?>
```

```php
<?php vr_trace(get_defined_functions()) ?>
```

```php
<?php vr_trace(get_defined_constants()) ?>
```

```php
<?php vr_trace(get_declared_classes()) ?>
```

####Safe Trace:
To avoid 'undefined function' errors when you deactivate the plugin you should use an if ```function_exists``` conditional. Typically I use find and replace to cleanup old vr_trace() calls but if you are using Multisite, you may want to be safe. For Multisite the plugin should be activated on a site by site basis, so to prevent errors on child sites that have not activated VR Trace, you should use the conditional.

```php
<?php if(function_exists(vr_trace)) vr_trace($var) ?>
```

####List Array Argument:
When viewing advanced traces or single arrays, the default trace behavior is to display the array in it's pre formatted state. If you want to view the array as a list of values, pass true or 1 as the second argument.

```php
<?php vr_trace($my_array,1) ?>
```

```php
<?php vr_trace(get_defined_vars(),1) ?>
```

##Tips & Shortcuts:

Keyboard - Use 'ctrl-space' to hide and show the trace window.
Settings - Click on the 'VR Trace' heading to open the settings page.
Multiple Trace Actions - Avoid multiple do_action('vr_print_trace') calls, the plugin not equipped for this.
Line Numbers - The function names above each trace represent the trace source. The hover tooltip provides the filename and line number of each vr_trace() function call.
Security - VR Trace data is only sent to the browser if a user is logged in and has permission to 'edit plugins(administrator role). Do not change this or allow any non admin users to view your server data. This data can be easily used to attack your server!
Page Loads - Calling up a trace within the global scope of a plugin or theme may return a ton of data, I don't recommend tracing in this manner. You can access most of that data using superglobal trace for $GLOBALS. If you choose to minimize the interface while it is set to display $_GLOBALS or is tracing data from the global scope, you may want to disable VR Trace. This is preferred because trace data is still loaded even if the interface is minimized and global traces contain lots of data.
Clean Up - As long as you don't deactivate the plugin you don't need to worry about cleaning up old ```vr_trace()``` function calls. If you happen to deactivate and there are still active traces you will get a 'undefined function' error. Your best bet is to clean up well or wrap the traces in a ```function_exists()``` function.

####Test Function:

Use this test function within one of your files to learn how to use VR Trace

```php
<?php
function vr_trace_test(){
   //Single Variable
   $test_var = "Single Variable Value";

   //Single Array
   $test_array = array('Array Item 1','Array Item 2');

   //Single Object
   $test_object = new ArrayObject();
   $test_object->append('Object Value 1');
   $test_object->append('Object Value 2');

   //Single Traces
   vr_trace($test_var);
   vr_trace($test_array);
   vr_trace($test_object);
   vr_trace($test_object,1);//Single Trace - List Array on

   //Advanced Traces
   vr_trace(get_defined_vars());
   vr_trace(get_defined_vars(),1);//Advanced Trace - List Array on
}
vr_trace_test();
?>
```

