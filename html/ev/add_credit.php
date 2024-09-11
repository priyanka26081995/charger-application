<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/functions.php'); 
  
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "Add Credit"; 
  
require_once('header.php'); 
$_SESSION['sk_razorpay_order_id']  = '';
$_SESSION['sk_razorpay_id']  = '';
$_SESSION['sk_razorpay_amount']  = '';
 $amount = 0;
  if(isset($_REQUEST['amount'] )         )  
{
	 $amount = $_REQUEST['amount'];
}
?>
	
 <br/>
   <!-- SELECT2 EXAMPLE -->
        <div class="card card-primary card-outline">
          <div class="card-header sk-text-center">
            <h3 class="card-title  sk-text-center" >Add Credit</h3>
 
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row sk-text-center">
              <div class="col-12">
                <div class="form-group sk-inline  ">
				<img src="../uploads/ewallet.png" height="50" /><br/>
                  My Credit : <?=$login_eviot_user_balance?>
                </div>  
			</div>  
              <div class="col-12">
                <div class="form-group sk-inline  ">
				 1 Credit = Rs. 1.00
                </div>  
            </div>
              <div class="col-12">
			  <hr/>
            </div> 
			
              <div class="col-12">
                <div class="form-group sk-inline  ">
				  Enter Credit Amount : Rs.<input type="text" onfocus="this.type='number'"     id="txt_amount"  placeholder="0"  onkeyup="CheckAmount()" value="<?php echo $amount; ?>" required>/-
                </div>  
            </div>
			 <div class="col-3">
                <button class="btn  btn-block btn-outline-warning  "  onclick="SetAmount(200)" >200</button>
			</div>  
			 <div class="col-3">
                <button class="btn  btn-block btn-outline-warning  "  onclick="SetAmount(300)" >300</button>
			</div>  
			 <div class="col-3">
                <button class="btn  btn-block btn-outline-warning  "  onclick="SetAmount(400)" >400</button>
			</div>  
			 <div class="col-3">
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
                  <button class="btn  btn-block btn-success  " onclick="AddNow()"   >
				  Add Money
				  </button>
                </div> 
            </div>
          </div>
       
        </div>
        <!-- /.card -->
  
  
  

<?php
require_once('footer.php'); 
?>

<script>  
var wallet_add_amount = <?php echo $amount; ?>; 
function AddNow()
{  
	 
	if(wallet_add_amount <= 0)
	{
		ShowToastSK("", "Please enter Wallet Amount" , 'warning'); 
		return;
	}  
	 // wallet_add_amount = 1;
	window.location = 'add_credit_pay.php?amount='+wallet_add_amount; 
	// $.ajax(
		// 'set_booking.php',
		// {
			// data: { 'amount': wallet_add_amount, 'connector': connector_pk, 'vehicle': vehicle_pk}
		// }
	// ).done(function (data) {
		// if(data.indexOf('SESSIONEXPIRED') > -1)
		// {
			// window.location = 'login.php';
		// }
		// else if(data.indexOf('OK') > -1)
		// {
			 // data = data.replace("OK", "");
			// window.location = 'pending_charging.php?id='+data;
		// }
		// else 
		// {
			 // ShowToastSK("", data, 'error'); 
		 
		// }
	// });
} 

function SetAmount(addAmount)
{   
	var amount = $.trim($('#txt_amount').val());
	if(amount == '' || amount == '0')
	{
		wallet_add_amount = 0;
	} 
	 wallet_add_amount = wallet_add_amount + addAmount;
	 
	$('#txt_amount').val(wallet_add_amount); 
	 
} 


function CheckAmount()
{   
	var amount = $.trim($('#txt_amount').val());
	if(amount == '' || amount == '0')
	{
		wallet_add_amount = 0;
	} 
	 amount = parseInt(amount, 10);
	 wallet_add_amount = amount;
	  
	$('#txt_amount').val(wallet_add_amount);  
} 


 </script>
 