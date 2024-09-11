<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/functions.php'); 
 
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "Wallet History";
	require_once('header.php'); 
 
 
$sql = "Select w.*, DATE_FORMAT(w.creation_date, '%d-%m-%Y %H:%i:%s') as wallet_time ,
IFNULL(cb.charging_booking_order_id, '') as charging_booking_order_id				
	from wallet as w 
left outer join charging_booking as cb on w.charging_booking_pk   = cb.charging_booking_pk   
where w.user_pk = ".$login_eviot_user_id." order by w.wallet_pk desc" ;
		 // die($sql);  
$charging_booking_list =   $db->fetch_array($sql) ; 
 if(count($charging_booking_list) > 0)
 {
	 echo"<br/>";
	 foreach($charging_booking_list as  $charging_booking)
	{
		// var_dump($charging_booking);
		$charging_booking_pk = $charging_booking['charging_booking_order_id'];
		$wallet_description = $charging_booking['description'];
		$wallet_note = $charging_booking['description'];
		 if($wallet_description == CANCELLATION_REFUND)
		 {
			 $wallet_note = 'Amount refunded due to due to order cancellation';
		 }
		 else if($wallet_description == BOOKING_CHARGES)
		 {
			 $wallet_note = 'Amount charged for charging session';
		 } 
		 else if($wallet_description == ORDER_REFUND)
		 {
			 $wallet_note = 'Amount refunded on charging session';
		 } 
		 else if($wallet_description == NEW_BOOKING)
		 {
			 $wallet_note = 'Amount charged for new booking';
		 } 
		 else if($wallet_description == CREDIT_RECHARGE)
		 {
			 $wallet_note = 'Amount Added in My Credit';
		 }  
		$transaction_type = 'Amount Debited';
		$wallet_amount = GetDecimalAmount($charging_booking['wallet_debit_amount']);
		if($wallet_amount == '0.00')
		{
			$transaction_type = 'Amount Credited';
			$wallet_amount = GetDecimalAmount($charging_booking['wallet_credit_amount']);
		}
		
	?>
		
 <br/>
	   <!-- SELECT2 EXAMPLE -->
			<div class="card card-dark  card-outline  " style="margin-top:20px">
			  <div class="card-header  ">
				<h3 class="card-title   " ><?=$wallet_description?></h3>
				 
			  </div>
			  <!-- /.card-header -->
			  <div class="card-body">
				<div class="row  ">  
					  <div class="col-12">
						<div   style="font-size:14px"> 
						    <?=$wallet_note ?> 
						</div>  
					</div>  
					  <div class="col-12">
						<div class="form-group    "> 
						  <span class="sk-left">Date : </span><span class="sk-right"><?=$charging_booking['wallet_time']?></span>
						</div>  
					</div>  
					  <div class="col-12">
						<div class="form-group    "> 
						  <span class="sk-left">Order ID : </span><span class="sk-right"><?=$charging_booking['charging_booking_order_id']?></span>
						</div>  
					</div>  
				  <div class="col-12">
				  <hr/>
				</div>  
				 <div class="col-6 sk-left ">
					 <i class="fa fa-wallet"></i><span style="padding-left:5px">Rs.<?=$wallet_amount?></span>
				</div> 
				 <div class="col-6   ">
					<span class=" sk-right "><?=$transaction_type?></span>
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
?>
 
<?php	  
	require_once('footer.php'); 
?>
	 