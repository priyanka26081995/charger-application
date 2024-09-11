</div>
<style>
.col-sk {
    -ms-flex-preferred-size: 0 !important;
    flex-basis: 0 !important;
    -ms-flex-positive: 1 !important;
    flex-grow: 1 !important;
    max-width: 100%  !important;  
	cursor:pointer;
}
</style>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php
	$show_footer = true;
	if(isset($_SESSION['IS_APP']))    
	{ 
		$show_footer = ($_SESSION['IS_APP'] == "1" ? false : true); 
	}
	if($show_footer)
	{
  ?>
	<div id="divFooter" class="row btn-dark" style="position:fixed;bottom:0;z-index:9999;width:100%;margin-bottom:-10px">
								 <div class="col-sm col-sk page-header text-center btn-dark" style="cursor:pointer;height:80px;margin: 0;padding: 0">
			  <a type="button" href="index.php" class="btn btn-dark  btn-block fs-24"  >
			 <i class="fa fa-home"></i><br/>Home
			</a>
			</div>
			 <div class=" col-sm col-sk  page-header text-center btn-dark" style="cursor:pointer;height:80px;margin: 0;padding: 0">
							 
			   <a type="button" href="pending_charging.php" class="btn btn-dark  btn-block fs-24"  >
			<i class="fa fa-wallet"></i><br/>Charging List
			</a>
			
			</div>
			 <div class="col-sm  col-sk  page-header text-center btn-dark" style="cursor:pointer;height:80px;margin: 0;padding: 0">
							 
			  <a type="button" href="profile.php" class="btn btn-dark  btn-block fs-24"  >
			 <i class="fa fa-user"></i><br/>My Profile
			</a>
)         
						</div>
		
		<!-- /.col-lg-12 -->
	</div>
	<?php
	}
	?>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- Toastr -->
<script src="../plugins/toastr/toastr.min.js"></script>
<!-- SweetAlert2 -->
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>

<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/sktech.js"></script>
<script> 
window.current_latitude =   19.05123200;
window.current_longitude  =   73.07667100;
function ReloadSKPage()
{ 
  window.location.href = window.location.href;
}
function setCurrentLocation(latValue, lngValue)
{
	window.current_latitude = parseFloat(latValue);
	window.current_longitude = parseFloat(lngValue);
	// window.current_latitude = window.current_latitude.toFixed(8);
	// window.current_longitude = window.current_longitude.toFixed(8);
	// alert(latValue + ' - ' + lngValue);
	// alert(window.current_latitude + ' - ' +  window.current_longitude);
	
}

function ShowToastSK(toastTitle, toastMessage, toastIcon = 'info')
{   
	Swal.fire({
	// toast: true,
      // position: 'bottom',
      // showConfirmButton: false,
      timer: 3000,
	  title: toastTitle,
	  text: toastMessage,
	  icon: toastIcon
	});
	// $(document).Toasts('create', {
		// class: 'bg-success',
		// title: toastTitle,
		// autohide: true,
		// delay: 1500,
		// position: 'bottomCenter',
		// body: toastMessage
	  // }) 
} 

function ShowToastSKWallet(toastTitle, toastMessage, toastIcon = 'info', amount = 0)
{   
	Swal.fire({  
	  title: toastTitle,
	  text: toastMessage,
	  icon: toastIcon,
	  showDenyButton: true,
  showCancelButton: false,
  confirmButtonText: "Yes",
  denyButtonText: 'No',
	}).then((result) => {
  /* Read more about isConfirmed, isDenied below */
  if (result.isConfirmed) {
     window.location = "add_credit.php?amount=" + amount;
  }  
});
} 

</script> 
</body>
</html>
