<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/functions.php'); 
$json_array = array();
$json_array["data"] =  "";
$json_array["status"] =  "ok";
$json_array["title"] =  "Success"; 
$json_array["used_unit"] =  '0.00'; 
$json_array["used_amount"] =  '0.00'; 
$transaction_begin_value = 0;
$transaction_reading_value = 0;
$transaction_stop_value = 0;
if(isset($_REQUEST['id'] )         )  
{
	 
	$energy_used = 0;
	$charging_rate = 0;
	$amount_used = 0;
	$amount_booked = 0;
	$vehicle_pk = 0;
	$url = "index.php";
	$charging_booking_pk = $_REQUEST['id'];
	 require_once('../dist/lib/db_class.php');  
	 require_once('check_ajax.php');    
	

	$sql = "Select charging_booking_pk 
	from charging_booking   
	where   transaction_pk  in 
	(select transaction_pk from transaction_stop)
		 and  user_pk = ".$login_eviot_user_id." and    charging_booking_pk =  ".$charging_booking_pk ;
			 // die($sql);  
	$charging_booking =   $db->query_first($sql) ; 
	  // var_dump($charging_booking);die();
	  
	if( is_array($charging_booking)   && !empty($charging_booking['charging_booking_pk']))
	{
		// $sql = 'update charging_booking set is_active = '.ACTIVE_FLAG.' 
			// where charging_booking_pk =  '.$charging_booking_pk ;
			// $db->query($sql);
			$json_array["data"] =  "";
			$json_array["status"] =  "redirect";
			$json_array["title"] =  "Success"; 
			$json_array["data"] =  'index.php'; 
	}		
	else
	{
		$sql = "Select bboking.*,IFNULL(bboking.transaction_pk, 0) as transaction_pk, DATE_FORMAT(bboking.start_time, '%d-%m-%Y') as booking_date,   DATE_FORMAT(bboking.start_time, '%H:%i') as booking_time ,cb.charge_box_pk ,cb.charge_point_vendor, cb.charge_box_id   , cb.charge_point_vendor,
		c.connector_pk  , c.connector_name    ,
		c.connector_id    , cp.min_charging_rate  , ct.connector_type, ct.current_type,	
		cp.charging_rate_unit  ,c.connector_capacity,
		IFNULL((Select status from connector_status where  connector_pk = c.connector_pk order by  status_timestamp  desc limit 1 ), 'Not Available') as connector_status	

		from charging_booking   as bboking  
		join connector as c  on c.connector_pk = bboking.connector_pk
		join connector_type as ct on c.connector_type_pk  = ct.connector_type_pk 	
		join charge_box as cb on c.charge_box_id  = cb.charge_box_id 	
		join connector_charging_profile as ccp on c.connector_pk  = ccp.connector_pk 	
		join charging_profile as cp on ccp.charging_profile_pk  = cp.charging_profile_pk 
		where bboking.transaction_pk not in (select transaction_pk from transaction_stop)
			 and bboking.user_pk = ".$login_eviot_user_id." and    bboking.charging_booking_pk =  ".$charging_booking_pk ;
				 // die($sql);  
		$charging_booking =   $db->query_first($sql) ; 
		  // var_dump($charging_booking);die();
		 // $isCharging = true;
		if( is_array($charging_booking)   && !empty($charging_booking['charging_booking_pk']))
		{
			$charging_final_amount = floatval($charging_booking['charging_final_amount']);
			$amount_booked = $charging_booking['charging_booked_amount'];
			$charging_rate = $charging_booking['min_charging_rate'];
			$transaction_pk =  $charging_booking['transaction_pk']; 
			$is_active = $charging_booking['is_active'];
			 
			if($is_active == INACTIVE_FLAG)
			{
				$json_array["data"] =  "";
				$json_array["status"] =  "ok";
				$json_array["title"] =  "Success"; 
				$json_array["used_unit"] =  '0.00'; 
				$json_array["used_amount"] =  '0.00'; 
			} 
			else
			{
				$connector_pk = $charging_booking['connector_pk'];
			 
				$charging_booking_status = $charging_booking['connector_status'];
				if($charging_booking_status == 'Charging'    )
				{
						 
					$url = "";
					if(empty($transaction_pk) )
					{
						if($charging_booking['connector_status'] == 'Charging')
						{
							$sql1 = "Select   transaction_pk  from transaction_start  
							where id_tag = '".$login_eviot_id_tag."' and connector_pk = ".$charging_booking['connector_pk']." and transaction_pk not in (Select   transaction_pk  from transaction_stop)
							 order by event_timestamp desc limit 1 ";

						 
								  
							 $data_sk1 =   $db->query_first($sql1) ; 
							 // var_dump($data_sk1);
								 
							if( is_array($data_sk1)   && !empty($data_sk1['transaction_pk']))
							{ 
								$transaction_pk = $data_sk1['transaction_pk'];
								$sql = 'update charging_booking set transaction_pk = '.$transaction_pk.' 
								where charging_booking_pk =  '.$charging_booking_pk ;
								$db->query($sql); 
							}
						}
					}
					if(!empty($transaction_pk))
					{
						$key_transaction_start_value = "transaction_start_value".$transaction_pk;
						$key_transaction_energy_used = "key_transaction_energy_used".$transaction_pk;
						$key_transaction_amount_used = "key_transaction_energy_used".$transaction_pk;
						if(isset($_SESSION[$key_transaction_start_value]))
						{
							$transaction_begin_value = intval($_SESSION[$key_transaction_start_value]);
						}
						else
						{
							 // $isCharging = true;
							$sql1 = "Select   transaction_pk, start_value  from transaction_start  
							where transaction_pk =  ".$transaction_pk ;

						 
								  
							 $data_sk1 =   $db->query_first($sql1) ; 
							 // var_dump($data_sk1);
								 
							if( is_array($data_sk1)   && !empty($data_sk1['start_value']))
							{ 
								$transaction_begin_value = floatval(trim($data_sk1['start_value'])); 
							}
							if(empty($transaction_begin_value))
							{ 
								$sql = "Select value from connector_meter_value    
									where  ((reading_context = '".TRANSACTION_BEGIN."') or 
								(reading_context = '".TRANSACTION_READING."' and measurand = '".TRANSACTION_MEASURAND_READING."')) 
									and transaction_pk =  ".$transaction_pk ." 
									order by value_timestamp desc limit 1";
										  // var_dump($sql);  
								$charging_booking_meter_value =   $db->query_first($sql) ; 
								  // var_dump($charging_booking_meter_value);
								 
								if( is_array($charging_booking_meter_value)   && !empty($charging_booking_meter_value['value']))
								{
									$transaction_begin_value = floatval(trim($charging_booking_meter_value['value'])); 
								}
							}
							if(!empty($transaction_begin_value))
							{ 
								$_SESSION[$key_transaction_start_value] = $transaction_begin_value;
							}
						}
						$sql = "Select value from connector_meter_value    
							where ((reading_context = '".TRANSACTION_END."') or 
							(reading_context = '".TRANSACTION_READING."' and measurand = '".TRANSACTION_MEASURAND_READING."')) 
							and transaction_pk =  ".$transaction_pk." order by value_timestamp desc limit 1" ;
								 // die($sql);  
						$charging_booking_meter_value =   $db->query_first($sql) ; 
						 // var_dump($sql);
						 // var_dump($charging_booking_meter_value);
						 // die();
						 // $isCharging = true;
						if( is_array($charging_booking_meter_value)   && !empty($charging_booking_meter_value['value']))
						{
							$transaction_reading_value = floatval(trim($charging_booking_meter_value['value'])); 
						}
						if(!empty($transaction_begin_value) && !empty($transaction_reading_value))
						{
							$energy_used = GetChargingUnitInKwh($transaction_begin_value , $transaction_reading_value);
						}
						if(!empty($energy_used))
						{
							 
							if(isset($_SESSION[$key_transaction_energy_used]))
							{
								$session_energy_used = floatval($_SESSION[$key_transaction_energy_used]);
								if($session_energy_used  > $energy_used)
								{
									$energy_used = $session_energy_used;
								}
							}
							$amount_used = $energy_used*$charging_rate ;
							if(isset($_SESSION[$key_transaction_amount_used]))
							{
								$session_amount_used = floatval($_SESSION[$key_transaction_amount_used]);
								if($session_amount_used > $amount_used)
								{
									$amount_used = $session_amount_used;
								}
							}
							$_SESSION[$key_transaction_amount_used] = $amount_used;
							$_SESSION[$key_transaction_energy_used] = $energy_used;
							$diff = 0;
							 
							$json_array["data"] =  "";
							$json_array["status"] =  "ok";
							$json_array["title"] =  "Success"; 
							$json_array["used_unit"] =  GetDecimalAmount($energy_used); 
							$json_array["used_amount"] =  GetDecimalAmount($amount_used); 
							$total_amount = GetAmountWithGST($amount_used, BILL_GST);
							if($total_amount > $charging_final_amount)
							{
								$sql = "update charging_booking set charging_final_unit = '".GetDecimalAmount($energy_used)."' , charging_final_amount = '".GetDecimalAmount($total_amount)."' 
								where charging_booking_pk =  ".$charging_booking_pk ;
								$db->query($sql);
								$diff = $amount_used-$charging_final_amount;
								
								
							}
							
							if(($total_amount+$diff) >= $amount_booked)
							{
								// // //Charging Complete Stop Charging
								// $getData = array();
								// $getData['id'] = $charging_booking_pk;
								// $getData['cmd'] = 0;
								// require_once('trigger.php');
								// // require_once('trigger.php?id='.$charging_booking_pk."&cmd=0");
								$json_array["data"] =  "";
								$json_array["status"] =  "done";
								$json_array["title"] =  "Success"; 
								$json_array["data"] =  'index.php';  
							}
						}
						 
						
					}
					
				}
			}
		}
	} 
} 

 
	
    
$html = json_encode($json_array, JSON_HEX_APOS|JSON_HEX_QUOT);
 // $html = json_encode($json_array, true);
 
 //Set these headers to avoid any issues with cross origin resource sharing issues
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
  ob_clean();
 echo $html; 
 exit();
 