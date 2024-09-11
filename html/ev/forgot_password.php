<?php
require_once('../dist/lib/config.php');  
require_once('recover_password.php'); 
 $PAGE_NAME = "Forgot Password " ;
 
require_once('header_login.php');  
?>
<div class="login-box"  id="login-box">
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
	
  <div class="login-logo">
    <img src="../dist/img/logo-big.png" alt="ServiceKeeda Logo"  style="opacity: .8;width:150px">
      
  </div>
      <p class="login-box-msg sk-hide" >Enter Email Address To Recover Password</p>

      <form   method="post">
	  
        <div class="input-group mb-3">
          <input type="email"  class="form-control" value="<?=$email?>" name="txt_username"  placeholder="Email Address *" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
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
            <button type="submit" disabled class="btn btn-primary btn-block">Recover Password</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

        <div class="row"> 
          <!-- /.col -->
          <div class="col-12  text-center ">
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