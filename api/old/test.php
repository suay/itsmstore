<?php
/* 	backend call for extend plan expired and send email invoice
*	method : POST
*	input parameter username = sitename, order_id = order id,invoice = invoice id, email = email
*	return json
*	10-05-2021
*/
require('../fpdf183/WriteHTML.php');
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

//find order_id is subscription
   $resultfind_subid = mysqli_query($mysqli, "SELECT order_item_name,meta_key,meta_value FROM wp_woocommerce_order_items a left join wp_woocommerce_order_itemmeta b on(a.order_item_id=b.order_item_id) where a.order_id='".$_REQUEST['order_id']."' and meta_key='_subscription_id'");
   $rowsubid = mysqli_fetch_array($resultfind_subid, MYSQLI_ASSOC);
   $order_subscription = $rowsubid['meta_value'];
   //var_dump($rowsubid['meta_value']);exit;

   $result = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$order_subscription." and meta_key='status' and meta_value='active'");
//and meta_key='status'
   $ckrow = mysqli_num_rows($result);

    if( $ckrow !== 0){
	   	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	   	//stats = active only
	   	//find period day,week,month,year
	   	$resultperiod = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$order_subscription."");
    while ($rowck = mysqli_fetch_array($resultperiod, MYSQLI_ASSOC)) {
   		
		if($rowck['meta_key']=='price_time_option'){
			$type_period = $rowck['meta_value'];
			//var_dump($type_period);exit;
			//set find subscription setting
		        switch($type_period){
		            case "days":
		            	$resultday = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$order_subscription." and meta_key='price_is_per'");
		            	$rowday = mysqli_fetch_array($resultday, MYSQLI_ASSOC);
		            	$price_is_per = $rowday['meta_value'];
		                $valueperday = $price_is_per."days";
		                $setday = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . $valueperday));
		                $nextbill = strtotime($setday);
		                $nextbill_format = date('F j, Y',$nextbill);
		            break;
		            case "weeks":
		           		$resultweek = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$order_subscription." and meta_key='price_is_per'");
		            	$rowweek = mysqli_fetch_array($resultweek, MYSQLI_ASSOC);
		            	$price_is_per = $rowweek['meta_value'];
		                $valueperweek = $price_is_per."week";
		                $setday = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . $valueperweek));
		                $nextbill = strtotime($setday);
		                $nextbill_format = date('F j, Y',$nextbill);
		            break;
		            case "months":
			            $resultmonth = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$order_subscription." and meta_key='price_is_per'");
			            $rowmonth = mysqli_fetch_array($resultmonth, MYSQLI_ASSOC);
			            $price_is_per = $rowmonth['meta_value'];
		                $valuepermonth = $price_is_per."month";
		                $setday = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . $valuepermonth));
		                $nextbill = strtotime($setday);
		                $nextbill_format = date('F j, Y',$nextbill);
		            break;
		            case "years":
		            	$resultyear = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id=".$order_subscription." and meta_key='price_is_per'");
			            $rowyear = mysqli_fetch_array($resultyear, MYSQLI_ASSOC);
			            $price_is_per = $rowyear['meta_value'];
		                $valueperyear = $price_is_per."year";
		                $setday = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . $valueperyear));
		                $nextbill = strtotime($setday);
		                $nextbill_format = date('F j, Y',$nextbill);
		            break;
		            
		        }
		    //echo $nextbill;  
		    //update payment_due_date,expire_date
		    $updatenext_pay = "UPDATE wp_postmeta SET meta_value='".$nextbill."' WHERE post_id=".$order_subscription." and meta_key='payment_due_date'";
		    //$rede = mysqli_query($mysqli, "DELETE FROM wp_postmeta WHERE meta_id = '".$row['meta_id']."'");
		    
	

			$resultorder = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_woocommerce_order_items a left join wp_woocommerce_order_itemmeta b on(a.order_item_id=b.order_item_id) where a.order_id='".$_REQUEST['order_id']."'");
			$resultData = array();
			while ($roworder = mysqli_fetch_array($resultorder, MYSQLI_ASSOC)) {
				//var_dump($roworder['meta_key'],$roworder['meta_value']);
				$detail_order[$roworder['meta_key']] = $roworder['meta_value'];
				//_subscription_id,_product_id,_line_total,_qty,_line_subtotal	
			}
			//array_push($resultData,$detail_order);
			// var_dump($detail_order);exit;
			//find product name
			$resultnameproduct = mysqli_query($mysqli, "SELECT * FROM wp_woocommerce_order_items where order_id='".$_REQUEST['order_id']."'");
			$rownameproduct = mysqli_fetch_array($resultnameproduct, MYSQLI_ASSOC);
			foreach ($rownameproduct as $key => $value) {
				$nameproduct[$key] = $value;
			}
			// var_dump($resultData['']);exit;
			// $producname = $nameproduct['order_item_name'];	


			//find curency,payment method
			$resultcurrency = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_postmeta where post_id='".$_REQUEST['order_id']."'");
			while ($rowcurency = mysqli_fetch_array($resultcurrency, MYSQLI_ASSOC)) {
				//var_dump($roworder['meta_key'],$roworder['meta_value']);
				$currencys[$rowcurency['meta_key']] = $rowcurency['meta_value'];
			}
			//array_push($detail_order,$currencys);
			//var_dump($currencys['_payment_method']);exit;

			//find bill address
			$resultuser = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id='".$order_subscription."' and meta_key='user_id'");
			$rowuserid = mysqli_fetch_array($resultuser, MYSQLI_ASSOC);
			$userid = $rowuserid['meta_value'];
			//find bill address
			$resultbill = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_usermeta where user_id='".$userid."' and meta_key like 'bill%' order by meta_key ASC");

			while ($rowbill = mysqli_fetch_array($resultbill, MYSQLI_ASSOC)) {
				//var_dump($rowbill['meta_key'],$rowbill['meta_value']);
				$bill_customer[$rowbill['meta_key']] = $rowbill['meta_value'];
			}

			// exit;
			//create pdf
			$pdf=new PDF_HTML();
			$pdf->AddPage();//หาก เราใช้คำสั่ง  $pdf->AddPage("l"); จะได้ pdf ขนาด A4 เป็น แนวนอน
			$pdf->SetFont('Arial','B','15');
			$pdf->Cell(0,0,iconv( 'UTF-8','cp874','Invoice'),0,1,'C');
			$pdf->SetFont('Arial','B',15);
			// $pdf->Image('http://localhost:8000/itsmstore4/wp-content/uploads/2021/05/logo-bill-01.png',150,20,40,25);
			$pdf->setXY( 20, 20 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Netka System' ) );
			$pdf->SetFont('Arial','',10);
			$pdf->setXY( 20, 30 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , '1 Soi Ramkhamhaeng 166 Yaek 2' ) );
			// $pdf->setXY( 135, 20 );
			// $pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'date ' ). date('d').'/'. date('m').'/'.( date('Y')+543 ).'' );
			// $pdf->setXY( 180, 20 );
			// $pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'page 1' ) );

			// $pdf->setXY(135,20);
			$pdf->Image('http://localhost:8000/itsmstore4/wp-content/uploads/2021/05/logo-bill-01.png',150,20,40,25);

			$pdf->setXY( 20, 35 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Ramkhamhaeng Rd. Khwang Minburi,' ) );
			$pdf->setXY( 20, 40 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Khet Minburi, Bangkok Thailand 10510' ) );
			$pdf->setXY( 20, 45 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Tel :+662-978-6805 Fax +662-978-6909') );
			$pdf->SetDrawColor(224, 224, 224);
			$pdf->setXY( 20, 50 );
			$pdf->WriteHTML('<hr>');

			$pdf->setXY( 20, 60 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_first_name'].' '.$bill_customer['billing_last_name']) );
			if( !empty($bill_customer['billing_company']) ){
			$pdf->setXY( 20, 65 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_company']) );
			}else{
			$pdf->setXY( 20, 65 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_address_1']) );	
			}
			$pdf->setXY( 20, 70 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_city']) );
			$pdf->setXY( 20, 75 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , checkstate($bill_customer['billing_state'])) );
			$pdf->setXY( 20, 80 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , checkstate($bill_customer['billing_country'])) );
			$pdf->setXY( 20, 85 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_postcode']) );
			$pdf->setXY( 20, 90 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_email']) );
			$pdf->setXY( 20, 95 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_phone']) );
			if( !empty($bill_customer['billing_vat_number']) ){
			$pdf->setXY( 20, 100 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_vat_number']) );
			}
			if( !empty($bill_customer['billing_vat_ssn']) ){
			$pdf->setXY( 20, 105 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $bill_customer['billing_vat_ssn']) );
			}

			//set invoice number
			$pdf->SetFont('Arial','',10);
			$pdf->setXY( 145, 60 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'INVOICE NUMBER ' ) );
			$pdf->setXY( 150, 65 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $_REQUEST['invoice_no'] ) );

			$pdf->setXY( 135, 75 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Order No.' ) );
			$pdf->setXY( 185, 75 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $_REQUEST['order_id'] ) );

			$pdf->setXY( 135, 80 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Invoice Date' ) );
			$pdf->setXY( 175, 80 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , date('d/m/Y') ) );

			$pdf->setXY( 135, 85 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Amount' ) );
			$pdf->setXY( 165, 85 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $currencys['_order_currency'].' '.number_format($detail_order['_line_total'],2) ) );
			// $pdf->setXY( 178, 85 );
			// $pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , number_format($detail_order['_line_total'],2) ) );

			//head title
			$pdf->setXY( 20, 105 );
			$pdf->SetDrawColor(224, 224, 224);
			$pdf->SetFillColor(51, 51, 225);
			//$pdf->SetTextColor(255, 255, 255);
			$pdf->WriteHTML('<hr>');
			$pdf->setXY( 65, 115 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Product' ) );
			$pdf->setXY( 135, 115 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Qty' ) );
			$pdf->setXY( 165, 115 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Product Price' ) );

			$pdf->setXY( 20, 120 );
			$pdf->SetDrawColor(224, 224, 224);
			$pdf->WriteHTML('<hr>');

			$pdf->setXY( 65, 130 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $nameproduct['order_item_name'] ) );
			$pdf->setXY( 137, 130 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $detail_order['_qty'] ) );
			$pdf->setXY( 170, 130 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $currencys['_order_currency'].' '.number_format($detail_order['_line_subtotal'],2) ) );

			$pdf->setXY( 135, 150 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Subtotal' ) );
			$pdf->setXY( 170, 150 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $currencys['_order_currency'].' '.number_format($detail_order['_line_subtotal'],2) ) );


			$pdf->setXY( 135, 160 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Total' ) );
			$pdf->setXY( 170, 160 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $currencys['_order_currency'].' '.number_format($detail_order['_line_total'],2) ) );


			$pdf->SetFont('Arial','B',10);
			$pdf->setXY( 20, 215 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Note' ) );
			$pdf->SetFont('Arial','',10);
			$pdf->setXY( 23, 220 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Payment Method : '.$currencys['_payment_method']) );
			$pdf->setXY( 23, 225 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Next Billing : '.$nextbill_format) );
			$pdf->setXY( 23, 230 );
			$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Expired On : '.$nextbill_format) );


			$pdf->Ln(15);
			//$pdf->FancyTable($header,$detail_order);
			$pdf->Ln();

			//$pdf->Output();//สั่งพิมพ์ pdf

				//send email invoice
				$to = "wattawadee@netkasystem.com";
		        $from = "atnetkasystem@gmail.com";
		        $subject = "the subject attachment";  
				$message = "<p>Please see the attachment.</p>";
		        $separator = md5(time());
		        $eol = PHP_EOL;
		        $filename = "invoice.pdf";
		        $pdfdoc = $pdf->Output('', 'S');
		        $attachment = chunk_split(base64_encode($pdfdoc));




		        $headers = "From: " . $from . $eol;
		        $headers .= "MIME-Version: 1.0" . $eol;
		        $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol . $eol;

		        $body = '';

		        $body .= "Content-Transfer-Encoding: 7bit" . $eol;
		        $body .= "This is a MIME encoded message." . $eol; //had one more .$eol


		        $body .= "--" . $separator . $eol;
		        $body .= "Content-Type: text/html; charset=\"iso-8859-1\"" . $eol;
		        $body .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
		        $body .= $message . $eol;


		        $body .= "--" . $separator . $eol;
		        $body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $eol;
		        $body .= "Content-Transfer-Encoding: base64" . $eol;
		        $body .= "Content-Disposition: attachment" . $eol . $eol;
		        $body .= $attachment . $eol;
		        $body .= "--" . $separator . "--";

		         if (mail($to, $subject, $body, $headers)) {

		            $message = 'Mail Send Successfully';
		        } else {

		            $message = 'Main not send';
		        }
			
			


		}//end if price option

	}//endwhile
  // exit;
   	echo json_encode( array('mscode'=>'Success','message'=>$message) );
   	
   }else{
   	 echo json_encode( array('mscode'=>'Error','message'=>'status not active.') );
   }
   
function checkstate($codestate){
	switch($codestate){
		case "TH":
			return "Thailand";
		break;
		case "TH-10":
			return "Bangkok";
		break;
		case "TH-37":
			return "Amnat Charoen";
		break;
		case "TH-15":
			return "Ang Thong";
		break;
		case "TH-14":
			return "Ayutthaya";
		break;
		case "TH-38":
			return "Bueng Kan";
		break;
		case "TH-31":
			return "Buri Ram";
		break;
		case "TH-24":
			return "Chachoengsao";
		break;
		case "TH-18":
			return "Chai Nat";
		break;
		case "TH-36":
			return "Chaiyaphum";
		break;
		case "TH-22":
			return "Chanthaburi";
		break;
		case "TH-50":
			return "Chiang Mai";
		break;
		case "TH-57":
			return "Chiang Rai";
		break;
		case "TH-20":
			return "Chonburi";
		break;
		case "TH-86":
			return "Chumphon";
		break;
		case "TH-46":
			return "Kalasin";
		break;
		case "TH-62":
			return "Kamphaeng Phet";
		break;
		case "TH-71":
			return "Kanchanaburi";
		break;
		case "TH-40":
			return "Khon Kaen";
		break;
		case "TH-81":
			return "Krabi";
		break;
		case "TH-52":
			return "Lampang";
		break;
		case "TH-51":
			return "Lamphun";
		break;
		case "TH-42":
			return "Loei";
		break;
		case "TH-16":
			return "Lopburi";
		break;
		case "TH-58":
			return "Mae Hong Son";
		break;
		case "TH-44":
			return "Maha Sarakham";
		break;
		case "TH-49":
			return "Mukdahan";
		break;
		case "TH-26":
			return "Nakhon Nayok";
		break;
		case "TH-73":
			return "Nakhon Pathom";
		break;
		case "TH-48":
			return "Nakhon Phanom";
		break;
		case "TH-30":
			return "Nakhon Ratchasima";
		break;
		case "TH-60":
			return "Nakhon Sawan";
		break;
		case "TH-80":
			return "Nakhon Si Thammarat";
		break;
		case "TH-55":
			return "Nan";
		break;
		case "TH-96":
			return "Narathiwat";
		break;
		case "TH-39":
			return "Nong Bua Lam Phu";
		break;
		case "TH-43":
			return "Nong Khai";
		break;
		case "TH-12":
			return "Nonthaburi";
		break;
		case "TH-13":
			return "Pathum Thani";
		break;
		case "TH-94":
			return "Pattani";
		break;
		case "TH-82":
			return "Phang Nga";
		break;
		case "TH-93":
			return "Phatthalung";
		break;
		case "TH-56":
			return "Phayao";
		break;
		case "TH-67";
			return "Phetchabun";
		break;
		case "TH-76":
			return "Phetchaburi";
		break;
		case "TH-66":
			return "Phichit";
		break;
		case "TH-65":
			return "Phitsanulok";
		break;
		case "TH-54":
			return "Phrae";
		break;
		case "TH-83":
			return "Phuket";
		break;
		case "TH-25":
			return "Prachin Buri";
		break;
		case "TH-77":
			return "Prachuap Khiri Khan";
		break;
		case "TH-85":
			return "Ranong";
		break;
		case "TH-70":
			return "Ratchaburi";
		break;
		case "TH-21":
			return "Rayong";
		break;
		case "TH-45":
			return "Roi Et";
		break;
		case "TH-27":
			return "Sa Kaeo";
		break;
		case "TH-47":
			return "Sakon Nakhon";
		break;
		case "TH-11":
			return "Samut Prakan";
		break;
		case "TH-74":
			return "Samut Sakhon";
		break;
		case "TH-75":
			return "Samut Songkhram";
		break;
		case "TH-19":
			return "Saraburi";
		break;
		case "TH-91":
			return "Satun";
		break;
		case "TH-17":
			return "Sing Buri";
		break;
		case "TH-33":
			return "Sisaket";
		break;
		case "TH-90":
			return "Songkhla";
		break;
		case "TH-64":
			return "Sukhothai";
		break;
		case "TH-72":
			return "Suphan Buri";
		break;
		case "TH-84":
			return "Surat Thani ";
		break;
		case "TH-32":
			return "Surin";
		break;
		case "TH-63":
			return "Tak";
		break;
		case "TH-92":
			return "Trang";
		break;
		case "TH-23";
			return "Trat";
		break;
		case "TH-34":
			return "Ubon Ratchathani";
		break;
		case "TH-41":
			return "Udon Thani";
		break;
		case "TH-61":
			return "Uthai Thani ";
		break;
		case "TH-53":
			return "Uttaradit";
		break;
		case "TH-95":
			return "Yala";
		break;
		case "TH-35":
			return "Yasothon";
		break;
	}
}

?>