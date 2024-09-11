<?php
require_once('../dist/lib/config.php'); 
$msg = "No data Posted. Please try again...";
$amount = 0;
$vehicle_pk = 0;
$connector_pk = 0;

// var_dump($_REQUEST);die();
 if(isset($_REQUEST['amount']) && isset($_REQUEST['vehicle']) && isset($_REQUEST['connector'])    )  
{
	$amount = $_REQUEST['amount'];
	$vehicle_pk = $_REQUEST['vehicle'];
	$connector_pk = $_REQUEST['connector'];
	require_once('../dist/lib/db_class.php');  
	require_once('../dist/lib/functions.php');  
	require_once('check_ajax.php'); 

	 $sql = "Select charging_booking_pk ,is_active  from charging_booking   	
	where   user_pk  = ".$login_eviot_user_id." and is_active in ( ".PROCESS_FLAG.") order by charging_booking_pk  desc limit 1"   ;
		    // var_dump($sql);  
	$user_data =   $db->query_first($sql) ;
	  // var_dump($user_data);
	  // die();
	if( is_array($user_data)   && !empty($user_data['charging_booking_pk']))
	{
		ob_clean();
		echo "Your Charging Schedule is Already Running.." ;
		exit();
	}
	
	$sql = "Select charging_booking_pk ,is_active  from charging_booking   	
	where   user_pk = ".$login_eviot_user_id." and    connector_pk =  ".$connector_pk ."    and  is_active in(".INACTIVE_FLAG.",".PROCESS_FLAG.") order by charging_booking_pk  desc limit 1"   ;
		    // var_dump($sql);  
	$user_data =   $db->query_first($sql) ;
	  // var_dump($user_data);
	  // die();
	if( is_array($user_data)   && !empty($user_data['charging_booking_pk']))
	{
		ob_clean();
		echo "Booking Schedule Not Available" ;
		exit();
	}
	
	$msg = "";
	
	
	
	if(!empty($msg))
	{
		ob_clean();
		 echo $msg;
		exit();
	}
	 
	 $sql = "Select   c.connector_pk  , c.connector_name    ,
	c.connector_id    , cp.min_charging_rate  , ct.connector_type, ct.current_type,	
	cp.charging_rate_unit  ,c.connector_capacity,
	IFNULL((Select status from connector_status where  connector_pk = c.connector_pk order by  status_timestamp  desc limit 1 ), 'Not Available') as connector_status 			
	from connector as c  
	join connector_type as ct on c.connector_type_pk  = ct.connector_type_pk 	
	join charge_box as cb on c.charge_box_id  = cb.charge_box_id 	
	join connector_charging_profile as ccp on c.connector_pk  = ccp.connector_pk 	
	join charging_profile as cp on ccp.charging_profile_pk  = cp.charging_profile_pk 	 
	where c.connector_pk = ".$connector_pk;

	$connector = $db->query_first($sql) ; 
	$rate_perunit = GetChargingRate($connector['min_charging_rate'], RATE_COMMISSION);
	$amount_withou_gst = GetAmountRemovingGST($amount, BILL_GST);
	$rate_with_gst = GetMinimumRate($connector['min_charging_rate'], RATE_COMMISSION , BILL_GST, MINIMUM_BOOKING_AMOUNT); 
	if($amount<$rate_with_gst)
	{
		ob_clean();
		 echo "Minimum Amount For Booking is Rs.".$rate_with_gst;
		exit();
	}

	if($amount>$login_eviot_user_balance)
	{
		ob_clean();
		 echo "Insufficient Wallet Balance";
		exit();
	}

	$charging_booked_unit =  GetChargingUnit( $amount_withou_gst, $rate_perunit);
	$charging_booking_order_id = ORDERID_PREFIX; 

	$stoptime = date('Y-m-d H:i:s', strtotime('+2 minutes', strtotime(CURRENT_DB_DATE)));
	$nru = array();  
	$nru['charging_booking_order_id'] = $charging_booking_order_id;	 	
	$nru['connector_pk'] = $connector_pk;	 	
	$nru['user_pk'] = $login_eviot_user_id;	 	
	$nru['charging_booked_unit'] = $charging_booked_unit;	 	
	$nru['min_charging_rate'] = $rate_perunit;	 	
	$nru['charging_rate_unit'] = $connector['charging_rate_unit'];	 	
	$nru['start_time'] = CURRENT_DB_DATE;
	$nru['end_time'] = $stoptime;  
	$nru['charging_booked_amount'] = $amount;	 	
	$nru['charging_final_unit'] = 0;	 	
	$nru['charging_final_amount'] = 0;	 	
	$nru['is_active'] = INACTIVE_FLAG;	 	
	$nru['vehicle_pk'] = $vehicle_pk;	 	
	$nru['transaction_pk'] = 0;	 	
	$charging_booking_pk =  $db->insert('charging_booking', $nru);


	$charging_booking_order_id = ORDERID_PREFIX.sprintf('%0'.ORDERID_LENGTH.'d', $charging_booking_pk);

	$nru['charging_booking_order_id'] = $charging_booking_order_id;	 
	$db->update('charging_booking', $nru, "charging_booking_pk  = ".$charging_booking_pk);



	$nru = array();  
	$nru['wallet_debit_amount'] = $amount;	 	
	$nru['wallet_credit_amount'] = 0;	 	
	$nru['user_pk'] = $login_eviot_user_id;	 	
	$nru['charging_booking_pk'] = $charging_booking_pk;	 	
	$nru['description'] = NEW_BOOKING;	 	
	$nru['creation_date'] = CURRENT_DB_DATE; 
	$id =  $db->insert('wallet', $nru);


	echo "OK".$charging_booking_pk ;
} 
?> 