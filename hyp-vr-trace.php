<?php
/*
Plugin Name: Hyp VR Trace
Plugin URI: https://github.com/hyptx/hyp-vr-trace
Description: A php debugging tool for wordpress developers
Version: 1.2.1
Author: Adam J Nowak
Author URI: http://hyperspatial.com
License: GPL2
*/
session_start();
require_once('vr-trace-core.php');

//Init
function vr_init(){
	global $vr_counter;
	if($_SERVER['HTTPS'] == 'on') $vr_url = str_replace('http:','https:',WP_PLUGIN_URL);
	else $vr_url = WP_PLUGIN_URL; 
	define('VR_URL',$vr_url . '/hyp-vr-trace/');
	define('VR_ADMIN_URL',get_bloginfo('wpurl') . '/wp-admin/');
	$vr_action = get_option('vr_action');
	if(!$vr_counter && get_option('vr_switch') == 'on') add_action($vr_action,'vr_print_trace');
}

//Create Menu
function vr_create_menu(){
	add_submenu_page('tools.php','VR Trace','VR Trace','administrator','vr_settings','vr_settings_page');
	add_action('admin_init', 'register_vr_settings');
}

//Register Settings
function register_vr_settings(){	
	$input_field_names = array('vr_switch','vr_action','vr_def_min');
	foreach($input_field_names as $field_name){ register_setting('vr_settings',$field_name); }
}

//Settings Page
function vr_settings_page(){
	if(get_option('vr_switch') == '') update_option('vr_switch','off');
	if(get_option('vr_action') == '') update_option('vr_action','wp_footer');
	if(get_option('vr_def_min') == '') update_option('vr_def_min','min');
	$vr_switch = get_option('vr_switch');
	$vr_action = get_option('vr_action');
	$vr_def_min = get_option('vr_def_min');
	$action_array = array('loop_start','the_post','loop_end','dynamic_sidebar','get_footer','wp_footer','admin_footer','vr_print_trace');
	?>
    <div class="wrap">
        <h2>VR Trace Settings</h2>
        <form id="vr-settings-form" method="post" action="options.php">
        	<img style="position:absolute; top:36px; right:43px; cursor:pointer;" onclick="this.style.display='none';" src="<?php echo VR_URL ?>graphics/logo.png" width="100" alt="VR Trace Logo" />
            <?php settings_fields('vr_settings'); ?>
            <p style="margin-bottom:30px;">
            	<label style="display:block; margin-bottom:3px; font-weight:bold;">Trace Action:</label>
                <select name="vr_action">
					<?php 
                    foreach($action_array as $action){?>
                        <option <?php if($vr_action == $action) echo 'selected="selected"' ?>><?php echo $action ?></option>
                        <?php
                    }?>
                </select>
            </p>
            <p>
                <label style="display:block; margin-bottom:3px; font-weight:bold;" class="input_label">Options:</label>
                <label><input name="vr_switch" type="checkbox" <?php if($vr_switch == 'on') echo 'checked' ?> value="on" />
                <span class="checkbox_text">Enable VR Trace</span></label><br />
            </p>
            <p style="margin-top:-5px;">
                <label><input name="vr_def_min" type="checkbox" <?php if($vr_def_min == 'max') echo 'checked' ?> value="max" />
                <span class="checkbox_text">Maximize on page load</span></label><br />
            </p>
            
            <p style="margin-top:24px;">
                <input name="bpt_submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
        <div style="width:650px">
        <h3 class="vr-h3-under">What Now</h3>
            <p>First off, Don't Panic! - The VR Trace interface displays important server side data to make debugging your php programs a snap. The plugin allows you to send additional data to the interface by calling the vr_trace() function within your program.  This allows you to keep your debugging data out of the main html flow, which prevents your printed data from breaking the layout. So stop using print_r and echo and start using vr_trace instead.</p>
            <p>Take a look at the instructions below -> Enable this awesome weapon -> Defeat The OSG's<br /></p>
            <p> <strong>Warning</strong><br />
            VR Trace only sends data to the browser if an admin user is logged in. Do not change this or allow any non admin users to view your server data. This data can be easily used to attack your server!</p>
            <p>This plugin accounts for many  data types, but sometimes you may notice images or other unknown data within a trace. Sometimes pieces of data can break the css layout of the interface or cause odd browser behavior. To avoid browser issues you should try to prevent tracing huge amounts of data, especially if calling a trace within a 'for' loop.</p><em style="font-size:10px;">(OSG) Overnight server gremlin that breaks your code late at night while you are asleep.</em>
        	<h3 class="vr-h3-under">Step 1 - Setup</h3>
            <p>
            	<strong>Trace Action:</strong><br />
            Use the 'Trace Action' dropdown above to select the WP action that will initiate your trace. So if you want to get front end data use wp_footer. For the admin side use admin_footer.
            </p>
            <p>To create a trace action elsewhere within the execution timeline, select 'vr_print_trace' from the dropdown, then paste this snippet below all the vr_trace() calls. Placing said snippet at the point in the script where the trace data is to be collected.
           	Do not add more than one do_action to your script.<pre class="vr-snippet">&lt;?php do_action('vr_print_trace') ?&gt;</pre>
            <p><em>Ajax Note: When tracing within a dynamically loaded file you will need to load Wordpress in the php file, and may need to force the trace using do_action('vr_print_trace') - One exception is when you have a Wordpress "Loop" running in the dynamic file. It this case the loop related actions will print out your trace.</em></p>
            <h3 class="vr-h3-under">Step 2 - Tracing</h3>
            <p>
            	<strong>Basic Trace:</strong><br />
                Upon adding a trace action from the step above you have access to the superglobal variables via a dropdown menu in the header of the VR Trace interface. These variables are relative to the scope they were loaded in, so the location of your trace action may change the results.
            </p>
            <p>
            	<strong>Single Trace:</strong><br />
            To trace the value of a single item, simply pass that item as the argument for the vr_trace() function.  Below are a few usage examples.
            	<pre class="vr-snippet">&lt;?php vr_trace(array('value1'=>'1','value2'=>'2')) ?&gt;</pre>
            	<pre class="vr-snippet">&lt;?php vr_trace($my_var) ?&gt;</pre>
            	<pre class="vr-snippet">&lt;?php vr_trace($my_obj) ?&gt;</pre>
            </p>
            <p>
            	<strong>Advanced Trace:</strong><br />
                To create an automated trace  insert one of the snippets below into a template file. The trace will display all variables that are available in the present scope. The present scope is relative to where you placed the vr_trace() function call. Function, constant and class traces are also available. Multiple vr_trace() calls are allowed, results stack up in the trace interface.
            	<pre class="vr-snippet">&lt;?php vr_trace(get_defined_vars()) ?&gt;</pre>
            	<pre class="vr-snippet">&lt;?php vr_trace(get_defined_functions()) ?&gt;</pre>
           	 	<pre class="vr-snippet">&lt;?php vr_trace(get_defined_constants()) ?&gt;</pre>
            	<pre class="vr-snippet">&lt;?php vr_trace(get_declared_classes()) ?&gt;</pre>
            </p>
            <p>
            	<strong>Safe Trace:</strong><br />
                To avoid 'undefined function' errors when you deactivate the plugin you should use an if function_exists conditional. Typically I use find and replace to cleanup old vr_trace() calls but if you are using Multisite, you may want to be safe. For Multisite the plugin should be activated on a site by site basis, so to prevent errors on child sites that have not activated VR Trace, you should use the conditional.
            	<pre class="vr-snippet" style="margin-top:10px;">&lt;?php if(function_exists(vr_trace)) vr_trace($var) ?&gt;</pre>
            </p>
            <p>
            	<strong>List Array Argument:</strong><br />
                When viewing advanced traces or single arrays, the default trace behavior is to display the array in it's pre formatted state.  If you want to view the array as a list of values, pass true or 1 as the second argument.
           	<pre class="vr-snippet">&lt;?php vr_trace($my_array,1) ?&gt;</pre>
            	<pre class="vr-snippet">&lt;?php vr_trace(get_defined_vars(),1) ?&gt;</pre>
            </p>
            <h3 class="vr-h3-under">Tips & Shortcuts:</h3>
            <ul>
            	<li><strong>Keyboard</strong> - Use 'ctrl-space' to hide and show the trace window.</li>
            	<li><strong>Settings</strong> - Click on the 'VR Trace' heading to open the settings page.</li>
            	<li><strong>Multiple Trace Actions</strong> -  
            Avoid multiple do_action('vr_print_trace') calls, the plugin not equipped for this.</li>
            	<li><strong>Line Numbers </strong>- The function names  above each trace represent the trace source. The hover tooltip provides the filename and line number of each vr_trace() function call.</li>
            	<li><strong>Security</strong> - VR Trace data is only sent to the browser if a user is logged in and has permission to 'edit plugins(administrator  role). Do not change this or allow any non admin users to view your server data. This data can be easily used to attack your server!</li>
            	<li><strong>Page Loads</strong> - Calling up a trace within the global scope of a plugin or theme may return a ton of data, I don't recommend tracing in this manner.  You can access most of that data using superglobal trace for $GLOBALS. If you choose to minimize the interface while it is set to display $_GLOBALS or is tracing data from the global scope, you may want to disable VR Trace. This is preferred because trace data is still loaded even if the interface is minimized and global traces contain lots of data.</li>
            	<li><strong>Clean Up</strong> - As long as you don't deactivate the plugin you don't need to worry about cleaning up old vr_trace() function calls. If you happen to deactivate and there are still active traces you will get a 'undefined function' error. Your best bet is to clean up well or wrap the traces in a function_exists() function.<br />
        	    </li>
            </ul>
            <h3 class="vr-h3-under">Test Function:</h3>
            <p>Use this test function within one of your files to learn how to use VR Trace
            <pre class="vr-snippet" style="width:463px;">&lt;?php
function vr_trace_test(){
   //Single Variable
   $test_var = "Single Variable Value";

   //Single Array
   $test_array = array('Array Item 1','Array Item 2');

   //Single Object
   $test_object = new ArrayObject();
   $test_object-&gt;append('Object Value 1');
   $test_object-&gt;append('Object Value 2');

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
?&gt;</pre></p>
        </div>
    </div><!-- /.wrap -->
	<?php
}

//Load Plugin First
function load_plugin_first(){
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_search($this_plugin, $active_plugins);
	if($this_plugin_key){
		array_splice($active_plugins,$this_plugin_key,1);
		array_unshift($active_plugins,$this_plugin);
		update_option('active_plugins',$active_plugins);
	}
}

//Enqueue Styles
function vr_add_stylesheet(){
     wp_enqueue_style('vr_styles',VR_URL . 'style.css');
}

//Enqueue Javascript
function vr_enqueue_js(){
     wp_enqueue_script('vr_trace_js',VR_URL . 'scripts.js');
}

/* ~~~~~~~~~~~~ Hooks ~~~~~~~~~~~~ */
add_action('wp_print_styles','vr_add_stylesheet');
add_action('admin_print_styles','vr_add_stylesheet');
add_action('wp_print_scripts','vr_enqueue_js');
add_action('activated_plugin','load_plugin_first');
add_action('admin_menu','vr_create_menu');
?>
