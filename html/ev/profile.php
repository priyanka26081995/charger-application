<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/functions.php'); 
 
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "My Profile"; 
	
 
require_once('header.php'); 
$sql = "Select u.user_pk, u.first_name, u.last_name, u.phone, u.e_mail, u.is_active
, IFNULL(t.id_tag , '') as street, IFNULL(a.street, '') as street, IFNULL(a.house_number, '') as house_number, IFNULL(a.zip_code, '') as zip_code, 
IFNULL(a.city, '') as city, IFNULL(a.country	, '') as country	 
from user as u
left outer join address as a on a.address_pk = u.address_pk						
left outer join ocpp_tag as t on u.ocpp_tag_pk  = t.ocpp_tag_pk 						
where u.user_pk = ".$login_eviot_user_id ;
   // die($sql);  
$user_data =   $db->query_first($sql) ;
?>
	<style>
	.form-group {
		margin-bottom: 0.5rem;
	}
	</style>
	
 <br/>
   <!-- SELECT2 EXAMPLE -->
        <div class="card card-primary card-outline"> 
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row sk-text-center">
              <div class="col-12">
                <div class="form-group sk-inline  ">
				<img class="profile-user-img img-fluid img-circle" src="../uploads/user.png" alt="User profile picture">
			</div>  
              <div class="col-12">
                <div class="form-group sk-inline  "> 
                 <?=($user_data['first_name'].' '.$user_data['last_name'])?>
                </div>  
            </div>
              <div class="col-12">
                <div class="form-group sk-inline  "> 
                 <?=($user_data['phone'])?>
                </div>  
            </div>
              <div class="col-12">
                <div class="form-group sk-inline  "> 
                 My Credit : <?=$login_eviot_user_balance?>
                </div>  
            </div>
              <div class="col-12">
			  <hr/>
            </div>
            </div>
            </div>
              
			<div class="row  "> 
              <div class="col-12">
                <div class="form-group">
                  <button class="btn  btn-block btn-light sk-text-left "   onclick="GoToUrl('add_credit.php');"   >
				  <i class="fa fa-wallet" style="padding-right:20px"></i>Add Credit
				  </button>
                </div> 
            </div> 
              <div class="col-12">
                <div class="form-group">
                  <button class="btn  btn-block btn-light sk-text-left "   onclick="GoToUrl('wallet_history.php');"   >
				  <i class="fa fa-database" style="padding-right:20px"></i>Wallet History
				  </button>
                </div> 
            </div> 
              <div class="col-12">
                <div class="form-group">
                  <button class="btn  btn-block btn-light sk-text-left "  onclick="GoToUrl('logout.php');"  >
				  <i class="fa fa-sign-out-alt" style="padding-right:20px"></i>Logout
				  </button>
                </div> 
            </div>
          </div>
       
        </div>
        <!-- /.card -->
  
  
  
 <?php
require_once('footer.php'); 
?> 