<?php
require_once('../dist/lib/config.php');   
require_once('../dist/lib/db_class.php');  
require_once('../dist/lib/functions.php');
require_once('check_otp.php'); 
 $PAGE_NAME = "Verify OTP " ;
 
require_once('header_login.php');  
?>
<div class="login-box"  id="login-box">
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
	
  <div class="login-logo">
    <img src="../dist/img/logo-big.png" alt="ServiceKeeda Logo"  style="opacity: .8;width:150px">
      
  </div>
      <p class="login-box-msg sk-hide" >Enter OTP received on your Mobile</p>

      <form   method="post">
	   
        <div class="input-group mb-3">
          <input type="text" onfocus="this.type='number'" class="form-control" value="<?=$otp?>" name="txt_otp"  placeholder="OTP *" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key"></span>
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
            <button type="submit" class="btn btn-primary btn-block">Verify OTP</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
  
      <!-- /.social-auth-links -->

        <div class="row"> 
          <!-- /.col -->
          <div class="col-12  text-center"> 
            <a href="login.php"  >Already have an account ? Sign In </a>
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