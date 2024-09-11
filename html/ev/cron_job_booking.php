<?php

// phpinfo();die();
require_once('/var/www/html/lib/config.php');  
require_once('/var/www/html/lib/functions.php'); 
require_once('/var/www/html/lib/db_class.php');  
 
 
// $getData = array();
// $getData['id'] = 777777;
// $getData['cmd'] = 0;
// require_once('/var/www/html/ev/cron_job_trigger.php');
// exit();


 
	 
 
$transaction_begin_value = 0;
$transaction_reading_value = 0;
$transaction_stop_value = 0;
 
	 
	$energy_used = 0;
	$charging_rate = 0;
	$amount_used = 0;
	$amount_booked = 0;
	$vehicle_pk = 0;
	$url = "index.php";  
	  
	  $id_array = array();
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
	and  bboking.is_active =  ".PROCESS_FLAG;
			  
	$chargings =   $db->fetch_array($sql) ; 
	  // var_dump($chargings);die();
	 // $isCharging = true;
	 foreach($chargings as $charging_booking) 
	{
		$login_eviot_user_id = $charging_booking['user_pk'];
		$charging_booking_pk = $charging_booking['charging_booking_pk'];
		$charging_final_amount = floatval($charging_booking['charging_final_amount']);
		$amount_booked = $charging_booking['charging_booked_amount'];
		$charging_rate = $charging_booking['min_charging_rate'];
		$transaction_pk =  $charging_booking['transaction_pk']; 
		$is_active = $charging_booking['is_active'];
		  
		if($is_active == INACTIVE_FLAG)
		{
			 
		} 
		else
		{
			$connector_pk = $charging_booking['connector_pk'];
		 
			$charging_booking_status = $charging_booking['connector_status'];
			if($charging_booking_status == 'Charging'    )
			{
					 
				$url = "";
				$sql = "Select  it.id_tag, u.user_pk from  user as u  		
				join ocpp_tag as it on u.ocpp_tag_pk = it.ocpp_tag_pk		
				where u.user_pk =  ".$login_eviot_user_id    ;
					   // var_dump($sql);  
				$user_data =   $db->query_first($sql) ;
				 // var_dump($user_data);
				 // die();
				if( is_array($user_data)   && !empty($user_data['user_pk']))
				{ 
					$login_eviot_id_tag = $user_data['id_tag']; 
					if(empty($transaction_pk) )
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
					 
					if($charging_booking['connector_status'] == 'Charging')
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
							$amount_used = $energy_used*$charging_rate ;
							$diff = 0;
							 
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
								array_push($id_array, $charging_booking_pk);
								// //Charging Complete Stop Charging
								$getData = array();
								$getData['id'] = $charging_booking_pk;
								$getData['cmd'] = 0;
								require_once('/var/www/html/ev/cron_job_trigger.php');
								// require_once('trigger.php?id='.$charging_booking_pk."&cmd=0"); 
							}
						}
						 
						
					}
					
				}
			}
		}
	}
	if(count($id_array) > 0)
	{
		$nru = array();  
		$nru['task_name'] = 'BOOKING_CHECK_CRON_TASK';	 	
		$nru['creation_date'] = CURRENT_DB_DATE;  	
		$nru['cron_data'] = implode(",", $id_array);
		$id =  $db->insert('cron_job', $nru);
	}
  