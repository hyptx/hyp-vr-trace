<?php 
session_start();
if(isset($_POST['vr_super_submit'])) $_SESSION['vr_superglobal'] = $_POST['vr_superglobal'];
$redirect_add = $_SERVER['HTTP_REFERER'];

if(strpos($redirect_add,'sg_switch=1')) header('Location: ' . $redirect_add);
else{
	if(strpos($redirect_add,'?')){ header('Location: ' . $redirect_add . '&sg_switch=1'); }
	else header('Location: ' . $redirect_add . '?sg_switch=1');
}
?>