<?php

vr_init(); //Wordpress init

//Trace
function vr_trace($args = ''){
	if(!current_user_can('edit_users')) return; //Do not erase
	global $vr_trace_array,$vr_backtrace_array,$vr_counter,$vr_multi;
	$vr_backtrace_array[$vr_counter] = debug_backtrace();
	if(func_num_args() > 1) {
		if(func_get_arg(1)){
			$vr_trace_array[$vr_counter] = $vr_backtrace_array[$vr_counter][0]['args'][0];
			$vr_multi[$vr_counter] = true;
		}
	}
	else if(!is_array($args) && !is_object($args)) $vr_trace_array[$vr_counter]['Single Variable'] = $vr_backtrace_array[$vr_counter][0]['args'][0];
	else $vr_trace_array[$vr_counter] = $vr_backtrace_array[$vr_counter][0]['args'];
	$vr_counter ++;
}

//Print Trace
function vr_print_trace(){
	global $single_trace_switch;
	if(!current_user_can('edit_users')) return; //Do not erase
	if($single_trace_switch):?>
    <div class="vr-error">Error: Multiple do_action('vr_print_trace')</div>
    <?php
	return; endif;  
	$single_trace_switch = true;
	?>
    <div id="vr-trace">
        <div id="vr-minimized">
            <img id="vr-open" class="minimize" src="<?php echo VR_URL ?>graphics/open.png" width="16" height="16" onclick="vrExpandCollapse('vr-trace-win');"/><img id="vr-exit" class="minimize" src="<?php echo VR_URL ?>graphics/close-x.png" width="16" height="16" onclick="vrExpandCollapse('vr-trace');"/><a href="<?php echo VR_ADMIN_URL . 'tools.php?page=vr_settings' ?>" class="vr-brand tshadow" title="Settings">VR Trace</a> 
        </div>
        <div id="vr-trace-win" style="<?php if($_GET['sg_switch']) echo 'display:block;'; else if(get_option('vr_def_min') == 'min') echo 'display:none;' ?>">
            <div id="vr-toolbar">
                <img id="vr-close" class="minimize" src="<?php echo VR_URL ?>graphics/close.png" width="16" height="16" onclick="vrExpandCollapse('vr-trace-win');"/><img id="vr-exit" class="minimize" src="<?php echo VR_URL ?>graphics/close-x.png" width="16" height="16" onclick="vrExpandCollapse('vr-trace');"/>
                <?php vr_superglobal_select() ?>
                <a href="<?php echo VR_ADMIN_URL . 'tools.php?page=vr_settings' ?>" class="vr-brand tshadow" title="Settings">VR Trace</a>
            </div>
            <div id="vr-container">
                <?php vr_superglobal_loop() ?>
                <?php vr_loop() ?>
            </div>
        </div>
    </div>
    <?php
}

//Superglobal Dropdown
function vr_superglobal_select(){
	$superglobal_array = array('No Globals','$GLOBALS','$_SERVER','$_GET','$_POST','$_FILES','$_COOKIE','$_SESSION','$_REQUEST','$_ENV');
	?> 
    <form action="<?php echo VR_URL . 'sg-handler.php' ?>" method="post" enctype="multipart/form-data">
        <select name="vr_superglobal" onchange="this.form.submit();">
            <?php 
            foreach($superglobal_array as $superglobal){
				?>
                <option <?php if($_SESSION['vr_superglobal'] == $superglobal) echo 'selected="selected"' ?>><?php echo $superglobal ?></option>
                <?php
            }?>
        </select>
        <input type="hidden" name="vr_super_submit" value="submit" />
	</form>
	<?php
}

//Superglobal Loop
function vr_superglobal_loop(){
	if($_SESSION['vr_superglobal']) $vr_superglobal = $_SESSION['vr_superglobal'];
	else $vr_superglobal = 'No Globals';
	$vr_sg_array = vr_get_sg_array($vr_superglobal);
	if($vr_superglobal == 'No Globals') return;
	?>
    <div id="vr-superglobals">
        <div class="vr-header">
        	<p class="vr-title vr-super-title" style="cursor:default;"><strong><?php echo $vr_superglobal ?> Superglobal</strong></p>
        </div>
		<?php 
		if(!$vr_sg_array){ echo '<p class="vr-error">Empty Array</p>'; return; } ?>
        <table class="vr-table">
            <?php
			foreach($vr_sg_array as $key => $value){
				if($key == 'GLOBALS') continue; //To much data to display
				//if(!$value) $value = 'Null';
				$orig_value = $value;
				$value = vr_prepare_value($vr_superglobal,$key,$value);
				vr_print_table($key,$value);
				vr_print_object($key,$orig_value);
				$i ++ ;
            }?>
        </table>
    </div>
	<?php
}

//Vr Loop
function vr_loop(){
	global $vr_trace_array,$vr_backtrace_array,$vr_multi;
	if(!$vr_trace_array) return;
	foreach($vr_trace_array as $trace){
		$title = basename($vr_backtrace_array[$vr_counter][0]['file']) . ' on line ' . $vr_backtrace_array[$vr_counter][0]['line'];
		$function = $vr_backtrace_array[$vr_counter][1]['function'];
		if($function) $function .= '()';
		else $function = '/';
		?>
		<div class="trace">
    		<div class="vr-header">
        		<p class="vr-title"title="<?php echo $title ?>"><?php echo $function ?></p>
        		<span class="vr-link vr-link-right" onclick="vrExpandCollapse('vr-backtrace-<?php echo $vr_counter ?>');">Backtrace</span>
    		</div>
    		<div id="vr-backtrace-<?php echo $vr_counter ?>" class="vr-backtrace shadow" style="display:none;">
            	<span class="vr-link vr-link-right" style="margin-right:-1px;" onclick="vrExpandCollapse('vr-backtrace-<?php echo $vr_counter ?>');">Close Backtrace</span>
        		<pre><?php print_r($vr_backtrace_array[$vr_counter]) ?></pre>
                <div style="overflow:hidden; font-size:10px;">
            		<span class="vr-link vr-link-right" style="margin-right:-1px;" onclick="vrExpandCollapse('vr-backtrace-<?php echo $vr_counter ?>');">Close Backtrace</span>
        		</div>
    		</div>
            <table class="vr-table">
                <?php 
                foreach($trace as $key => $value){
					if($key){ if($key == 'GLOBALS' || $key == '_SERVER' || $key == '_COOKIE') continue; } //To much data to display
					//if(!$value) $value = 'Null';
                    else if(is_array($value)){
						if($vr_multi[$vr_counter] != true) $key = 'Single Array';
						$value = '<pre>' . print_r($value,true) . '</pre>';
					}
                    else if(is_object($value)){
						if($vr_multi[$vr_counter] != true) $key = 'Single Object';
						$value = '<pre>' . print_r($value,true) . '</pre>';
					}
                    vr_print_table($key,$value);
				}
                $vr_counter ++ ;
                ?>
        </table>
	</div>
	<?php
	}
}

//Print Table
function vr_print_table($key,$value){
	if($key == 'Single Object' || $key == 'Single Array' || $key == 'Single Variable') $pre_string = '';
	else if(is_int($key)) $pre_string = '';
	else $pre_string = '$';
	?>
	<tr>
    	<td width="154"><div class="vr-key vr-cell"><?php echo $pre_string . trim($key) ?></div></td>
        <td class="spacer"></td>
        <td><div class="vr-value vr-cell"><?php echo $value ?></div></td>
    </tr>
	<?php
}
//Prepare Value
function vr_prepare_value($vr_superglobal,$key,$value){
	$object_type = vr_get_object_type($value);
	if(!$object_type){
		if($key == 'HTTP_COOKIE' || $vr_superglobal == '$_COOKIE') $value = wordwrap($value,40,'<wbr>',1);
		return $value;
	}
	$printed_array = print_r($value,true);
	$value = '<span class="vr-link vr-global-dropdown" onclick="vrExpandColTable(\'' . $key . '\');">View ' . $object_type .'</span>';
	$value .= vr_obj_char_size_span($printed_array);	
	return $value;
}

//Print Object
function vr_print_object($key,$value){
	$object_type  = vr_get_object_type($value);
	if(!$object_type) return;
	$printed_array = print_r($value,true);
	$vr_obj_value = '<span class="vr-link vr-object-link" onclick="vrExpandColTable(\'' . $key . '\');">Close ' . $object_type  . '</span>';
	$vr_obj_value .= '<pre class="vr-global-pre">$' . $key . ' = ';
	$vr_obj_value .= $printed_array;
	$vr_obj_value .= '</pre><span class="vr-link vr-object-link" onclick="vrExpandColTable(\'' . $key . '\');">Close ' . $object_type  . '</span>';
	echo '<tr class="vr-object-trace shadow"><td id="' . $key . '" style="display:none;" colspan="3">' . $vr_obj_value . '</td></tr>';
}

//Get Object Type
function vr_get_object_type($value){
	if(is_array($value)){ $link_text = 'Array'; }
	else if(is_object($value)){ $link_text = 'Object'; }
	return $link_text;
}

//Superglobal Array
function vr_get_sg_array($vr_superglobal){
	switch($vr_superglobal){
		case '$GLOBALS':
		$vr_sg_array = $GLOBALS;
		break;
		case '$_SERVER':
		$vr_sg_array = $_SERVER;
		break;
		case '$_GET':
		$vr_sg_array = $_GET;
		break;
		case '$_POST':
		$vr_sg_array = $_POST;
		break;
		case '$_FILES':
		$vr_sg_array = $_FILES;
		break;
		case '$_COOKIE':
		$vr_sg_array = $_COOKIE;
		break;
		case '$_SESSION':
		$vr_sg_array = $_SESSION;
		break;
		case '$_REQUEST':
		$vr_sg_array = $_REQUEST;
		break;
		case '$_ENV':
		$vr_sg_array = $_ENV;
		break;
		default:
		return;
	}
	return $vr_sg_array;
}

//Empty Object
function vr_obj_char_size_span($ojbect){
	$size = strlen($ojbect);
	if($size <= 10) $size = 'Empty';
	return ' <span class="vr-array-len">' . $size . '</span>';
}?>