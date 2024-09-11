<?php

require_once('../dist/lib/config.php');  
if(!isset($_REQUEST['id'] )         )  
{
	ob_clean();
	header('location:./');
	exit();
}
require_once('../dist/lib/functions.php'); 
 
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "Booking Invoice Details";
	require_once('header.php'); 
 $charging_booking_pk = $_REQUEST['id'];
 $sql = "Select bboking.*,IFNULL(bboking.transaction_pk, 0) as transaction_pk, DATE_FORMAT(bboking.start_time, '%d-%m-%Y') as booking_date,   DATE_FORMAT(bboking.start_time, '%h:%i:%s %p') as booking_time 

from charging_booking   as bboking   where bboking.user_pk = ".$login_eviot_user_id." and    bboking.charging_booking_pk =  ".$charging_booking_pk ;
		 // die($sql);  
$charging_booking =   $db->query_first($sql) ; 
  // var_dump($charging_booking);die();
 $isCharging = true;
if( is_array($charging_booking)   && !empty($charging_booking['charging_booking_pk']))
{
 ?>
 
 <br/>
 <div class="row">
		
			 <div class="col-12 p-1">
                 <h4>Booking Invoice Details</h4>
			</div>  
		</div>
 
 <?php
 
		 

	?>
		
	   <!-- Transaction Details-->
		<div class="card card-default  card-outline  " style="margin-top:5px">
			  <div class="card-header sk-text-center">
				<h3 class="card-title   " >Transaction Details</h3>
	 
			  </div>
			  <!-- /.card-header -->
			<div class="card-body">
				<div class="row  "> 
					<div class="col-12">  
						<div class="form-group   p-1   "> 
							<span class="sk-left">Order ID : </span><span class="sk-right"><?=$charging_booking['charging_booking_order_id']?></span>
						</div>    
					</div>
				<!-- /.card -->
				 <div class="col-12">  
				  <hr/>  
					</div>
					<div class="col-12">  
						<div class="form-group    p-1  "> 
							<span class="sk-left"> <?php echo $charging_booking['booking_date']." ".$charging_booking['booking_time']?></span>
						</div>    
					</div>
				<!-- /.card -->
				</div>
			</div>
		</div>
	   <!-- Payments-->
		<div class="card card-default  card-outline  " style="margin-top:5px">
			  <div class="card-header sk-text-center">
				<h3 class="card-title   " >Payments</h3>
	 
			  </div>
			  <!-- /.card-header -->
			<div class="card-body">
				<div class="row  "> 
					<div class="col-12">  
						<div class="form-group     p-1 "> 
							<span class="sk-left">Booking Amount: </span><span class="sk-right">Rs.<?=$charging_booking['charging_booked_amount']?></span>
						</div>    
					</div>
					<div class="col-12">  
						<div class="form-group   p-1 "> 
							<span class="sk-left">Discount Amount: </span><span class="sk-right">Rs.0.00</span>
						</div>    
					</div>
					<div class="col-12">  
						<div class="form-group    p-1  "> 
							<span class="sk-left">Paid Amount: </span><span class="sk-right">Rs.<?=$charging_booking['charging_booked_amount']?></span>
						</div>    
					</div>
				<!-- /.card -->
				 <div class="col-12">  
				  <hr/>  
					</div>
					 
				<!-- /.card -->
				</div>
			</div>
		</div>
	  
	   <!-- Charging Details-->
		<div class="card card-default  card-outline  " style="margin-top:5px">
			  <div class="card-header sk-text-center">
				<h3 class="card-title   " > Charging Details</h3>
	 
			  </div>
			  <!-- /.card-header -->
			<div class="card-body">
			<?php
				$charging_booked_amount = floatval($charging_booking['charging_booked_amount']);
				$charging_rate = floatval($charging_booking['min_charging_rate']);
				$energy_used = floatval($charging_booking['charging_final_unit']); 
				$amount_used = $energy_used*$charging_rate ;
				$amount_used = floatval($charging_booking['charging_final_amount']) ;
				$total_amount = GetAmountWithGST($amount_used, BILL_GST);
			?>
				<div class="row  "> 
					<div class="col-12">  
						<div class="form-group   p-1   "> 
							<span class="sk-left">Energy Consumed(kWh) : </span><span class="sk-right"><?php echo $energy_used; ?>kW</span>
						</div>    
					</div>
					<div class="col-12">  
						<div class="form-group   p-1   "> 
							<span class="sk-left">Rate/kWh : </span><span class="sk-right">Rs.<?=$charging_rate?></span>
						</div>    
					</div>
					
					<div class="col-12">  
						<div class="form-group   p-1   "> 
							<span class="sk-left">Sub Total : </span><span class="sk-right">Rs.<?=$amount_used?></span>
						</div>    
					</div>
					<div class="col-12">  
						<div class="form-group   p-1   "> 
							<span class="sk-left">Taxes(<?=BILL_GST?>%) : </span><span class="sk-right">Rs.<?=($total_amount-$amount_used)?></span>
						</div>    
					</div>
					<div class="col-12">  
						<div class="form-group   p-1   "> 
							<span class="sk-left">Total Amount: </span><span class="sk-right">Rs.<?=$total_amount?></span>
						</div>    
					</div>
				<!-- /.card -->
				 <div class="col-12">  
				  <hr/>  
					</div>
					
					<div class="col-12">  
						<div class="form-group   p-1   "> 
							<span class="sk-left">Refunded Amount  : </span><span class="sk-right">Rs.<?=($charging_booked_amount-$total_amount)?></span>
						</div>    
					</div>
				<!-- /.card -->
				</div>
			</div>
		</div>
	  
	   
				<div class="row  " > 
				 <div class="col-6" style="display:none">
					<a class="btn  btn-block btn-warning   " href="invoice.php?id=<?php echo $charging_booking_pk; ?>"  >Download Invoice </a>
				</div> 
				 <div class="col-12">
					<a class="btn  btn-block btn-danger  "  href="completed_charging.php"  >Back </a>
				</div>  
				</div>  
	  
	 
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
  
	<?php
 
 
	require_once('footer.php'); 
}
else
{
	ob_clean();
	header('location:./');
	exit();
}
?>
	 