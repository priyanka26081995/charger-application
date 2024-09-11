<?php
require_once('../dist/lib/config.php');   
require_once('check_register.php'); 
 $PAGE_NAME = "Register " ;
 
require_once('header_login.php');  
?>
<div class="login-box"  id="login-box">
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
	
  <div class="login-logo">
    <img src="../dist/img/logo-big.png" alt="ServiceKeeda Logo"  style="opacity: .8;width:150px">
      
  </div>
      <p class="login-box-msg sk-hide" >Create New Account</p>

      <form   method="post">
	  
        <div class="input-group mb-3">
          <input type="text"  class="form-control" value="<?=$first_name?>" name="txt_firstname"  placeholder="First Name *" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text"  class="form-control" value="<?=$last_name?>" name="txt_lastname"  placeholder="Last Name *" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" onfocus="this.type='number'" class="form-control" value="<?=$phone?>" name="txt_phone"  placeholder="Mobile Number *" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-phone"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="email"  class="form-control" value="<?=$email?>" name="txt_email"  placeholder="Email Address *" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control"   name="txt_password"  placeholder="Password  *" required>
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
            <button type="submit" class="btn btn-primary btn-block">Request for OTP</button>
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