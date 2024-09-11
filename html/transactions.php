<?php
// echo "<h1>SERVICEKEEDA EV IOT</h1>";die(); 
require_once('lib/config.php');  
  require_once('lib/db_class.php');  
// require_once('check.php'); 
 $PAGE_NAME = "Transactions";
 $transaction_array = array();
 
 
 $sql=" SELECT  tstart.transaction_pk, tstart.start_timestamp , tstart.start_value, 
 DATE_FORMAT(tstop.stop_timestamp, '%d-%m-%Y %H:%i:%s') as  stop_timestamp, 
 DATE_FORMAT(tstart.start_timestamp, '%d-%m-%Y %H:%i:%s') as  start_timestamp,
 tstart.id_tag,   tstop.stop_value, tstop.stop_reason ,
 conn.connector_id, conn.charge_box_id
 from transaction_start as tstart 
 join  transaction_stop as tstop on tstart.transaction_pk = tstop.transaction_pk 
 join  connector as conn on tstart.connector_pk = conn.connector_pk 
  where tstart.start_value > 0 and  tstop.stop_value > 0
 order by tstart.start_timestamp desc" ;
			   
$transaction_array =   $db->fetch_array($sql) ; 
// var_dump($sql);
  // var_dump($transaction_array);
  // die(); 
$rate = 22;
$sql=" SELECT     min_charging_rate 
FROM charging_profile  
WHERE   valid_from <= '".CURRENT_DB_DATE."' AND  valid_to  >= '".CURRENT_DB_DATE."'  order by charging_profile_pk desc limit 1 ";
   
$data =   $db->query_first($sql) ; 
// var_dump($sql);
// var_dump($data);die();
if($data != null && count($data) > 0  && !empty($data['min_charging_rate']))
{
	$rate = intval($data['min_charging_rate']);
}
require_once('header.php'); 
?>  
  
        <div class="row">
          <div class="col-12">
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Transaction Filter Form</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
             
			  </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Transaction List</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No.</th>
                    <th>Charger ID</th>
                    <th>Connector ID </th>
                    <th>Tag ID </th>
                    <th>Start-End Time</th>
                    <th>Reading(KW)</th>
                    <th>Amount(Rs.)</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php
					foreach($transaction_array as $transaction)
					{
						  $reading = ($transaction['stop_value']-$transaction['start_value'])/1000;
						?>
						<tr>
							<td><?php echo $transaction['transaction_pk']; ?></td>
							<td><?php echo $transaction['charge_box_id']; ?></td>
							<td><?php echo $transaction['connector_id']; ?></td>
							<td><?php echo $transaction['id_tag']; ?></td>
							<td><?php echo $transaction['start_timestamp'].' - <br/>'.$transaction['stop_timestamp']; ?></td>
							<td><?php echo $reading; ?></td>
							<td><?php echo round($reading*$rate); ?></td>
						</tr>
						<?php
					}  
				  ?>
                  </tbody>
                 </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
		
		
<?php
		
require_once('footer.php'); 
?>
     

<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    // $('#example2').DataTable({
      // "paging": true,
      // "lengthChange": false,
      // "searching": false,
      // "ordering": true,
      // "info": true,
      // "autoWidth": false,
      // "responsive": true,
    // });
  });
</script>	 