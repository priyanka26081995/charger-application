<?php

require_once('../dist/lib/config.php');  
if(!isset($_REQUEST['id'] )         )  
{
	ob_clean();
	header('location:./');
	exit();
}
require_once('../dist/lib/functions.php'); 

require_once('../dist/lib/db_class.php');  
require_once('check.php');  
// require_once('../dist/lib/dompdf/vendor/autoload.php');    
require_once('../dist/lib/dompdf/autoload.inc.php');    
  
 

// reference the Dompdf namespace
use Dompdf\Dompdf;
$websiteContent = file_get_contents('https://google.com');
// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($websiteContent);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream(); 
?>