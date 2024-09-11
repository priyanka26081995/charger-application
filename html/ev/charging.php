<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/functions.php'); 

	$transaction_begin_value = 0;
	$transaction_reading_value = 0;
	$charging_final_amount = 0;
 if(!isset($_REQUEST['bid'] )         )  
{
	ob_clean();
	header('location:./');
	exit();
}
$energy_used = 0;
$charging_rate = 0;
$amount_used = 0;
$amount_booked = 0;
$vehicle_pk = 0;
$url = "index.php";
$ping_trigger = false;
$charging_booking_pk = $_REQUEST['bid'];
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "Pending Charging";
// $sql = "Select bboking.*, DATE_FORMAT(bboking.start_time, '%d-%m-%Y') as booking_date,   DATE_FORMAT(bboking.start_time, '%H:%i') as booking_time 
// from charging_booking   as bboking 
// left outer join transaction_start as txn on txn.connector_pk = bboking.connector_pk and txn.transaction_pk = bboking.transaction_pk and txn.id_tag = '".$login_eviot_id_tag."' 
	// where    bboking.charging_booking_pk =  ".$charging_booking_pk ;
$sql = "Select bboking.*,IFNULL(bboking.transaction_pk, 0) as transaction_pk, DATE_FORMAT(bboking.start_time, '%d-%m-%Y') as booking_date,   DATE_FORMAT(bboking.start_time, '%H:%i') as booking_time 

from charging_booking   as bboking  where bboking.transaction_pk not in (select transaction_pk from transaction_stop)
	 and bboking.user_pk = ".$login_eviot_user_id." and    bboking.charging_booking_pk =  ".$charging_booking_pk ;
		 // die($sql);  
$charging_booking =   $db->query_first($sql) ; 
  // var_dump($charging_booking);die();
 $isCharging = true;
if( is_array($charging_booking)   && !empty($charging_booking['charging_booking_pk']))
{
	$charging_final_amount = floatval($charging_booking['charging_final_amount']);
	$amount_booked = $charging_booking['charging_booked_amount'];
	$charging_rate = $charging_booking['min_charging_rate'];
	$transaction_pk =  $charging_booking['transaction_pk'];
	$vehicle_pk = $charging_booking['vehicle_pk'];
	$is_active = $charging_booking['is_active'];
	$url = '';
	if($is_active == ACTIVE_FLAG)
	{
		$url = 'completed_charging.php'  ;  
	}
	else if($is_active == REJECT_FLAG)
	{
		$url = 'cancelled_charging.php'  ;  
	} 
	else if($is_active == INACTIVE_FLAG)
	{
		 $isCharging = false;
	}
	if(!empty($url))
	{
		ob_clean();
		header('Location: '.$url) ; 
		exit();
	}
	
	$connector_pk = $charging_booking['connector_pk'];
	$connector_pk = $charging_booking['connector_pk'];
	$sql = "Select  cb.charge_box_pk ,cb.charge_point_vendor, cb.charge_box_id   , cb.charge_point_vendor,
c.connector_pk  , c.connector_name    ,
c.connector_id    , cp.min_charging_rate  , ct.connector_type, ct.current_type,	
cp.charging_rate_unit  ,c.connector_capacity,
IFNULL((Select status from connector_status where  connector_pk = c.connector_pk order by  status_timestamp  desc limit 1 ), 'Available') as connector_status				
from connector as c  
join connector_type as ct on c.connector_type_pk  = ct.connector_type_pk 	
join charge_box as cb on c.charge_box_id  = cb.charge_box_id 	
join connector_charging_profile as ccp on c.connector_pk  = ccp.connector_pk 	
join charging_profile as cp on ccp.charging_profile_pk  = cp.charging_profile_pk 	
where c.connector_pk = ".$connector_pk;
		 // die($sql);  
 $connector =   $db->query_first($sql) ; 
  // var_dump($connector);die();
if( is_array($connector)   && !empty($connector['connector_pk']))
{
	$connector_name = $connector['connector_name'];
	if(!empty($connector_name))
	{
		$connector_name = "-".$connector_name;
	}
	$connector_status = $connector['connector_status'];
	if($connector_status != 'Available' && $connector_status != 'Preparing' && $connector_status != 'Charging'    )
	{
		 ob_clean();
		header('Location: ./') ; 
		exit();
	}
	$url = "";
	if(empty($transaction_pk) )
	{
		if($connector['connector_status'] == 'Charging')
		{
			$sql1 = "Select   transaction_pk  from transaction_start  
			where id_tag = '".$login_eviot_id_tag."' and connector_pk = ".$connector['connector_pk']." and transaction_pk not in (Select   transaction_pk  from transaction_stop)
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
		  $isCharging = true;
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
  $isCharging = true;
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
			//Charging Complete Stop Charging
$ping_trigger = true;
			 
		}
	}
	 
	
}

	
require_once('header.php'); 

?>
	
 <br/>
   <!-- SELECT2 EXAMPLE -->
        <div class="card card-dark  card-outline ">
          <div class="card-header sk-text-center">
            <h3 class="card-title  sk-text-center" ><?=$connector['charge_point_vendor']?></h3>
 
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row  sk-text-center">  
			 <div class="col-12">
                <h6   ><?=$connector['charge_box_id'].$connector_name ?>  </h6> 
			</div> 
              <div class="col-12 sk-text-center">
                <div class="form-group   ">
				<img id="imgStop"  src="../uploads/charging.gif" height="100" <?=($isCharging ? "" : "style='display:none'")?>/> 
                
				<img id="imgStart" src="../uploads/charging.png" height="100"  <?=($isCharging ? "style='display:none'" : "")?>/> 
				<br/>
				<?php
				$energy_used = GetDecimalAmount($energy_used);
				// $energy_used = "0.00";
				?>
				<span style="font-size:24px; " id="used_unit"><?=$energy_used?></span><br/>
				<span style="font-size:16px;  ">Energy Used (Kwh)</span>
                </div>  
			</div>  
			<div class="col-12">
			  <hr/>
            </div> 
			 <div class="col-12 sk-text-center">
                <div class="form-group   ">   
				 <i class="fa fa-bolt"></i><span style="font-size:16px;  " id="spanChargerStatus">
				 <?php
					if($connector_status == 'Preparing' )
					{ 
						 echo 'Preparing ' ;	
					}
					else if($connector_status == 'Charging' )
					{ 
						 echo 'Charging ' ;	
					}
					else  
					{ 
						 echo 'Charger is '.$connector_status ;	
					}  
				 ?>
				 </span><br/>
				<img src="../uploads/vehicle_<?=$charging_booking['vehicle_pk']?>.jpg" height="30"   /> 
			</div>   
			  <hr/> 
			 <div class="col-12 sk-text-center">
                <div class="form-group   ">   
				<?php
				$amount_used = GetDecimalAmount($amount_used);
				// $amount_used = "0.00";
				?>
				<span style="font-size:24px; " id="used_amount"><?=$amount_used?></span><br/>
				<span style="font-size:16px;  ">Total Amount (Rs.)</span>
                </div>  
			</div>     
			 <div class=" col-12 sk-text-center">
                <button id="btnStart"  class="btn    btn-success   " style="width:200px;<?=($isCharging ? "display:none" : "")?>"  onclick="PingCharger(1)" >Start Charging</button>
			 
                <button id="btnStop" class="btn    btn-danger  " style="width:200px;<?=($isCharging ? "" : "display:none")?>"   onclick="PingCharger(0)" >Stop Charging</button>
			</div>  
			</div> 
            <!-- /.row -->
 
          </div>  
        </div>
        <!-- /.card -->
  
  
 
<?php
require_once('footer.php'); 
?>
 <script>   
 var isCharging = <?=($isCharging ? 1 : 0)?>;
function CheckChargingStatus() 
{     
	$.ajax(
		'get_charging_status.php',
		{
			data: { 'id': <?=$charging_booking_pk?>}
		}
	).done(function (data) {
		try{
			try{
				if(data.indexOf('SESSIONEXPIRED') > -1)
				{
					window.location = 'login.php';
				} 
			}catch(err1) {}
			var dataObj = JSON.parse(data);
			if(dataObj.status.indexOf('redirect') > -1)
			{
				window.location = dataObj.data;
			} 
			else if(dataObj.data.indexOf('SESSIONEXPIRED') > -1)
			{
				window.location = 'login.php';
			} 
			else if(dataObj.status.indexOf('done') > -1)
			{
				PingCharger(0);
			} 
			else if(dataObj.status == 'ok')
			{
				$('#used_unit').html(dataObj.used_unit);
				$('#used_amount').html(dataObj.used_amount);
				setTimeout(function() { CheckChargingStatus(); }, 10000); 
			}
			else
			{
				ShowToastSK("", data , 'warning');
			}	
		}
		catch(err) {
			 alert(err.message);
				ShowToastSK("", data , 'error'); 
			 
		}
	});
} 
function GetConnectorStatus()
{     
	if(isCharging == 0)
	{
		$.ajax(
			'get_connector_status.php',
			{
				data: { 'id': <?=$connector_pk?>}
			}
		).done(function (data) {
			if(data.indexOf('SESSIONEXPIRED') > -1)
			{
				window.location = 'login.php';
			}
			else if(data.indexOf('Available') > -1)
			{ 
				 $('#spanChargerStatus').html('Please connect the Charging Cable.');	
			}
			else if(data.indexOf('Preparing') > -1)
			{ 
				 $('#spanChargerStatus').html(data);
				  
			}
			else if(data.indexOf('Charging') > -1)
			{ 
				 $('#spanChargerStatus').html(data); 
				 
			}
			else  
			{
				 $('#spanChargerStatus').html('Charger is ' + data);	 
			}
			
			setTimeout(function() { GetConnectorStatus(); }, 2000);
		});
	}
} 
function PingCharger(chargeStatus)
{    
// window.location = "trigger.php?id=" + <?=$charging_booking_pk?> + "cmd=" +chargeStatus;
// return;
	$.ajax(
		'trigger.php',
		{
			data: { 'id': <?=$charging_booking_pk?> , cmd : chargeStatus }
		}
	).done(function (data) {
		//ShowToastSK("", data, 'info'); 
		if(data.indexOf('SESSIONEXPIRED') > -1)
		{
			window.location = 'login.php';
		}
		else if(data.indexOf('Available') > -1)
		{ 
			 $('#spanChargerStatus').html('Please connect the Charging Cable.');	
			 ShowToastSK("", "Please connect the Charging Cable." , 'warning'); 
		}
		else if(data.indexOf('OK') > -1)
		{  
			if(chargeStatus == 1)
			{
				 $('#spanChargerStatus').html("Charging");
				 $('#btnStart').hide();
				 $('#btnStop').show();
				 $('#imgStart').hide();
				 $('#imgStop').show();
				 isCharging = 1;
				setTimeout(function() { CheckChargingStatus(); }, 20000); 
				  // setTimeout(function() { ReloadSKPage(); }, 21000); 
			}
			 else
			 {
				 GoToUrl('completed_charging.php');
			 }
		}
		else  
		{
			 ShowToastSK("", data, 'error'); 	 
		} 
	});
} 
<?php
if($ping_trigger)
{
?>
	PingCharger(0);
<?php
} 
if($isCharging)
{
?>
	setTimeout(function() { CheckChargingStatus(); }, 10000); 
<?php
}
else
{
	?>
	setTimeout(function() { GetConnectorStatus(); }, 2000);
	<?php
}
?>
</script>


<?php
}
}
if(!empty($url))
{
	ob_clean();
	header('Location: '.$url) ; 
	exit();
}
?>