<?php
ob_start();
session_start();
// $_SESSION['uspl_user_id']= '';  
session_regenerate_id(true);  
session_destroy();
ob_clean();
header('location:login.php');
?>