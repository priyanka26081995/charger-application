<?php
require_once('../dist/lib/config.php'); 
$msg = "No data Posted. Please try again..."; 

if(!isset($_REQUEST['bid'])        )  
{
	ob_clean();
	header('location:./');
	exit();
}
$url = "index.php";
$charging_booking_pk = $_REQUEST['bid'];
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "Pending Charging";
$sql = "Select * from charging_booking   	where   user_pk = ".$login_eviot_user_id." and    charging_booking_pk =  ".$charging_booking_pk ;
		 // die($sql);  
$charging_booking =   $db->query_first($sql) ; 
 // var_dump($charging_booking);
if( is_array($charging_booking)   && !empty($charging_booking['charging_booking_pk']))
{ 
	$amount = $charging_booking['charging_booked_amount'];
	$nru = array();    	
	$nru['end_time'] = CURRENT_DB_DATE;  	
	$nru['is_active'] = REJECT_FLAG;   
	$db->update('charging_booking', $nru, "charging_booking_pk  = ".$charging_booking_pk);


	$sql = "Select charging_booking_pk from wallet   	where  description = '".CANCELLATION_REFUND."' and   charging_booking_pk =  ".$charging_booking_pk ;
			 // die($sql);  
	$charging_booking =   $db->query_first($sql) ; 
	 // var_dump($charging_booking);
	if( is_array($charging_booking)   && !empty($charging_booking['charging_booking_pk']))
	{
	}
	else
	{
		$nru = array();  
		$nru['wallet_debit_amount'] = 0;	 	
		$nru['wallet_credit_amount'] = $amount;	 	
		$nru['user_pk'] = $login_eviot_user_id;	 	
		$nru['charging_booking_pk'] = $charging_booking_pk;	 	
		$nru['description'] = CANCELLATION_REFUND;	 	
		$nru['creation_date'] = CURRENT_DB_DATE; 
		$id =  $db->insert('wallet', $nru);
	}

	}

 
ob_clean();
header('Location: ./ ') ; 
exit();
 
?> 