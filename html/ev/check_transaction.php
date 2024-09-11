<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/functions.php'); 
 
if(isset($_SESSION['IS_APP']))    
{ 
?>
<style>
.sk-btn-charging
{ 
margin-bottom: 120px !important;
}
</style>
<?php
}
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');     
 
$sql = "Select bb.charging_booking_pk 			
from charging_booking as bb  
where bb.user_pk = ".$login_eviot_user_id." and  bb.is_active =  ".PROCESS_FLAG;
// $sql = "Select bb.charging_booking_pk 			
// from charging_booking as bb  
// where bb.transaction_pk = 167" ;
// die($sql);  
$charging_booking_list =    $db->fetch_array($sql) ; 
 // var_dump($charging_booking_list);
if(count($charging_booking_list) > 0)
{
	 foreach($charging_booking_list as  $charging_booking)
	{
		$charging_booking_pk = $charging_booking['charging_booking_pk'];
		echo '<button class="btn    btn-outline-danger sk-btn-charging   "  onclick="GoToUrl(\'charging.php?bid='.$charging_booking_pk.'\')" ><i class="fa fa-bolt"></i></button>';
	}
}