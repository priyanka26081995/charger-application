<?php
ob_start();
session_start();
$_SESSION['login_eviot_user_id']= '';  
$_SESSION['login_eviot_access_token']= '';  
$_SESSION['login_eviot_company'] = 1; //COMPANY
if(isset($_REQUEST['cid'])      )    
{  
	$cid = trim($_REQUEST['cid']); 
	$_SESSION['login_eviot_company'] = $cid; 
}
setcookie('COOKIE_USER_ID', '', time()-1000);
$_COOKIE['COOKIE_USER_ID'] = '';
setcookie('COOKIE_TOKEN', '', time()-1000);
$_COOKIE['COOKIE_TOKEN'] = '';

session_regenerate_id(true);  
session_destroy();
ob_clean();
header('location:login.php?cid='.$cid);
exit();
?>