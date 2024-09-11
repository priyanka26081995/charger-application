<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try{
require_once('../dist/lib/config.php');  
require_once('check_login.php'); 
 $PAGE_NAME = "Login " ;
 
 $_SESSION['login_eviot_company'] = 1; //COMPANY
 if(isset($_REQUEST['cid'])      )    
{   
	$_SESSION['login_eviot_company'] = $_REQUEST['cid']; 
}
require_once('header_login.php');  
}catch(Exception $e){
	print_r($e);
	
}
?>
<div class="login-box"  id="login-box">
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
	
  <div class="login-logo">
    <img src="../dist/img/logo-big.png" alt="ServiceKeeda Logo"  style="opacity: .8;width:150px">
      
  </div>
      <p class="login-box-msg sk-hide" >Sign in to start your session</p>

      <form   method="post">
	  
        <div class="input-group mb-3">
          <input type="text" onfocus="this.type='number'" class="form-control" value="<?=$username?>" name="txt_username"  placeholder="Mobile Number" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-phone"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control"   name="txt_password"  placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row"> 
		
          <!-- /.col -->
          <div class="col-12 mb-3">
		  <?php
		if(!empty($msg))
		{
		?>
		
		<div class="callout callout-danger">
		  
		  <p class="text-danger"><?=$msg?></p>
		</div> 
		<?php
		}
		?>
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

        <div class="row"> 
          <!-- /.col -->
          <div class="col-12  text-center " style="display:none">
            <a class="text-danger " href="forgot_password.php">Forgot Password ?</a>
          </div>
          <!-- /.col --> 
          <!-- /.col -->
        </div> 
      <div class="social-auth-links text-center mb-3">
        <hr/>
      </div>
      <!-- /.social-auth-links -->

        <div class="row"> 
          <!-- /.col -->
          <div class="col-12  text-center"> 
            <a href="register.php"  >Don't have an account ? Sign Up </a>
          </div>
          <!-- /.col -->
        </div> 
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
<?php  
require_once('footer_login.php'); 
?>