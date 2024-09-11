<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/functions.php'); 
 
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "Pending Charging";
	require_once('header.php'); 
 
 ?> 
 <br/>
 <div class="row">
		
			 <div class="col-4 p-1">
                <button class="btn  btn-block btn-warning  sk-rounded"    >Pending</button>
			</div>  
			 <div class="col-4 p-1">
                <button class="btn  btn-block btn-outline-success  sk-rounded "  onclick="GoToUrl('completed_charging.php')" >Completed</button>
			</div>
			 <div class="col-4 p-1">
                <button class="btn  btn-block btn-outline-danger  sk-rounded "  onclick="GoToUrl('cancelled_charging.php')" >Cancelled</button>
			</div>
		</div>
 
<?php
$sql = "Select bb.*, DATE_FORMAT(bb.start_time, '%d-%m-%Y') as booking_date,   DATE_FORMAT(bb.start_time, '%H:%i') as booking_time ,
cb.charge_box_pk ,cb.charge_point_vendor, cb.charge_box_id   , cb.charge_point_vendor,
c.connector_pk  , c.connector_name    ,
c.connector_id    , cp.min_charging_rate  , ct.connector_type, ct.current_type,	
cp.charging_rate_unit  ,c.connector_capacity,
IFNULL((Select status from connector_status where  connector_pk = c.connector_pk order by  status_timestamp  desc limit 1 ), 'Not Available') as connector_status				
from charging_booking as bb 
join connector as c  on c.connector_pk = bb.connector_pk
join connector_type as ct on c.connector_type_pk  = ct.connector_type_pk 	
join charge_box as cb on c.charge_box_id  = cb.charge_box_id 	
join connector_charging_profile as ccp on c.connector_pk  = ccp.connector_pk 	
join charging_profile as cp on ccp.charging_profile_pk  = cp.charging_profile_pk 
where bb.user_pk = ".$login_eviot_user_id." and  bb.is_active =  ".INACTIVE_FLAG." 
 order by bb.charging_booking_pk desc ";
// die($sql);  
$charging_booking_list =    $db->fetch_array($sql) ; 
if(count($charging_booking_list) > 0)
{
	 foreach($charging_booking_list as  $charging_booking)
	{
		$charging_booking_pk = $charging_booking['charging_booking_pk'];
		$is_active = $charging_booking['is_active'];
		$url = '';
		 
		
		$connector_pk = $charging_booking['connector_pk'];
		  

	?>
		
	   <!-- SELECT2 EXAMPLE -->
			<div class="card card-warning   card-outline" style="margin-top:20px">
			  <div class="card-header sk-text-center">
				<h3 class="card-title  sk-text-center" ><?=$charging_booking['charge_point_vendor']?></h3>
	 
			  </div>
			  <!-- /.card-header -->
			  <div class="card-body">
				<div class="row  ">
				  <div class="col-3 sk-text-center">
					<div class="form-group  sk-inline ">
					<img src="../uploads/charger.png" height="135" /> 
					</div>  
				</div>  
				  <div class="col-9"> 
					<div class="row  ">
					  <div class="col-12">
						<div class="form-group    "> 
						  <span class="sk-left">Charger ID : </span><span class="sk-right"><?=$charging_booking['charge_box_id']?></span>
						</div>  
					</div>  
					  <div class="col-12">
						<div class="form-group    "> 
						  <span class="sk-left">Charger Type : </span><span class="sk-right"><?=$charging_booking['current_type']?></span>
						</div>    
					</div>   
					  <div class="col-12">
						<div class="form-group    "> 
						  <span class="sk-left">Connector No : </span><span class="sk-right"><?=$charging_booking['connector_id']?></span>
						</div>    
					</div>     
					  <div class="col-12">
						<div class="form-group    "> 
						  <span class="sk-left">kWH Booked : </span><span class="sk-right"><?=$charging_booking['charging_booked_unit']?></span>
						</div>    
					</div>   
					  <div class="col-12">
						<div class="form-group    "> 
						  <span class="sk-left">Booking Date : </span><span class="sk-right"><?=$charging_booking['booking_date']?></span>
						</div>    
					</div>   
					  <div class="col-12">
						<div class="form-group    "> 
						  <span class="sk-left">Start Time : </span><span class="sk-right"><?=$charging_booking['booking_time']?></span>
						</div>    
					</div>    
				</div>
				</div>
				  <div class="col-12">
				  <hr/>
				</div>  
				 <div class="col-6">
					<button class="btn  btn-block btn-success   "  onclick="GoToUrl('charging.php?bid=<?=$charging_booking_pk?>')" >Start </button>
				</div> 
				 <div class="col-6">
					<button class="btn  btn-block btn-danger  "  onclick="GoToUrl('cancel_booking.php?bid=<?=$charging_booking_pk?>')" >Cancel </button>
				</div>  
				<!-- /.row -->
	 
			  </div>  
			</div>
			<!-- /.card -->
	  </div>
	  
	  
		 

	<?php
 
}

echo  '<br/><br/><br/><br/> ';
} 
else{
	?>
	<div class="row  ">
	  <div class="col-12 sk-text-center  mt-5">
		<div class="form-group  sk-inline mt-5">
			<h3>No new charging schedule available</h3>
		</div>  
	</div>  
</div>  
	<?php
}
 
	require_once('footer.php'); 
?>
	 
	 