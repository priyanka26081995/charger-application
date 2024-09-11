<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Service Keeda</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="javascript:void()" class="h1"><b>SK</b>Tech</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">OM BHAT SWAHA</p>
 
        <div class="input-group mb-3">
          <input type="text" onfocus="this.type='number'" id="txtMobile" class="form-control" maxlength="10" placeholder="ENTER MOBILE NUMBER">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div> 
        <div class="row"> 
          <!-- /.col -->
          <div class="col-12">
            <button type="button" class="btn btn-primary btn-block" onclick="CalculateMagic()">Check This Mobile Number</button>
          </div>
          <!-- /.col -->
        </div>  
		<div id="divError"  >
		<hr/>
		
		 <p class="mb-1" id="pError" style="color:red">
			 
		</p>
		</div>
		<hr/>
		 <p class="mb-1" style="font-size:22px;">
			Numerology Number : <span id="spnNumerology"  style="color:blue">----</span>
		</p>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
		$('#divError').hide();
	$('#spnNumerology').html('----');
});
function CalculateMagic()
{
	$('#divError').hide();
	$('#spnNumerology').html('----');
	var mobile = $.trim($('#txtMobile').val());
	if(mobile.length != 10)
	{
		$('#pError').html('Provide 10 digit Mobile Number');
		$('#divError').show();
		return;
	}
	var arr = mobile.split('');
	var magicNumber = 0;
	
	while(arr.length > 1)
	{  
		var  sum = 0;
		arr.forEach((el) => sum += parseInt(el)); 
		magicNumber = sum.toString();
		arr = magicNumber.split('');
	}
	$('#spnNumerology').html(magicNumber);
	if(mobile.indexOf("2") != -1 || mobile.indexOf("4") != -1 || mobile.indexOf("8") != -1 )
	{
		$('#pError').html('Mobile Number must not contain [ 2,4,8 ]');
		$('#divError').show();
		return;
	}
	
	
}
</script>
</body>
</html>
