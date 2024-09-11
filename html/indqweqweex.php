<?php
require_once('lib/config.php');  
require_once('lib/db_class.php');  
 $sql=" SELECT    * from transaction_start" ;
			   
$data =   $db->fetch_array($sql) ; 
var_dump($sql);
var_dump($data);
die(); 
?>