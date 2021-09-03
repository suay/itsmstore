<?php
/* 	backend call for extend plan expired and send email invoice
*	method : POST
*	input parameter username = sitename, order_id = order id,invoice = invoice id, email = email
*	return json
*	10-05-2021
*/
require('../fpdf183/fpdf.php');
//require('../fpdf183/WriteHTML.php');
date_default_timezone_set('UTC');

	if( empty($_REQUEST['order_id']) ){
		//$value = array('code'=>'Error','message'=>'Request Customer Name.');
		echo json_encode( array('mscode'=>'Error','message'=>'Request OrderID.') );
		exit;
	}
	if( empty($_REQUEST['invoice_no']) ){
		echo json_encode( array('mscode'=>'Error','message'=>'Request InvoiceID.') );
		exit;
	}
	if( empty($_REQUEST['username']) ){
		echo json_encode( array('mscode'=>'Error','message'=>'Request Username.') );
		exit;
	}
	if( empty($_REQUEST['email']) ){
		echo json_encode( array('mscode'=>'Error','message'=>'Request Email.') );
		exit;
	}



  $servername = "localhost"; // database host
  $username = "root"; //database username
  $password = ""; //databse password
  $db = "itsmstore4"; //database name

// Create connection
  $mysqli = new mysqli($servername, $username, $password,$db);

// Check connection
   if ($mysqli->connect_error) {
   die("Connection failed: " . $mysqli->connect_error);
   }

//Response after the connection is successful

   // echo "Connected successfully";

   $result = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']." and meta_key='status' and meta_value='active'");
//and meta_key='status'
   $ckrow = mysqli_num_rows($result);

   if( $ckrow !== 0){
   	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
   	//stats = active only
   	//find period day,week,month,year
   		$resultperiod = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']."");
   while ($rowck = mysqli_fetch_array($resultperiod, MYSQLI_ASSOC)) {
   		
		if($rowck['meta_key']=='price_time_option'){
			$type_period = $rowck['meta_value'];
			//var_dump($type_period);
			//set find subscription setting
		        switch($type_period){
		            case "days":
		            	$resultday = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']." and meta_key='price_is_per'");
		            	$rowday = mysqli_fetch_array($resultday, MYSQLI_ASSOC);
		            	$price_is_per = $rowday['meta_value'];
		                $valueperday = $price_is_per."days";
		                $setday = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . $valueperday));
		                $nextbill = strtotime($setday);
		            break;
		            case "weeks":
		           		$resultweek = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']." and meta_key='price_is_per'");
		            	$rowweek = mysqli_fetch_array($resultweek, MYSQLI_ASSOC);
		            	$price_is_per = $rowweek['meta_value'];
		                $valueperweek = $price_is_per."week";
		                $setday = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . $valueperweek));
		                $nextbill = strtotime($setday);
		            break;
		            case "months":
			            $resultmonth = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']." and meta_key='price_is_per'");
			            $rowmonth = mysqli_fetch_array($resultmonth, MYSQLI_ASSOC);
			            $price_is_per = $rowmonth['meta_value'];
		                $valuepermonth = $price_is_per."month";
		                
		                $setday = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . $valuepermonth));
		                $nextbill = strtotime($setday);
		            break;
		            case "years":
		            	$resultyear = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']." and meta_key='price_is_per'");
			            $rowyear = mysqli_fetch_array($resultyear, MYSQLI_ASSOC);
			            $price_is_per = $rowyear['meta_value'];
		                $valueperyear = $price_is_per."year";
		                $setday = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . $valueperyear));
		                $nextbill = strtotime($setday);
		            break;
		            
		        }
		    //echo $nextbill;  
		    //update payment_due_date,expire_date
		    $updatenext_pay = "UPDATE wp_postmeta SET meta_value='".$nextbill."' WHERE post_id=".$_REQUEST['order_id']." and meta_key='payment_due_date'";
		    //$rede = mysqli_query($mysqli, "DELETE FROM wp_postmeta WHERE meta_id = '".$row['meta_id']."'");
		    //send email invoice
			 //    if( function_exists( 'YITH_Vendors' ) ){
				//     add_filter( 'woocommerce_email_customer_details_fields', 'yith_woocommerce_email_customer_details_fields', 10, 3 );

				//     if( ! function_exists( 'yith_woocommerce_email_customer_details_fields' ) ){
				//         function yith_woocommerce_email_customer_details_fields( $fields, $sent_to_admin, $order ){
				//             if( $order instanceof WC_Order && wp_get_post_parent_id( $_REQUEST['order_id'] ) != 0 ){
				//                 $fields_to_remove = array( 'billing_email', 'billing_phone' );
				//                 foreach( $fields_to_remove as $to_remove ){
				//                     if( isset( $fields[ $to_remove ] ) ){
				//                         unset( $fields[ $to_remove ] );
				//                     }
				//                 }
				//             }
				//             return $fields;
				//         }
				//     }
				// }
		    $pdf=new FPDF();
		    $pdf->AddPage();
$pdf->SetFont('Arial','B',15);
$pdf->Cell(0,20,iconv( 'UTF-8','TIS-620','Invoice'),0,1,'C');
$pdf->Cell(1, 5 ,"Netka System");
$pdf->SetFont('Arial','',10);
$pdf->Cell(1, 15, "1 Soi Ramkhamhaeng 166 Yaek 2");
$pdf->Cell(1, 25, "Ramkhamhaeng Rd. Khwang Minburi, ");
$pdf->Cell(1, 35, "Khet Minburi, Bangkok Thailand 10510");
$pdf->Cell(1, 45, "Tel :+662-978-6805 Fax +662-978-6909");

//find userid
$resultuser = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$_REQUEST['order_id']." and meta_key='user_id'");
$rowuserid = mysqli_fetch_array($resultuser, MYSQLI_ASSOC);
$userid = $rowuserid['meta_value'];
//find bill address
$resultbill = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_usermeta where user_id=".$userid." and meta_key in ('billing_first_name','billing_last_name','billing_address_1','billing_city','billing_state','billing_postcode','billing_country','billing_email','billing_phone')");
//$rowbill = mysqli_fetch_array($resultbill, MYSQLI_ASSOC);
while ($rowbill = mysqli_fetch_array($resultbill, MYSQLI_ASSOC)) {
	//$pdf->Cell(50, 3, $rowbill);
	var_dump($rowbill['meta_key'],$rowbill['meta_value']);
}


// === logo

$pdf->Image('http://localhost:8000/itsmstore4/wp-content/uploads/2021/05/logo-bill-01.png',150,30,45,0,'PNG');;
// $pdf->Cell(30,3,"ธรณีนี่นี้เป็นพยาน",1,"L");
// $pdf->Cell(30,3,"ธรณีนี่นี้เป็นพยาน",1,"L");
// $pdf->ln(11);
// $pdf->SetFillColor(255,255,0);
// $pdf->Rect(10, 40, 40, 10,'F');
// $pdf->Rect(50, 40, 40, 10);
// $pdf->Rect(90, 40, 40, 10);
// $pdf->SetFont('Arial','',10);
// ===
// $pdf->Cell(50, 3 ,"ธรณีนี่นี้ เป็นพยาน เราก็ศิษย์มีอาจารย์ ");
// $pdf->Cell(30,3,"ธรณีนี่นี้เป็นพยาน",0,'L');
// $pdf->Cell(30,3,'ธรณีนี่นี้เป็นพยาน',0,'L');
// $pdf->ln();
// $pdf->Cell(50, 3 ,'หนึ่งบ้าง เราผิดท่านประหาร เราชอบ');
// $pdf->ln();
// $pdf->Cell(50, 3 ,'เราบ่ผิดท่านมล้าง ดาบนั้น คืนสนอง');
// $pdf->ln(5);
// ===

$f = time();
$pdf->Output("$f.pdf","F");
echo "<a href='$f.pdf'>here</a>";

		}

	}
  // exit;
   	// echo json_encode( array('mscode'=>'Success','message'=>$message) );
   	
   }else{
   	 echo json_encode( array('mscode'=>'Error','message'=>'status not active.') );
   }
   

?>