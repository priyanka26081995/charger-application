<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/functions.php'); 

 if(!isset($_REQUEST['id'])        )  
{
	ob_clean();
	header('location:./');
	exit();
}
$connector_pk = $_REQUEST['id'];
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "Book Charger";
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

if( is_array($connector)   && !empty($connector['connector_pk']))
{
	$connector_name = $connector['connector_name'];
	if(!empty($connector_name))
	{
		$connector_name = "-".$connector_name;
	}
 $rate_with_gst = GetMinimumRate($connector['min_charging_rate'], RATE_COMMISSION , BILL_GST, MINIMUM_BOOKING_AMOUNT); 
  
require_once('header.php'); 

?>
	
 <br/>
   <!-- SELECT2 EXAMPLE -->
        <div class="card card-primary card-outline">
          <div class="card-header sk-text-center">
            <h2 class="card-title  sk-text-center" ><?=$connector['charge_point_vendor']?></h2>
			 
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row sk-text-center">
              <div class="col-12">
                <h6   ><?=$connector['charge_box_id'].$connector_name ?>  </h6> 
			</div> 
              <div class="col-6">
                <div class="form-group sk-inline  ">
				<img src="../uploads/charger-capacity.png" height="25" /><br/>
                  Capacity : <?=$connector['connector_capacity']?>
                </div>  
			</div>  
              <div class="col-6">
                <div class="form-group sk-inline  ">
				<img src="../uploads/charger-rate.png" height="25" /><br/>
                  Rate : Rs.<?=GetChargingRate($connector['min_charging_rate'], RATE_COMMISSION).'/'.$connector['charging_rate_unit']?> 
                </div>  
            </div>
              <div class="col-12">
			  <hr/>
            </div>
              <div class="col-12">
                <div class="form-group sk-inline  ">
				<img src="../uploads/ewallet.png" height="20" /> 
                  My Credit : <?=$login_eviot_user_balance?> 
                </div>  
            </div>
			  
              <div class="col-12">
                <div class="form-group">
                  <button class="btn  btn-block btn-outline-primary  " onclick="ShowVehicleModal()" id="btnVehicle" >
				  <img src="../uploads/car.jpg" height="20" /> Select Vehicle Model
				  </button>
                </div> 
            </div>
			
              <div class="col-12">
                <div class="form-group sk-inline  ">
				  Enter Amount : <input type="text" onfocus="this.type='number'"     id="txt_amount"  placeholder="0" onkeyup="CheckAmount()" required>
                </div>  
            </div>
			 <div class="col-4">
                <button class="btn  btn-block btn-outline-warning  "  onclick="SetAmount(100)" >100</button>
			</div> 
			 <div class="col-4">
                <button class="btn  btn-block btn-outline-warning  "  onclick="SetAmount(200)" >200</button>
			</div> 
			 <div class="col-4">
                <button class="btn  btn-block btn-outline-warning  "  onclick="SetAmount(500)" >500</button>
			</div>  
            <!-- /.row -->
			
          </div>
			<div class="row sk-text-center">
              <div class="col-12">
			  <hr/>
            </div> 
              <div class="col-12">
                <div class="form-group">
                  <button class="btn  btn-block btn-success  " onclick="BookNow()"   >
				  Book Now
				  </button>
                </div> 
            </div>
          </div>
       
        </div>
        <!-- /.card -->
  
  
  
	<div class="modal fade " id="modal-vehicle">
        <div class="modal-dialog modal-fullscreen modal-xl">
          <div class="modal-content  modal-fullscreen"> 
            <div class="modal-body" style="background:white;padding:bottom:50px">
			
 <br/>
               <?php
			   $sql = "Select * from vehicle_model where is_active = 1";
			   $data_list =   $db->fetch_array($sql) ; 
			   if( count($data_list) > 0  )
				{    
					foreach($data_list as  $data_sk)
					{
						?> 
						<div class="callout callout-light sk-pointer" onclick="SetVehicle(<?=$data_sk['vehicle_pk']?>, '<?=$data_sk['vehicle_model']?>')">
						  <h5>
						  <img src="../uploads/vehicle_<?=$data_sk['vehicle_pk']?>.jpg" height="20" style="margin:5px" /> <?=$data_sk['vehicle_model']?>
						  </h5> 
						</div> 
						<?php
					}
					
echo  '<br/><br/><br/> ';
				}
			   ?>
            </div> 
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
<?php
require_once('footer.php'); 
?>

<script> 
var vehicle_pk = 0;
var charging_amount = 0;
var connector_pk = <?=$connector_pk?>;
var min_amount = <?=$rate_with_gst?>;
var wallet_amount = <?=$login_eviot_user_balance?>;
function BookNow()
{  
	if(vehicle_pk == 0)
	{
		ShowToastSK("", "Please select Vehicle for Booking " , 'warning'); 
		return;
	}
	if(charging_amount <= 0)
	{
		ShowToastSK("", "Please enter Booking Amount" , 'warning'); 
		return;
	}
	if(charging_amount < min_amount)
	{
		ShowToastSK("", "Minimum Amount For Booking is Rs." + min_amount, 'warning'); 
		return;
	} 
	if(charging_amount > wallet_amount)
	{
		var amt = charging_amount - wallet_amount;
		ShowToastSKWallet("Insufficient Wallet Balance", "  Do you want to Add Balance to your Wallet? ", 'warning', amt); 
		return;
	} 
	// // // window.location = "set_booking.php?amount=" + charging_amount + "&connector=" + connector_pk+ "&vehicle=" + vehicle_pk;
	$.ajax(
		'set_booking.php',
		{
			data: { 'amount': charging_amount, 'connector': connector_pk, 'vehicle': vehicle_pk}
		}
	).done(function (data) {
		if(data.indexOf('SESSIONEXPIRED') > -1)
		{
			window.location = 'login.php';
		}
		else if(data.indexOf('OK') > -1)
		{
			 data = data.replace("OK", "");
			window.location = 'pending_charging.php?id='+data;
		}
		else 
		{
			 ShowToastSK("", data, 'error'); 
		 
		}
	});
} 

function SetAmount(addAmount)
{  
// ShowToastSK("", "Insufficient Wallet Balance");
// alert(addAmount);
	wallet_amount = parseInt(wallet_amount, 10);
	var amount = $.trim($('#txt_amount').val());
	if(amount == '' || amount == '0')
	{
		charging_amount = 0;
	}
	if((charging_amount+addAmount) > wallet_amount)
	{
		// ShowToastSK("", "Insufficient Wallet Balance");
		// charging_amount = wallet_amount;
	}
	 else
	 {
		 charging_amount = charging_amount + addAmount;
	 }
	$('#txt_amount').val(charging_amount); 
	 
} 


function CheckAmount()
{  
	wallet_amount = parseInt(wallet_amount, 10);
	var amount = $.trim($('#txt_amount').val());
	if(amount == '' || amount == '0')
	{
		charging_amount = 0;
	}
	if((charging_amount) > wallet_amount)
	{
		// ShowToastSK("", "Insufficient Wallet Balance");
		// charging_amount = wallet_amount;
	}
	 else
	 {
		 amount = parseInt(amount, 10);
		 charging_amount = amount;
	 }
		$('#txt_amount').val(charging_amount); 
	 
} 


function SetVehicle(id, modelName)
{ 

	vehicle_pk = id;
	$('#btnVehicle').html('<img src="../uploads/vehicle_' + id + '.jpg" style="margin:5px" height="20" />'  +  modelName); 
	$('#modal-vehicle').modal('hide');
}
 function ShowVehicleModal()
{
	
	$('#modal-vehicle').modal('show'); 
}
</script>

<?php
}
else
{
	ob_clean();
	header('Location: ./') ; 
	exit();
}
?>