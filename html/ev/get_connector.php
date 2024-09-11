<?php
require_once('../dist/lib/config.php'); 
require_once('../dist/lib/functions.php'); 
 // var_dump($_REQUEST['id']);die();
$charge_box_pk = '';
 if(isset($_REQUEST['id'])        )  
{
	$charge_box_pk = $_REQUEST['id']; 
}  
if(empty($charge_box_pk))
{
	
	?>
	 
	<div class="alert alert-danger ">
   <h5> NO DATA AVAILABLE !</h5>
   </div>
	<?php
	exit();
}
 require_once('../dist/lib/db_class.php');  
 require_once('check_ajax.php'); 

$sql = "Select  location_latitude  , location_longitude  
from charge_box 
where charge_box_pk = ".$charge_box_pk; 
	   // var_dump($sql);  
$chargebox =   $db->query_first($sql) ;
 // var_dump($chargebox);
 // die();
if( is_array($chargebox)   && !empty($chargebox['location_latitude']))
{
	$location_latitude = $chargebox['location_latitude'];  
	$location_longitude = $chargebox['location_longitude'];  
	$sql = "Select   c.charge_box_id  ,c.connector_pk  , c.connector_name    ,
	c.connector_id    , cp.min_charging_rate  , ct.connector_type, ct.current_type,	
	cp.charging_rate_unit  ,c.connector_capacity,
	IFNULL((Select status from connector_status where  connector_pk = c.connector_pk order by  status_timestamp  desc limit 1 ), 'Available') as connector_status,
	IFNULL(cbook.charging_booking_pk , '0') as charging_booking_pk 				
	from connector as c  
	join connector_type as ct on c.connector_type_pk  = ct.connector_type_pk 	
	join charge_box as cb on c.charge_box_id  = cb.charge_box_id 	
	join connector_charging_profile as ccp on c.connector_pk  = ccp.connector_pk 	
	join charging_profile as cp on ccp.charging_profile_pk  = cp.charging_profile_pk 	
	left outer join charging_booking as cbook on c.connector_pk  = cbook.connector_pk and cbook.user_pk = ".$login_eviot_user_id."	and cbook.is_active = ".INACTIVE_FLAG." 
	where  c.IS_ACTIVE = 1 and  cb.location_latitude = '".$location_latitude."'  and  cb.location_longitude = '".$location_longitude."' 
	and cb.IS_ACTIVE = 1 and cb.registration_status = 'Accepted'" ;
			 // die($sql);  
	$connector_list =   $db->fetch_array($sql) ; 
	// if($login_eviot_user_id == '13')
	// {
	 // var_dump($connector_list);
	  // die($sql);  
	
	// }
	 if(count($connector_list) > 0)
	 {
		 foreach($connector_list as  $connector)
		{
			$connector_name = $connector['connector_name'];
			if(!empty($connector_name))
			{
				$connector_name = "-".$connector_name;
			}
		?>

		<div class="callout callout-success">
		<div class="row">
			<div class="col-6"> 
			  <h5 style="display:inline-block" ><?=$connector['charge_box_id'].$connector_name ?>
			  </h5><br/>
			  <p style="display:inline-block" ><?=$connector['connector_type'].'('.$connector['connector_capacity'].')'?>
			  </p>

			  <p class="sk-hide">Rate : Rs.<?=GetChargingRate($connector['min_charging_rate'], RATE_COMMISSION).'/'.$connector['charging_rate_unit']?> </p> 
		  </div>
		  <div class="col-6"> 
			  <?php
			   if($connector['connector_status'] == 'Available' || $connector['connector_status'] == "Preparing" )
							  { 
									  echo '<button class="btn  sk-right btn-warning" onclick="GoToUrl(\'book_charger.php?id='.$connector['connector_pk'].'\')" >Book Charger </button>';
								 
							  }   
							   else if($connector['connector_status'] == 'Charging')
							  {
								   $sql1 = "Select   cb.charging_booking_pk   from transaction_start  as t
								   join charging_booking as cb on t.transaction_pk=cb.transaction_pk
								   
									where  t.connector_pk = ".$connector['connector_pk']." and t.transaction_pk not in (Select   transaction_pk  from transaction_stop)
									 order by t.event_timestamp desc limit 1 ";
 
  
									 $data_sk2 =   $db->query_first($sql1) ; 
									 //var_dump($data_list);
										 
									if( is_array($data_sk2)   && !empty($data_sk2['charging_booking_pk ']))
									{ 
										  $sql1 = "Select   cb.charging_booking_pk   from transaction_start  as t
										   join charging_booking as cb on t.transaction_pk=cb.transaction_pk
										   
											where t.id_tag = '".$login_eviot_id_tag."' and t.connector_pk = ".$connector['connector_pk']." and t.transaction_pk not in (Select   transaction_pk  from transaction_stop)
											 order by t.event_timestamp desc limit 1 ";
		 
		  
											 $data_sk1 =   $db->query_first($sql1) ; 
											 //var_dump($data_list);
												 
											if( is_array($data_sk1)   && !empty($data_sk1['charging_booking_pk ']))
											{  
											  echo '<button class="btn  sk-right btn-info  "  onclick="GoToUrl(\'charging.php?bid='.$data_sk1['charging_booking_pk '].'\')" >Charging</button>';
										  }
										  else{
											   echo '<button class="btn  sk-right btn-info  "  >Charging </button>';
										  }
									}
									else
									{
										$connector['connector_status'] = "Available";
										 echo '<button class="btn  sk-right btn-warning" onclick="GoToUrl(\'book_charger.php?id='.$connector['connector_pk'].'\')" >Book Charger </button>';
									}
							  } 
							  else if($connector['connector_status'] == 'Unavailable')
							  {
								  echo '<button class="btn  sk-right btn-danger  "  >Unavailable </button>';
							  } 
							  else if($connector['connector_status'] == 'Finishing')
							  {
								  echo '<button class="btn  sk-right btn-warning  "  >Unavailable </button>';
							  } 
							  else if($connector['connector_status'] == 'Faulted')
							  {
								  echo '<button class="btn  sk-right btn-danger  "  >Unavailable </button>';
							  } 
							  else  
							  {
								  echo '<button class="btn  sk-right btn-light  "  >'.$connector['connector_status'].' </button>';
							  }  
			  ?> 
			  
			</div>
			</div>
			</div>

		<?php

		}
	}
	else
	{
	 ?>
	 
	 
		<div class="alert alert-danger ">
	   <h5> NO CONNECTOR AVAILABLE   !</h5>
	   </div>
	 <?php
	}
}
else
{
	?>
	<div class="alert alert-danger ">
   <h5> NO CHARGER AVAILABLE   !</h5>
   </div>
	<?php
}
?> 