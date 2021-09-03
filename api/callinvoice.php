<?php

/* 	backend call for extend plan expired and send email invoice
*	method : POST
*	input parameter username = sitename, order_id = order id,invoice = invoice id, email = email
*	return json
*	10-05-2021
*/
ob_start();
require('../fpdf17/fpdf.php');
define('FPDF_FONTPATH','../fpdf17/font/');

// date_default_timezone_set('UTC');
//edit 21-06-2021 timezone
date_default_timezone_set("Asia/Bangkok");

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

$namecustomer = $_REQUEST['username'];

			$received_number = $_REQUEST['invoice_no'];

		//find order_id is subscription
	   $resultfind_subid = mysqli_query($mysqli, "SELECT order_item_name,meta_key,meta_value FROM wp_woocommerce_order_items a left join wp_woocommerce_order_itemmeta b on(a.order_item_id=b.order_item_id) where a.order_id='".$_REQUEST['order_id']."' and meta_key='_subscription_id'");
	   $rowsubid = mysqli_fetch_array($resultfind_subid, MYSQLI_ASSOC);
	   $order_subscription = $rowsubid['meta_value'];
	   // var_dump($rowsubid['meta_value']);exit;

	   $result = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$order_subscription." and meta_key='status' and meta_value='active'");
	//and meta_key='status'
	   // $sql = "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$order_subscription." and meta_key='status' and meta_value='active'";
	   // var_dump($sql);exit;
	   $ckrow = mysqli_num_rows($result);

	    if( $ckrow !== 0){
		   	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		   	//stats = active only
		   	//find period day,week,month,year
		   	$resultperiod = mysqli_query($mysqli, "SELECT meta_key,meta_value FROM wp_postmeta where post_id=".$order_subscription." and meta_key ='price_time_option' ");
	   		$rowck = mysqli_fetch_array($resultperiod, MYSQLI_ASSOC);
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
			   	
			   	//insert log expirce
			    $insertlog = "INSERT INTO log_expirce_next (id_logexpirce, date_expirce,dateex_format) VALUES('','".$nextbill."','".$setday."')";
			    mysqli_query($mysqli, $insertlog);

			    //update payment_due_date,expire_date
			    $updatenext_pay = "UPDATE wp_postmeta SET meta_value='".$nextbill."' WHERE post_id=".$order_subscription." and meta_key='payment_due_date'";
			    $rede = mysqli_query($mysqli, $updatenext_pay);
			    // $updateexpired = "UPDATE wp_postmeta SET meta_value='".$nextbill."' WHERE post_id=".$order_subscription." and meta_key='expired_date'";
			    // $reex = mysqli_query($mysqli, $updateexpired);
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

				$full_state_contry = checkstate($bill_customer['billing_state'],$bill_customer['billing_country']);

				//find vat location thailand only
				$resultuser = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta where post_id='".$order_subscription."' and meta_key='user_id'");
	 
				// Create PDF 
				$pdf=new FPDF();
				$pdf->AddPage();
				$pdf->AddFont('tahoma','','tahoma.php');
				$pdf->AddFont('tahomab','','tahomab.php');
				
				$pdf->SetFont('tahomab','','15');
				$pdf->Cell(0,0,iconv( 'UTF-8','cp874','Receipt'),0,1,'C');

				$pdf->setXY( 20, 20 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , 'Netka System' ) );

				$pdf->SetFont('tahoma','','10');
				$pdf->setXY( 20, 30 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , '1 Soi Ramkhamhaeng 166 Yaek 2' ) );

				//logo
				$pdf->Image('http://localhost:8000/itsmstore4/wp-content/uploads/2021/05/logo-bill-01.png',150,20,40,25);

				$pdf->setXY( 20, 35 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , 'Ramkhamhaeng Rd. Khwang Minburi,' ) );
				$pdf->setXY( 20, 40 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , 'Khet Minburi, Bangkok Thailand 10510' ) );
				$pdf->setXY( 20, 45 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , 'Tel :+662-978-6805 Fax +662-978-6909') );

				$pdf -> Line(20, 55, 200, 55);

				$pdf->setXY( 20, 60 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , $bill_customer['billing_first_name'].' '.$bill_customer['billing_last_name']) );
				//$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620','สวัสดี ชาวไทยครีเอท') );
				if( !empty($bill_customer['billing_company']) ){
				$pdf->setXY( 20, 65 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , $bill_customer['billing_company']) );
				}else{
				$pdf->setXY( 20, 65 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , $bill_customer['billing_address_1']) );	
				}
				$pdf->setXY( 20, 70 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , $bill_customer['billing_city']) );
				$pdf->setXY( 20, 75 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , $full_state_contry[1]) );
				$pdf->setXY( 20, 80 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , $full_state_contry[0]) );
				$pdf->setXY( 20, 85 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , $bill_customer['billing_postcode']) );
				$pdf->setXY( 20, 90 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','TIS-620' , $bill_customer['billing_email']) );
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
				$pdf->setXY( 145, 60 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'RECEIPT NUMBER ' ) ); //INVOICE NUMBER
				$pdf->setXY( 150, 65 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $received_number ) );

				$pdf->setXY( 135, 75 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Order No.' ) );
				$pdf->setXY( 185, 75 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $_REQUEST['order_id'] ) );

				$pdf->setXY( 135, 80 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Receipt Date' ) ); //Invoice Date
				$pdf->setXY( 175, 80 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , date('d/m/Y') ) );

				$pdf->setXY( 135, 85 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Amount' ) );
				$pdf->setXY( 165, 85 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $currencys['_order_currency'].' '.number_format($detail_order['_line_total'],2) ) );

				//head title
				$pdf->setXY( 20, 105 );
				//$pdf -> Line(20, 105, 200, 105);

			    // Background color
			    $pdf->SetFillColor(240, 240, 245);
			    // Title
			    $pdf->Cell(180,20,iconv( 'UTF-8','cp874' , 'Product                                                                                             Qty                           Product Price' ),0,1,'L',true);

			  $pdf->setXY( 20, 120 );
				$pdf->SetFillColor(224, 224, 224);
				$pdf -> Line(20, 125, 200, 125);

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

				if( !empty($detail_order['_line_subtotal_tax']) && $bill_customer['billing_country']=="TH" ){

					$pdf->setXY( 135, 160 );
					$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Tax' ) );
					$pdf->setXY( 170, 160 );
					$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $currencys['_order_currency'].' '.number_format($$detail['_line_subtotal_tax'],2) ) );

					$pdf->setXY( 135, 170 );
					$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Total' ) );
					$pdf->setXY( 170, 170 );
					$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $currencys['_order_currency'].' '.number_format($detail_order['_line_total'],2) ) );

				}else{

					$pdf->setXY( 135, 160 );
					$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Total' ) );
					$pdf->setXY( 170, 160 );
					$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , $currencys['_order_currency'].' '.number_format($detail_order['_line_total'],2) ) );
				}

				


				$pdf->SetFont('tahomab','',10);
				$pdf->setXY( 20, 215 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Note' ) );
				$pdf->SetFont('tahoma','',10);
				$pdf->setXY( 23, 220 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Payment Method : '.$currencys['_payment_method']) );
				$pdf->setXY( 23, 225 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Next Billing : '.$nextbill_format) );
				$pdf->setXY( 23, 230 );
				$pdf->MultiCell( 0 , 0 , iconv( 'UTF-8','cp874' , 'Expired On : '.$nextbill_format) );


				$pdf->Ln(16);

				$pdf->Output();
				exit;
				$pdfdoc = $pdf->Output('save/Receipt.pdf', "F");
				// call function send email 
			    $filename  = "Receipt.pdf";
				$path      = "save/";
				$file      = $path . $filename;
				$file_size = filesize($file);
				$handle    = fopen($file, "r");
				$content   = fread($handle, $file_size);
				fclose($handle);

				$content = chunk_split(base64_encode($content));
				$uid     = md5(uniqid(time()));
				$name    = basename($file);

				$eol     = PHP_EOL;
				$subject = "RECEIPT FOR YOUR PAYMENT TO Netka System Co., Ltd.";
				// $message = '<h3>You made a payment to Netka System Co., Ltd.</h3> 
				// 			<p>Please see the Attachment Receipt.</p>';
				$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
				$message .= '<html><body>';
				$message .= '<body style="width:100%;font-family:arial, helvetica neue, helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0"> 
	    <div class="es-wrapper-color" style="background-color: #f6f6f6;">
	      <table class="es-wrapper" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px; padding: 0; margin: 0; width: 100%; height: 100%; background-repeat: repeat; background-position: center top;" width="100%" cellspacing="0" cellpadding="0">
	        <tbody>
	          <tr>
	            <td style="padding: 0; margin: 0;" valign="top">
	              <table class="es-header" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px; table-layout: fixed !important; width: 100%; background-color: transparent; background-repeat: repeat; background-position: center top;" cellspacing="0" cellpadding="0" align="center">
	                <tbody>
	                  <tr>
	                    <td style="padding: 0; margin: 0;" align="center">
	                      <table class="es-header-body" style="border-collapse: collapse; border-spacing: 0px; background-color: #ffffff; width: 600px; height: 625px;" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">
	                        <tbody>
	                          <tr style="height: 59px;">
	                            <td style="padding: 20px 20px 0px; margin: 0px; height: 59px; width: 516.4px;" align="left">
	                              <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" width="100%" cellspacing="0" cellpadding="0">
	                                <tbody>
	                                  <tr>
	                                    <td style="padding: 0; margin: 0; width: 560px;" align="center" valign="top">
	                                      <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" role="presentation" width="100%" cellspacing="0" cellpadding="0">
	                                        <tbody>
	                                          <tr>
	                                            <td style="padding: 0; margin: 0; font-size: 0px;" align="center"><img class="adapt-img" style="display: block; border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;" src="https://netkasystem.com/itsmstore/wp-content/uploads/2021/03/headermail-01.jpg" alt="" width="560" /></td>
	                                          </tr>
	                                        </tbody>
	                                      </table>
	                                    </td>
	                                  </tr>
	                                </tbody>
	                              </table>';
				$message .= '</td>
	                          </tr>
	                          <tr style="height: 116px;">
	                            <td style="padding: 20px 20px 0px; margin: 0px; height: 116px; width: 516.4px;" align="left">
	                              <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" width="100%" cellspacing="0" cellpadding="0">
	                                <tbody>
	                                  <tr>
	                                    <td style="padding: 0; margin: 0; width: 560px;" align="center" valign="top">
	                                      <table style="border-collapse: collapse; border-spacing: 0px; height: 72px; width: 100%;" role="presentation" width="100%" cellspacing="0" cellpadding="0">
	                                        <tbody>
	                                          <tr style="height: 72px;">
	                                            <td style="padding: 20px 0px 20px 15px; margin: 0px; height: 72px;" align="left" bgcolor="#f0585d">
	                                              <h1 style="margin: 0px; line-height: 36px; font-family: arial, helvetica neue, helvetica, sans-serif; font-size: 30px; font-style: normal; font-weight: normal; color: #ffffff; text-align: center;">Welcome to NetkaQuartz Service Desk X on Cloud</h1>
	                                            </td>
	                                          </tr>
	                                        </tbody>
	                                      </table>
	                                    </td>
	                                  </tr>
	                                </tbody>
	                              </table>
	                            </td>
	                          </tr>
	                          <tr style="height: 450px;">
	                            <td style="padding: 20px 20px 0px; margin: 0px; height: 450px; width: 516.4px;" align="left">
	                              <table style="border-collapse: collapse; border-spacing: 0px; height: 393px; width: 100%;" width="100%" cellspacing="0" cellpadding="0">
	                                <tbody>
	                                  <tr style="height: 393px;">
	                                    <td style="padding: 0px; margin: 0px; width: 560px; height: 393px;" align="center" valign="top">
	                                      <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" role="presentation" width="100%" cellspacing="0" cellpadding="0">
	                                        <tbody>
	                                          <tr>
	                                            <td style="padding: 0; margin: 0;" align="left">
	                                              <p style="margin: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; mso-line-height-rule: exactly; font-family: arial, helvetica neue, helvetica, sans-serif; line-height: 28px; color: #333333; font-size: 14px;">Hi '.$namecustomer.',</p>
	                                              <p>Thank you for your purchase. Your NSDX on Cloud has been successfully created and now is ready to use.</p>
	                                              <p>Please access the receipt in the attachment :</p>
	                                              <p style="margin: 0; mso-line-height-rule: exactly; font-family: arial, helvetica neue, helvetica, sans-serif;">&nbsp;</p>
	                                              <p style="margin: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; mso-line-height-rule: exactly; font-family: arial, helvetica neue, helvetica, sans-serif; line-height: 28px; color: #333333; font-size: 14px;">if you have any questions.Please feel free to contact us.</p>
	                                              <p style="margin: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; mso-line-height-rule: exactly; font-family: arial, helvetica neue, helvetica, sans-serif; line-height: 28px; color: #333333; font-size: 14px;"><br /><br /><br /><br /><strong>Best Regards,<br />NSDX Online store</strong><br />email: support@netkasystem.com</p>
	                                            </td>
	                                          </tr>
	                                        </tbody>
	                                      </table>';	
				$message .= '</td>
	                                  </tr>
	                                </tbody>
	                              </table>
	                            </td>
	                          </tr>
	                        </tbody>
	                      </table>
	                    </td>
	                  </tr>
	                </tbody>
	              </table>
	              <table class="es-content" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px; table-layout: fixed !important; width: 100%;" cellspacing="0" cellpadding="0" align="center">
	                <tbody>
	                  <tr>
	                    <td style="padding: 0; margin: 0;" align="center">
	                      <table class="es-content-body" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px; background-color: #ffffff; width: 600px;" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">
	                        <tbody>
	                          <tr>
	                            <td style="padding: 0; margin: 0; padding-top: 20px; padding-left: 20px; padding-right: 20px;" align="left">
	                              <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" width="100%" cellspacing="0" cellpadding="0">
	                                <tbody>
	                                  <tr>
	                                    <td style="padding: 0; margin: 0; width: 560px;" align="center" valign="top">&nbsp;</td>
	                                  </tr>
	                                </tbody>
	                              </table>
	                            </td>
	                          </tr>
	                        </tbody>
	                      </table>
	                    </td>
	                  </tr>
	                </tbody>
	              </table>
	              <table class="es-footer" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px; table-layout: fixed !important; width: 100%; background-color: transparent; background-repeat: repeat; background-position: center top;" cellspacing="0" cellpadding="0" align="center">
	                <tbody>
	                  <tr>
	                    <td style="padding: 0; margin: 0;" align="center">
	                      <table class="es-footer-body" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px; background-color: #ffffff; width: 600px;" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">
	                        <tbody>
	                          <tr>
	                            <td style="padding: 0; margin: 0; padding-top: 20px; padding-left: 20px; padding-right: 20px;" align="left">
	                              <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" width="100%" cellspacing="0" cellpadding="0">
	                                <tbody>
	                                  <tr>
	                                    <td style="padding: 0; margin: 0; width: 560px;" align="center" valign="top">
	                                      <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" role="presentation" width="100%" cellspacing="0" cellpadding="0">
	                                        <tbody>
	                                          <tr>
	                                            <td style="padding: 0; margin: 0; padding-top: 20px; padding-bottom: 20px;" align="center" bgcolor="#f0585d">
	                                              <p style="margin: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; mso-line-height-rule: exactly; font-family: arial, helvetica neue, helvetica, sans-serif; line-height: 21px; color: #ffffff; font-size: 14px;"><strong>Online Saas NSDX &mdash; Netka System Co., Ltd.</strong></p>
	                                            </td>
	                                          </tr>
	                                        </tbody>
	                                      </table>';
				$message .= '</td>
	                                  </tr>
	                                </tbody>
	                              </table>
	                            </td>
	                          </tr>
	                          <tr>
	                            <td style="padding: 0; margin: 0; padding-top: 20px; padding-left: 20px; padding-right: 20px;" align="left">
	                              <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" width="100%" cellspacing="0" cellpadding="0">
	                                <tbody>
	                                  <tr>
	                                    <td style="padding: 0; margin: 0; width: 560px;" align="center" valign="top">
	                                      <table style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; border-spacing: 0px;" width="100%" cellspacing="0" cellpadding="0">
	                                        <tbody>
	                                          <tr>
	                                            <td style="padding: 0; margin: 0; display: none;" align="center">&nbsp;</td>
	                                          </tr>
	                                        </tbody>
	                                      </table>
	                                    </td>
	                                  </tr>
	                                </tbody>
	                              </table>
	                            </td>
	                          </tr>
	                        </tbody>
	                      </table>
	                    </td>
	                  </tr>
	                </tbody>
	              </table>
	            </td>
	          </tr>
	        </tbody>
	      </table>
	    </div>';
				$message .= '</body></html>';


				$from_name = "Netka System";
				$from_mail = "atnetkasystem@gmail.com";
				$replyto   = "";
				$mailbcc   = "";
				$mailto    = "wattawadee.raksachon@gmail.com";
				$header    = "From: " . $from_name . " <" . $from_mail . ">\n";
				$header .= "Reply-To: " . $replyto . "\n";
				$header .= "Bcc: " . $mailbcc . "\n";
				$header .= "MIME-Version: 1.0\n";
				$header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\n\n";
				$emessage = "--" . $uid . "\n";
				$emessage .= "Content-type:text/html; charset=iso-8859-1\n";
				$emessage .= "Content-Transfer-Encoding: 7bit\n\n";
				$emessage .= $message . "\n\n";
				$emessage .= "--" . $uid . "\n";
				$emessage .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\n"; // use different content types here
				$emessage .= "Content-Transfer-Encoding: base64\n";
				$emessage .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\n\n";
				$emessage .= $content . "\n\n";
				$emessage .= "--" . $uid . "--";
				if ( mail($mailto, $subject, $emessage, $header) ){
					$message1 = 'Mail Send Successfully';
				}else{
					$message1 = 'Main not send';
				}

	  // exit;
	   	echo json_encode( array('mscode'=>'Success','message'=>$message1) );
	   	
	   }else{
	   	 echo json_encode( array('mscode'=>'Error','message'=>'status not active.') );
	   }
ob_end_flush();




/* =================== function change statecode to full state ==================================== */
function checkstate($codestate,$codecountry){
	$country = array();
		if($codecountry=="TH"){
				array_push($country , "Thailand");
			//return $codecountry.''.$codestate;exit;
			switch($codestate){
				case "TH-10":
					array_push($country , "Bangkok");
				break;
				case "TH-37":
					array_push($country , "Amnat Charoen");
				break;
				case "TH-15":
					array_push($country , "Ang Thong");
				break;
				case "TH-14":
					array_push($country , "Ayutthaya");
				break;
				case "TH-38":
					array_push($country , "Bueng Kan");
				break;
				case "TH-31":
					array_push($country , "Buri Ram");
				break;
				case "TH-24":
					array_push($country , "Chachoengsao");
				break;
				case "TH-18":
					array_push($country , "Chai Nat");
				break;
				case "TH-36":
					array_push($country , "Chaiyaphum");
				break;
				case "TH-22":
					array_push($country , "Chanthaburi");
				break;
				case "TH-50":
					array_push($country , "Chiang Mai");
				break;
				case "TH-57":
					array_push($country , "Chiang Rai");
				break;
				case "TH-20":
					array_push($country , "Chonburi");
				break;
				case "TH-86":
					array_push($country , "Chumphon");
				break;
				case "TH-46":
					array_push($country , "Kalasin");
				break;
				case "TH-62":
					array_push($country , "Kamphaeng Phet");
				break;
				case "TH-71":
					array_push($country , "Kanchanaburi");
				break;
				case "TH-40":
					array_push($country , "Khon Kaen");
				break;
				case "TH-81":
					array_push($country , "Krabi");
				break;
				case "TH-52":
					array_push($country , "Lampang");
				break;
				case "TH-51":
					array_push($country , "Lamphun");
				break;
				case "TH-42":
					array_push($country , "Loei");
				break;
				case "TH-16":
					array_push($country , "Lopburi");
				break;
				case "TH-58":
					array_push($country , "Mae Hong Son");
				break;
				case "TH-44":
					array_push($country , "Maha Sarakham");
				break;
				case "TH-49":
					array_push($country , "Mukdahan");
				break;
				case "TH-26":
					array_push($country , "Nakhon Nayok");
				break;
				case "TH-73":
					array_push($country , "Nakhon Pathom");
				break;
				case "TH-48":
					array_push($country , "Nakhon Phanom");
				break;
				case "TH-30":
					array_push($country , "Nakhon Ratchasima");
				break;
				case "TH-60":
					array_push($country , "Nakhon Sawan");
				break;
				case "TH-80":
					array_push($country , "Nakhon Si Thammarat");
				break;
				case "TH-55":
					array_push($country , "Nan");
				break;
				case "TH-96":
					array_push($country , "Narathiwat");
				break;
				case "TH-39":
					array_push($country , "Nong Bua Lam Phu");
				break;
				case "TH-43":
					array_push($country , "Nong Khai");
				break;
				case "TH-12":
					array_push($country , "Nonthaburi");
				break;
				case "TH-13":
					array_push($country , "Pathum Thani");
				break;
				case "TH-94":
					array_push($country , "Pattani");
				break;
				case "TH-82":
					array_push($country , "Phang Nga");
				break;
				case "TH-93":
					array_push($country , "Phatthalung");
				break;
				case "TH-56":
					array_push($country , "Phayao");
				break;
				case "TH-67";
					array_push($country , "Phetchabun");
				break;
				case "TH-76":
					array_push($country , "Phetchaburi");
				break;
				case "TH-66":
					array_push($country , "Phichit");
				break;
				case "TH-65":
					array_push($country , "Phitsanulok");
				break;
				case "TH-54":
					array_push($country , "Phrae");
				break;
				case "TH-83":
					array_push($country , "Phuket");
				break;
				case "TH-25":
					array_push($country , "Prachin Buri");
				break;
				case "TH-77":
					array_push($country , "Prachuap Khiri Khan");
				break;
				case "TH-85":
					array_push($country , "Ranong");
				break;
				case "TH-70":
					array_push($country , "Ratchaburi");
				break;
				case "TH-21":
					array_push($country , "Rayong");
				break;
				case "TH-45":
					array_push($country , "Roi Et");
				break;
				case "TH-27":
					array_push($country , "Sa Kaeo");
				break;
				case "TH-47":
					array_push($country , "Sakon Nakhon");
				break;
				case "TH-11":
					array_push($country , "Samut Prakan");
				break;
				case "TH-74":
					array_push($country , "Samut Sakhon");
				break;
				case "TH-75":
					array_push($country , "Samut Songkhram");
				break;
				case "TH-19":
					array_push($country , "Saraburi");
				break;
				case "TH-91":
					array_push($country , "Satun");
				break;
				case "TH-17":
					array_push($country , "Sing Buri");
				break;
				case "TH-33":
					array_push($country , "Sisaket");
				break;
				case "TH-90":
					array_push($country , "Songkhla");
				break;
				case "TH-64":
					array_push($country , "Sukhothai");
				break;
				case "TH-72":
					array_push($country , "Suphan Buri");
				break;
				case "TH-84":
					array_push($country , "Surat Thani");
				break;
				case "TH-32":
					array_push($country , "Surin");
				break;
				case "TH-63":
					array_push($country , "Tak");
				break;
				case "TH-92":
					array_push($country , "Trang");
				break;
				case "TH-23";
					array_push($country , "Trat");
				break;
				case "TH-34":
					array_push($country , "Ubon Ratchathani");
				break;
				case "TH-41":
					array_push($country , "Udon Thani");
				break;
				case "TH-61":
					array_push($country , "Uthai Thani");
				break;
				case "TH-53":
					array_push($country , "Uttaradit");
				break;
				case "TH-95":
					array_push($country , "Yala");
				break;
				case "TH-35":
					array_push($country , "Yasothon");
				break;
			}
			//return $country;
		}
		if($codecountry=='AU'){
			array_push($country ,"Australia");
			switch ($codestate) {
				case "ACT":
					array_push($country , "Australian Capital Territory");
				break;
				case "NSW":
					array_push($country , "New South Wales");
				break;
				case "NT":
					array_push($country , "Northern Territory");
				break;
				case "QLD":
					array_push($country , "Queensland");
				break;
				case "SA":
					array_push($country , "South Australia");
				break;
				case "TAS":
					array_push($country , "Tasmania");
				break;
				case "VIC":
					array_push($country , "Victoria");
				break;
				case "WA":
					array_push($country , "Western Australia");
				break;

			}
		}
		if($codecountry=='BD'){
			array_push($country , "Bangladesh");
			switch ($codestate) {
				case "BAG":
					array_push($country , "Bagerhat");
				break;
				case "BAN":
					array_push($country , "Bandarban");
				break;
				case "BAR":
					array_push($country , "Barguna");
				break;
				case "BARI":
					array_push($country , "Barisal");
				break;
				case "BHO":
					array_push($country , "Bhola");
				break;
				case "BOG":
					array_push($country , "Bogra");
				break;
				case "BRA":
					array_push($country , "Brahmanbaria");
				break;
				case "CHA":
					array_push($country , "Chandpur");
				break;
				case "CHI":
					array_push($country , "Chittagong");
				break;
				case "CHU":
					array_push($country , "Chuadanga");
				break;
				case "COM":
					array_push($country , "Comilla");
				break;
				case "COX":
					array_push($country , "Cox’s Bazar");
				break;
				case "DHA":
					array_push($country , "Dhaka");
				break;
				case "DIN":
					array_push($country , "Dinajpur");
				break;
				case "FAR":
					array_push($country , "Faridpur");
				break;
				case "FEN":
					array_push($country , "Feni");
				break;
				case "GAI":
					array_push($country , "Gaibandha");
				break;
				case "GAZI":
					array_push($country , "Gazipur");
				break;
				case "GOP":
					array_push($country , "Gopalganj");
				break;
				case "HAB":
					array_push($country , "Habiganj");
				break;
				case "JAM":
					array_push($country , "Jamalpur");
				break;
				case "JES":
					array_push($country , "Jessore");
				break;
				case "JHA":
					array_push($country , "Jhalokati");
				break;
				case "JHE":
					array_push($country , "Jhenaidah");
				break;
				case "JOY":
					array_push($country , "Joypurhat");
				break;
				case "KHA":
					array_push($country , "Khagrachhari");
				break;
				case "KHU":
					array_push($country , "Khulna");
				break;
				case "KIS":
					array_push($country , "Kishoreganj");
				break;
				case "KUR":
					array_push($country , "Kurigram");
				break;
				case "KUS":
					array_push($country , "Kushtia");
				break;
				case "LAK":
					array_push($country , "Lakshmipur");
				break;
				case "LAL":
					array_push($country , "Lalmonirhat");
				break;
				case "MAD":
					array_push($country , "Madaripur");
				break;
				case "MAG":
					array_push($country , "Magura");
				break;
				case "MAN":
					array_push($country , "Manikganj");
				break;
				case "MEH":
					array_push($country , "Meherpur");
				break;
				case "MOU":
					array_push($country , "Moulvibazar");
				break;
				case "MUN":
					array_push($country , "Munshiganj");
				break;
				case "MYM":
					array_push($country , "Mymensingh");
				break;
				case "NAO":
					array_push($country , "Naogaon");
				break;
				case "NAR":
					array_push($country , "Narail");
				break;
				case "NARG":
					array_push($country , "Narayanganj");
				break;
				case "NARD":
					array_push($country , "Narsingdi");
				break;
				case "NAT":
					array_push($country , "Natore");
				break;
				case "NAW":
					array_push($country , "Nawabganj");
				break;
				case "NET":
					array_push($country , "Netrakona");
				break;
				case "NIL":
					array_push($country , "Nilphamari");
				break;
				case "NOA":
					array_push($country , "Noakhali");
				break;
				case "PAB":
					array_push($country , "Pabna");
				break;
				case "PAN":
					array_push($country , "Panchagarh");
				break;
				case "PAT":
					array_push($country , "Patuakhali");
				break;
				case "PIR":
					array_push($country , "Pirojpur");
				break;
				case "RAJB":
					array_push($country , "Rajbari");
				break;
				case "RAJ":
					array_push($country , "Rajshahi");
				break;
				case "RAN":
					array_push($country , "Rangamati");
				break;
				case "RANP":
					array_push($country , "Rangpur");
				break;
				case "SAT":
					array_push($country , "Satkhira");
				break;
				case "SHA":
					array_push($country , "Shariatpur");
				break;
				case "SHE":
					array_push($country , "Sherpur");
				break;
				case "SIR":
					array_push($country , "Sirajganj");
				break;
				case "SUN":
					array_push($country , "Sunamganj");
				break;
				case "SYL":
					array_push($country , "Sylhet");
				break;
				case "TAN":
					array_push($country , "Tangail");
				break;
				case "THA":
					array_push($country , "Thakurgaon");
				break;
			}
		}
		if($codecountry=='BR'){
			array_push($country , "Brazil");
			switch ($codestate) {
				case "AC":
					array_push($country , "Acre");
				break;
				case "AL":
					array_push($country , "Alagoas");
				break;
				case "AP":
					array_push($country , "Amapá");
				break;
				case "AM":
					array_push($country , "Amazonas");
				break;
				case "BA":
					array_push($country , "Bahia");
				break;
				case "CE";
					array_push($country , "Ceará");
				break;
				case "DF":
					array_push($country , "Distrito Federal");
				break;
				case "ES":
					array_push($country , "Espírito Santo");
				break;
				case "GO":
					array_push($country , "Goiás");
				break;
				case "MA":
					array_push($country , "Maranhão");
				break;
				case "MT":
					array_push($country , "Mato Grosso");
				break;
				case "MS":
					array_push($country , "Mato Grosso do Sul");
				break;
				case "MG":
					array_push($country , "Minas Gerais");
				break;
				case "PA":
					array_push($country , "Pará");
				break;
				case "PB":
					array_push($country , "Paraíba");
				break;
				case "PR":
					array_push($country , "Paraná");
				break;
				case "PE":
					array_push($country , "Pernambuco");
				break;
				case "PI":
					array_push($country , "Piauí");
				break;
				case "RJ":
					array_push($country , "Rio de Janeiro");
				break;
				case "RN":
					array_push($country , "Rio Grande do Norte");
				break;
				case "RS":
					array_push($country , "Rio Grande do Sul");
				break;
				case "RO":
					array_push($country , "Rondônia");
				break;
				case "RR":
					array_push($country , "Roraima");
				break;
				case "SC":
					array_push($country , "Santa Catarina");
				break;
				case "SP":
					array_push($country , "São Paulo");
				break;
				case "SE":
					array_push($country , "Sergipe");
				break;
				case "TO":
					array_push($country , "Tocantins");
				break;
			}
		}
		if($codecountry=='BG'){
			array_push($country ,"Bulgaria");
			switch ($codestate) {
				case "BG-01":
					array_push($country , "Blagoevgrad");
				break;
				case "BG-02":
					array_push($country , "Burgas");
				break;
				case "BG-08":
					array_push($country , "Dobrich");
				break;
				case "BG-07":
					array_push($country , "Gabrovo");
				break;
				case "BG-26":
					array_push($country , "Haskovo");
				break;
				case "BG-09":
					array_push($country , "Kardzhali");
				break;
				case "BG-10":
					array_push($country , "Kyustendil");
				break;
				case "BG-11":
					array_push($country , "Lovech");
				break;
				case "BG-12":
					array_push($country , "Montana");
				break;
				case "BG-13":
					array_push($country , "Pazardzhik");
				break;
				case "BG-14":
					array_push($country , "Pernik");
				break;
				case "BG-15":
					array_push($country , "Pleven");
				break;
				case "BG-16":
					array_push($country , "Plovdiv");
				break;
				case "BG-17":
					array_push($country , "Razgrad");
				break;
				case "BG-18":
					array_push($country , "Ruse");
				break;
				case "BG-27":
					array_push($country , "Shumen");
				break;
				case "BG-19":
					array_push($country , "Silistra");
				break;
				case "BG-20":
					array_push($country , "Sliven");
				break;
				case "BG-21":
					array_push($country , "Smolyan");
				break;
				case "BG-23":
					array_push($country , "Sofia");
				break;
				case "BG-22":
					array_push($country , "Sofia-Grad");
				break;
				case "BG-24":
					array_push($country , "Stara Zagora");
				break;
				case "BG-25":
					array_push($country , "Targovishte");
				break;
				case "BG-03":
					array_push($country , "Varna");
				break;
				case "BG-04":
					array_push($country , "Veliko Tarnovo");
				break;
				case "BG-05":
					array_push($country , "Vidin");
				break;
				case "BG-06":
					array_push($country , "Vratsa");
				break;
				case "BG-28":
					array_push($country , "Yambol");
				break;
			}
		}
		if($codecountry=='CA'){
			array_push($country , "Canada");
			switch ($codestate) {
				case "AB":
					array_push($country , "Alberta");
				break;
				case "BC":
					array_push($country , "British Columbia");
				break;
				case "MB":
					array_push($country , "Manitoba");
				break;
				case "NB":
					array_push($country , "New Brunswick");
				break;
				case "NL":
					array_push($country , "Newfoundland");
				break;
				case "NT":
					array_push($country , "Northwest Territories");
				break;
				case "NS":
					array_push($country , "Nova Scotia");
				break;
				case "NU":
					array_push($country , "Nunavut");
				break;
				case "ON":
					array_push($country , "Ontario");
				break;
				case "PE":
					array_push($country , "Prince Edward Island");
				break;
				case "QC":
					array_push($country , "Quebec");
				break;
				case "SK":
					array_push($country , "Saskatchewan");
				break;
				case "YT":
					array_push($country , "Yukon Territory");
				break;
			}
		}
		if($codecountry=='CN'){
			array_push($country , "China");
			switch ($codestate) {
				case "CN1":
					array_push($country , "Yunnan / 云南");
				break;
				case "CN2":
					array_push($country , "Beijing / 北京");
				break;
				case "CN3":
					array_push($country , "Tianjin / 天津");
				break;
				case "CN4":
					array_push($country , "Hebei / 河北");
				break;
				case "CN5":
					array_push($country , "Shanxi / 山西");
				break;
				case "CN6":
					array_push($country , "Inner Mongolia / 內蒙古");
				break;
				case "CN7":
					array_push($country , "Liaoning / 辽宁");
				break;
				case "CN8":
					array_push($country , "Jilin / 吉林");
				break;
				case "CN9":
					array_push($country , "Heilongjiang / 黑龙江");
				break;
				case "CN10":
					array_push($country , "Shanghai / 上海");
				break;
				case "CN11":
					array_push($country , "Jiangsu / 江苏");
				break;
				case "CN12":
					array_push($country , "Zhejiang / 浙江");
				break;
				case "CN13":
					array_push($country , "Anhui / 安徽");
				break;
				case "CN14":
					array_push($country , "Fujian / 福建");
				break;
				case "CN15":
					array_push($country , "Jiangxi / 江西");
				break;
				case "CN16":
					array_push($country , "Shandong / 山东");
				break;
				case "CN17":
					array_push($country , "Henan / 河南");
				break;
				case "CN18":
					array_push($country , "Hubei / 湖北");
				break;
				case "CN19":
					array_push($country , "Hunan / 湖南");
				break;
				case "CN20":
					array_push($country , "Guangdong / 广东");
				break;
				case "CN21":
					array_push($country , "Guangxi Zhuang / 广西壮族");
				break;
				case "CN22":
					array_push($country , "Hainan / 海南");
				break;
				case "CN23":
					array_push($country , "Chongqing / 重庆");
				break;
				case "CN24":
					array_push($country , "Sichuan / 四川");
				break;
				case "CN25":
					array_push($country , "Guizhou / 贵州");
				break;
				case "CN26":
					array_push($country , "Shaanxi / 陕西");
				break;
				case "CN27":
					array_push($country , "Gansu / 甘肃");
				break;
				case "CN28":
					array_push($country , "Qinghai / 青海");
				break;
				case "CN29":
					array_push($country , "Ningxia Hui / 宁夏");
				break;
				case "CN30":
					array_push($country , "Macau / 澳门");
				break;
				case "CN31":
					array_push($country , "Tibet / 西藏");
				break;
				case "CN32":
					array_push($country , "Xinjiang / 新疆");
				break;
			}
		}
		if($codecountry=='HK'){
			array_push($country , "Hong Kong");
			switch ($codestate) {
				case "HONG KONG":
					array_push($country , "Hong Kong Island");
				break;
				case "KOWLOON":
					array_push($country , "Kowloon");
				break;
				case "NEW TERRITORIES":
					array_push($country , "New Territories");
				break;
			}
		}
		if($codecountry=='HU'){
			array_push($country , "Hungary");
			switch ($codestate) {
				case "BK":
					array_push($country , "Bács-Kiskun");
				break;
				case "BE":
					array_push($country , "Békés");
				break;
				case "BA":
					array_push($country , "Baranya");
				break;
				case "BZ":
					array_push($country , "Borsod-Abaúj-Zemplén");
				break;
				case "BU":
					array_push($country , "Budapest");
				break;
				case "CS":
					array_push($country , "Csongrád");
				break;
				case "FE":
					array_push($country , "Fejér");
				break;
				case "GS":
					array_push($country , "Győr-Moson-Sopron");
				break;
				case "HB":
					array_push($country , "Hajdú-Bihar");
				break;
				case "HE":
					array_push($country , "Heves");
				break;
				case "JN":
					array_push($country , "Jász-Nagykun-Szolnok");
				break;
				case "KE":
					array_push($country , "Komárom-Esztergom");
				break;
				case "NO":
					array_push($country , "Nógrád");
				break;
				case "PE":
					array_push($country , "Pest");
				break;
				case "SO":
					array_push($country , "Somogy");
				break;
				case "SZ":
					array_push($country , "Szabolcs-Szatmár-Bereg");
				break;
				case "TO":
					array_push($country , "Tolna");
				break;
				case "VA":
					array_push($country , "Vas");
				break;
				case "VE":
					array_push($country , "Veszprém");
				break;
				case "ZA":
					array_push($country , "Zala");
				break;
			}
		}
		if($codecountry=='IN'){
			array_push($country , "India");
			switch ($codestate) {
				case "AP":
					array_push($country , "Andra Pradesh");
				break;
				case "AR":
					array_push($country , "Arunachal Pradesh");
				break;
				case "AS":
					array_push($country , "Assam");
				break;
				case "BR":
					array_push($country , "Bihar");
				break;
				case "CT":
					array_push($country , "Chhattisgarh");
				break;
				case "GA":
					array_push($country , "Goa");
				break;
				case "GJ":
					array_push($country , "Gujarat");
				break;
				case "HR":
					array_push($country , "Haryana");
				break;
				case "HP":
					array_push($country , "Himachal Pradesh");
				break;
				case "JK":
					array_push($country , "Jammu and Kashmir");
				break;
				case "JH":
					array_push($country , "Jharkhand");
				break;
				case "KA":
					array_push($country , "Karnataka");
				break;
				case "KL":
					array_push($country , "Kerala");
				break;
				case "MP":
					array_push($country , "Madhya Pradesh");
				break;
				case "MH":
					array_push($country , "Maharashtra");
				break;
				case "MN":
					array_push($country , "Manipur");
				break;
				case "ML":
					array_push($country , "Meghalaya");
				break;
				case "MZ":
					array_push($country , "Mizoram");
				break;
				case "NL":
					array_push($country , "Nagaland");
				break;
				case "OR":
					array_push($country , "Orissa");
				break;
				case "PB":
					array_push($country , "Punjab");
				break;
				case "RJ":
					array_push($country , "Rajasthan");
				break;
				case "SK":
					array_push($country , "Sikkim");
				break;
				case "TN":
					array_push($country , "Tamil Nadu");
				break;
				case "TR":
					array_push($country , "Tripura");
				break;
				case "UK":
					array_push($country , "Uttarakhand");
				break;
				case "UP":
					array_push($country , "Uttar Pradesh");
				break;
				case "WB":
					array_push($country , "West Bengal");
				break;
				case "AN":
					array_push($country , "Andaman and Nicobar Islands");
				break;
				case "CH":
					array_push($country , "Chandigarh");
				break;
				case "DN":
					array_push($country , "Dadar and Nagar Haveli");
				break;
				case "DD":
					array_push($country , "Daman and Diu");
				break;
				case "DL":
					array_push($country , "Delhi");
				break;
				case "LD":
					array_push($country , "Lakshadeep");
				break;
				case "PY":
					array_push($country , "Pondicherry (Puducherry)");
				break;
			}
		}
		if($codecountry=='ID'){
			array_push($country , "Indonesia");
			switch ($codestate) {
				case "AC":
					array_push($country , "Daerah Istimewa Aceh");
				break;
				case "SU":
					array_push($country , "Sumatera Utara");
				break;
				case "SB":
					array_push($country , "Sumatera Barat");
				break;
				case "RI":
					array_push($country , "Riau");
				break;
				case "KR":
					array_push($country , "Kepulauan Riau");
				break;
				case "JA":
					array_push($country , "Jambi");
				break;
				case "SS":
					array_push($country , "Sumatera Selatan");
				break;
				case "BB":
					array_push($country , "Bangka Belitung");
				break;
				case "BE":
					array_push($country , "Bengkulu");
				break;
				case "LA":
					array_push($country , "Lampung");
				break;
				case "JK":
					array_push($country , "DKI Jakarta");
				break;
				case "JB":
					array_push($country , "Jawa Barat");
				break;
				case "BT":
					array_push($country , "Banten");
				break;
				case "JT":
					array_push($country , "Jawa Tengah");
				break;
				case "JI":
					array_push($country , "Jawa Timur");
				break;
				case "YO":
					array_push($country , "Daerah Istimewa Yogyakarta");
				break;
				case "BA":
					array_push($country , "Bali");
				break;
				case "NB":
					array_push($country , "Nusa Tenggara Barat");
				break;
				case "NT":
					array_push($country , "Nusa Tenggara Timur");
				break;
				case "KB":
					array_push($country , "Kalimantan Barat");
				break;
				case "KT":
					array_push($country , "Kalimantan Tengah");
				break;
				case "KI":
					array_push($country , "Kalimantan Timur");
				break;
				case "KS":
					array_push($country , "Kalimantan Selatan");
				break;
				case "KU":
					array_push($country , "Kalimantan Utara");
				break;
				case "SA":
					array_push($country , "Sulawesi Utara");
				break;
				case "ST":
					array_push($country , "Sulawesi Tengah");
				break;
				case "SG":
					array_push($country , "Sulawesi Tenggara");
				break;
				case "SR":
					array_push($country , "Sulawesi Barat");
				break;
				case "SN":
					array_push($country , "Sulawesi Selatan");
				break;
				case "GO":
					array_push($country , "Gorontalo");
				break;
				case "MA":
					array_push($country , "Maluku");
				break;
				case "MU":
					array_push($country , "Maluku Utara");
				break;
				case "PA":
					array_push($country , "Papua");
				break;
				case "PB":
					array_push($country , "Papua Barat");
				break;
			}
		}
		if($codecountry=='IT'){
			array_push($country , "Italy");
			switch ($codestate) {
				case "AG":
					array_push($country , "Agrigento");
				break;
				case "AL":
					array_push($country , "Alessandria");
				break;
				case "AN":
					array_push($country , "Ancona");
				break;
				case "AO":
					array_push($country , "Aosta");
				break;
				case "AR":
					array_push($country , "Arezzo");
				break;
				case "AP":
					array_push($country , "Ascoli Piceno");
				break;
				case "AT":
					array_push($country , "Asti");
				break;
				case "AV":
					array_push($country , "Avellino");
				break;
				case "BA":
					array_push($country , "Bari");
				break;
				case "BT":
					array_push($country , "Barletta-Andria-Trani");
				break;
				case "BL":
					array_push($country , "Belluno");
				break;
				case "BN":
					array_push($country , "Benevento");
				break;
				case "BG":
					array_push($country , "Bergamo");
				break;
				case "BI":
					array_push($country , "Biella");
				break;
				case "BO":
					array_push($country , "Bologna");
				break;
				case "BZ":
					array_push($country , "Bolzano");
				break;
				case "BS":
					array_push($country , "Brescia");
				break;
				case "BR":
					array_push($country , "Brindisi");
				break;
				case "CA":
					array_push($country , "Cagliari");
				break;
				case "CL":
					array_push($country , "Caltanissetta");
				break;
				case "CB":
					array_push($country , "Campobasso");
				break;
				case "CI":
					array_push($country , "Carbonia-Iglesias");
				break;
				case "CE":
					array_push($country , "Caserta");
				break;
				case "CT":
					array_push($country , "Catania");
				break;
				case "CZ":
					array_push($country , "Catanzaro");
				break;
				case "CH":
					array_push($country , "Chieti");
				break;
				case "CO":
					array_push($country , "Como");
				break;
				case "CS":
					array_push($country , "Cosenza");
				break;
				case "CR":
					array_push($country , "Cremona");
				break;
				case "KR":
					array_push($country , "Crotone");
				break;
				case "CN":
					array_push($country , "Cuneo");
				break;
				case "EN":
					array_push($country , "Enna");
				break;
				case "FM":
					array_push($country , "Fermo");
				break;
				case "FE":
					array_push($country , "Ferrara");
				break;
				case "FI":
					array_push($country , "Firenze");
				break;
				case "FG":
					array_push($country , "Foggia");
				break;
				case "FC":
					array_push($country , "Forlì-Cesena");
				break;
				case "FR":
					array_push($country , "Frosinone");
				break;
				case "GE":
					array_push($country , "Genova");
				break;
				case "GO":
					array_push($country , "Gorizia");
				break;
				case "GR":
					array_push($country , "Grosseto");
				break;
				case "IM":
					array_push($country , "Imperia");
				break;
				case "IS":
					array_push($country , "Isernia");
				break;
				case "SP":
					array_push($country , "La Spezia");
				break;
				case "AQ":
					array_push($country , "L’Aquila");
				break;
				case "LT":
					array_push($country , "Latina");
				break;
				case "LE":
					array_push($country , "Lecce");
				break;
				case "LC":
					array_push($country , "Lecco");
				break;
				case "LI":
					array_push($country , "Livorno");
				break;
				case "LO":
					array_push($country , "Lodi");
				break;
				case "LU":
					array_push($country , "Lucca");
				break;
				case "MC":
					array_push($country , "Macerata");
				break;
				case "MN":
					array_push($country , "Mantova");
				break;
				case "MS":
					array_push($country , "Massa-Carrara");
				break;
				case "MT":
					array_push($country , "Matera");
				break;
				case "ME":
					array_push($country , "Messina");
				break;
				case "MI":
					array_push($country , "Milano");
				break;
				case "MO":
					array_push($country , "Modena");
				break;
				case "MB":
					array_push($country , "Monza e della Brianza");
				break;
				case "NA":
					array_push($country , "Napoli");
				break;
				case "NO":
					array_push($country , "Novara");
				break;
				case "NU":
					array_push($country , "Nuoro");
				break;
				case "OT":
					array_push($country , "Olbia-Tempio");
				break;
				case "OR":
					array_push($country , "Oristano");
				break;
				case "PD":
					array_push($country , "Padova");
				break;
				case "PA":
					array_push($country , "Palermo");
				break;
				case "PR":
					array_push($country , "Parma");
				break;
				case "PV":
					array_push($country , "Pavia");
				break;
				case "PG":
					array_push($country , "Perugia");
				break;
				case "PU":
					array_push($country , "Pesaro e Urbino");
				break;
				case "PE":
					array_push($country , "Pescara");
				break;
				case "PC":
					array_push($country , "Piacenza");
				break;
				case "PI":
					array_push($country , "Pisa");
				break;
				case "PT":
					array_push($country , "Pistoia");
				break;
				case "PN":
					array_push($country , "Pordenone");
				break;
				case "PZ":
					array_push($country , "Potenza");
				break;
				case "PO":
					array_push($country , "Prato");
				break;
				case "RG":
					array_push($country , "Ragusa");
				break;
				case "RA":
					array_push($country , "Ravenna");
				break;
				case "RC":
					array_push($country , "Reggio Calabria");
				break;
				case "RE":
					array_push($country , "Reggio Emilia");
				break;
				case "RI":
					array_push($country , "Rieti");
				break;
				case "RN":
					array_push($country , "Rimini");
				break;
				case "RM":
					array_push($country , "Roma");
				break;
				case "RO":
					array_push($country , "Rovigo");
				break;
				case "SA":
					array_push($country , "Salerno");
				break;
				case "VS":
					array_push($country , "Medio Campidano");
				break;
				case "SS":
					array_push($country , "Sassari");
				break;
				case "SV":
					array_push($country , "Savona");
				break;
				case "SI":
					array_push($country , "Siena");
				break;
				case "SR":
					array_push($country , "Siracusa");
				break;
				case "SO":
					array_push($country , "Sondrio");
				break;
				case "TA":
					array_push($country , "Taranto");
				break;
				case "TE":
					array_push($country , "Teramo");
				break;
				case "TR":
					array_push($country , "Terni");
				break;
				case "TO":
					array_push($country , "Torino");
				break;
				case "OG":
					array_push($country , "Ogliastra");
				break;
				case "TP":
					array_push($country , "Trapani");
				break;
				case "TN":
					array_push($country , "Trento");
				break;
				case "TV":
					array_push($country , "Treviso");
				break;
				case "TS":
					array_push($country , "Trieste");
				break;
				case "UD":
					array_push($country , "Udine");
				break;
				case "VA":
					array_push($country , "Varese");
				break;
				case "VE":
					array_push($country , "Venezia");
				break;
				case "VB":
					array_push($country , "Verbano-Cusio-Ossola");
				break;
				case "VC":
					array_push($country , "Vercelli");
				break;
				case "VR":
					array_push($country , "Verona");
				break;
				case "VV":
					array_push($country , "Vibo Valentia");
				break;
				case "VI":
					array_push($country , "Vicenza");
				break;
				case "VT":
					array_push($country , "Viterbo");
				break;
			}
		}
		if($codecountry=='JP'){
			array_push($country , "Japan");
			switch ($codestate) {
				case "JP01":
					array_push($country , "Hokkaido");
				break;
				case "JP02":
					array_push($country , "Aomori");
				break;
				case "JP03":
					array_push($country , "Iwate");
				break;
				case "JP04":
					array_push($country , "Miyagi");
				break;
				case "JP05":
					array_push($country , "Akita");
				break;
				case "JP06":
					array_push($country , "Yamagata");
				break;
				case "JP07":
					array_push($country , "Fukushima");
				break;
				case "JP08":
					array_push($country , "Ibaraki");
				break;
				case "JP09":
					array_push($country , "Tochigi");
				break;
				case "JP10":
					array_push($country , "Gunma");
				break;
				case "JP11":
					array_push($country , "Saitama");
				break;
				case "JP12":
					array_push($country , "Chiba");
				break;
				case "JP13":
					array_push($country , "Tokyo");
				break;
				case "JP14":
					array_push($country , "Kanagawa");
				break;
				case "JP15":
					array_push($country , "Niigata");
				break;
				case "JP16":
					array_push($country , "Toyama");
				break;
				case "JP17":
					array_push($country , "Ishikawa");
				break;
				case "JP18":
					array_push($country , "Fukui");
				break;
				case "JP19":
					array_push($country , "Yamanashi");
				break;
				case "JP20":
					array_push($country , "Nagano");
				break;
				case "JP21":
					array_push($country , "Gifu");
				break;
				case "JP22":
					array_push($country , "Shizuoka");
				break;
				case "JP23":
					array_push($country , "Aichi");
				break;
				case "JP24":
					array_push($country , "Mie");
				break;
				case "JP25":
					array_push($country , "Shiga");
				break;
				case "JP26":
					array_push($country , "Kyouto");
				break;
				case "JP27":
					array_push($country , "Osaka");
				break;
				case "JP28":
					array_push($country , "Hyougo");
				break;
				case "JP29":
					array_push($country , "Nara");
				break;
				case "JP30":
					array_push($country , "Wakayama");
				break;
				case "JP31":
					array_push($country , "Tottori");
				break;
				case "JP32":
					array_push($country , "Shimane");
				break;
				case "JP33":
					array_push($country , "Okayama");
				break;
				case "JP34":
					array_push($country , "Hiroshima");
				break;
				case "JP35":
					array_push($country , "Yamaguchi");
				break;
				case "JP36":
					array_push($country , "Tokushima");
				break;
				case "JP37":
					array_push($country , "Kagawa");
				break;
				case "JP38":
					array_push($country , "Ehime");
				break;
				case "JP39":
					array_push($country , "Kochi");
				break;
				case "JP40":
					array_push($country , "Fukuoka");
				break;
				case "JP41":
					array_push($country , "Saga");
				break;
				case "JP42":
					array_push($country , "Nagasaki");
				break;
				case "JP43":
					array_push($country , "Kumamoto");
				break;
				case "JP44":
					array_push($country , "Oita");
				break;
				case "JP45":
					array_push($country , "Miyazaki");
				break;
				case "JP46":
					array_push($country , "Kagoshima");
				break;
				case "JP47":
					array_push($country , "Okinawa");
				break;
			}
		}
		if($codecountry=='MY'){
			array_push($country , "Malaysia");
			switch ($codestate) {
				case "JHR":
					array_push($country , "Johor");
				break;
				case "KDH":
					array_push($country , "Kedah");
				break;
				case "KTN":
					array_push($country , "Kelantan");
				break;
				case "MLK":
					array_push($country , "Melaka");
				break;
				case "NSN":
					array_push($country , "Negeri Sembilan");
				break;
				case "PHG":
					array_push($country , "Pahang");
				break;
				case "PRK":
					array_push($country , "Perak");
				break;
				case "PLS":
					array_push($country , "Perlis");
				break;
				case "PNG":
					array_push($country , "Pulau Pinang");
				break;
				case "SBH":
					array_push($country , "Sabah");
				break;
				case "SWK":
					array_push($country , "Sarawak");
				break;
				case "SGR":
					array_push($country , "Selangor");
				break;
				case "TRG":
					array_push($country , "Terengganu");
				break;
				case "KUL":
					array_push($country , "W.P. Kuala Lumpur");
				break;
				case "LBN":
					array_push($country , "W.P. Labuan");
				break;
				case "PJY":
					array_push($country , "W.P. Putrajaya");
				break;
			}
		}
		if($codecountry=='NZ'){
			array_push($country , "New Zealand");
			switch ($codestate) {
				case "NL":
					array_push($country , "Northland");
				break;
				case "AK":
					array_push($country , "Auckland");
				break;
				case "WA":
					array_push($country , "Waikato");
				break;
				case "BP":
					array_push($country , "Bay of Plenty");
				break;
				case "TK":
					array_push($country , "Taranaki");
				break;
				case "HB":
					array_push($country , "Hawke’s Bay");
				break;
				case "MW":
					array_push($country , "Manawatu-Wanganui");
				break;
				case "WE":
					array_push($country , "Wellington");
				break;
				case "NS":
					array_push($country , "Nelson");
				break;
				case "MB":
					array_push($country , "Marlborough");
				break;
				case "TM":
					array_push($country , "Tasman");
				break;
				case "WC":
					array_push($country , "West Coast");
				break;
				case "CT":
					array_push($country , "Canterbury");
				break;
				case "OT":
					array_push($country , "Otago");
				break;
				case "SL":
					array_push($country , "Southland");
				break;
			}
		}
		if($codecountry=='PE'){
			array_push($country , "Peru");
			switch ($codestate) {
				case "CAL":
					array_push($country , "El Callao");
				break;
				case "LMA":
					array_push($country , "Municipalidad Metropolitana de Lima");
				break;
				case "AMA":
					array_push($country , "Amazonas");
				break;
				case "ANC":
					array_push($country , "Ancash");
				break;
				case "APU":
					array_push($country , "Apurímac");
				break;
				case "ARE":
					array_push($country , "Arequipa");
				break;
				case "AYA":
					array_push($country , "Ayacucho");
				break;
				case "CAJ":
					array_push($country , "Cajamarca");
				break;
				case "CUS":
					array_push($country , "Cusco");
				break;
				case "HUV":
					array_push($country , "Huancavelica");
				break;
				case "HUC":
					array_push($country , "Huánuco");
				break;
				case "ICA":
					array_push($country , "Ica");
				break;
				case "JUN":
					array_push($country , "Junín");
				break;
				case "LAL":
					array_push($country , "La Libertad");
				break;
				case "LAM":
					array_push($country , "Lambayeque");
				break;
				case "LIM":
					array_push($country , "Lima");
				break;
				case "LOR":
					array_push($country , "Loreto");
				break;
				case "MDD":
					array_push($country , "Madre de Dios");
				break;
				case "MOQ":
					array_push($country , "Moquegua");
				break;
				case "PAS":
					array_push($country , "Pasco");
				break;
				case "PIU":
					array_push($country , "Piura");
				break;
				case "PUN":
					array_push($country , "Puno");
				break;
				case "SAM":
					array_push($country , "San Martín");
				break;
				case "TAC":
					array_push($country , "Tacna");
				break;
				case "TUM":
					array_push($country , "Tumbes");
				break;
				case "UCA":
					array_push($country , "Ucayali");
				break;
			}
		}
		if($codecountry=='ZA'){
			array_push($country , "South Africa");
			switch ($codestate) {
				case "EC":
					array_push($country , "Eastern Cape");
				break;
				case "FS":
					array_push($country , "Free State");
				break;
				case "GP":
					array_push($country , "Gauteng");
				break;
				case "KZN":
					array_push($country , "KwaZulu-Natal");
				break;
				case "LP":
					array_push($country , "Limpopo");
				break;
				case "MP":
					array_push($country , "Mpumalanga");
				break;
				case "NC":
					array_push($country , "Northern Cape");
				break;
				case "NW":
					array_push($country , "North West");
				break;
				case "WC":
					array_push($country , "Western Cape");
				break;
			}
		}
		if($codecountry=='ES'){
			array_push($country , "Spain");
			switch ($codestate) {
				case "C":
					array_push($country , "A Coruña");
				break;
				case "VI":
					array_push($country , "Araba/Álava");
				break;
				case "AB":
					array_push($country , "Albacete");
				break;
				case "A":
					array_push($country , "Alicante");
				break;
				case "AL":
					array_push($country , "Almería");
				break;
				case "O":
					array_push($country , "Asturias");
				break;
				case "AV":
					array_push($country , "Ávila");
				break;
				case "BA":
					array_push($country , "Badajoz");
				break;
				case "PM":
					array_push($country , "Baleares");
				break;
				case "B":
					array_push($country , "Barcelona");
				break;
				case "BU":
					array_push($country , "Burgos");
				break;
				case "CC":
					array_push($country , "Cáceres");
				break;
				case "CA":
					array_push($country , "Cádiz");
				break;
				case "S":
					array_push($country , "Cantabria");
				break;
				case "CS":
					array_push($country , "Castellón");
				break;
				case "CE":
					array_push($country , "Ceuta");
				break;
				case "CR":
					array_push($country , "Ciudad Real");
				break;
				case "CO":
					array_push($country , "Córdoba");
				break;
				case "CU":
					array_push($country , "Cuenca");
				break;
				case "GI":
					array_push($country , "Girona");
				break;
				case "GR":
					array_push($country , "Granada");
				break;
				case "GU":
					array_push($country , "Guadalajara");
				break;
				case "SS":
					array_push($country , "Gipuzkoa");
				break;
				case "H":
					array_push($country , "Huelva");
				break;
				case "HU":
					array_push($country , "Huesca");
				break;
				case "J":
					array_push($country , "Jaén");
				break;
				case "LO":
					array_push($country , "La Rioja");
				break;
				case "GC":
					array_push($country , "Las Palmas");
				break;
				case "LE":
					array_push($country , "León");
				break;
				case "L":
					array_push($country , "Lleida");
				break;
				case "LU":
					array_push($country , "Lugo");
				break;
				case "M":
					array_push($country , "Madrid");
				break;
				case "MA":
					array_push($country , "Málaga");
				break;
				case "ML":
					array_push($country , "Melilla");
				break;
				case "MU":
					array_push($country , "Murcia");
				break;
				case "NA":
					array_push($country , "Navarra");
				break;
				case "OR":
					array_push($country , "Ourense");
				break;
				case "P":
					array_push($country , "Palencia");
				break;
				case "PO":
					array_push($country , "Pontevedra");
				break;
				case "SA":
					array_push($country , "Salamanca");
				break;
				case "TF":
					array_push($country , "Santa Cruz de Tenerife");
				break;
				case "SG":
					array_push($country , "Segovia");
				break;
				case "SE":
					array_push($country , "Sevilla");
				break;
				case "SO":
					array_push($country , "Soria");
				break;
				case "T":
					array_push($country , "Tarragona");
				break;
				case "TE":
					array_push($country , "Teruel");
				break;
				case "TO":
					array_push($country , "Toledo");
				break;
				case "V":
					array_push($country , "Valencia");
				break;
				case "VA":
					array_push($country , "Valladolid");
				break;
				case "BI":
					array_push($country , "Bizkaia");
				break;
				case "ZA":
					array_push($country , "Zamora");
				break;
				case "Z":
					array_push($country , "Zaragoza");
				break;
			}
		}
		if($codecountry=='TR'){
			array_push($country , "Turkey");
			switch ($codestate) {
				case "TR01":
					array_push($country , "Adana");
				break;
				case "TR02":
					array_push($country , "Adıyaman");
				break;
				case "TR03":
					array_push($country , "Afyon");
				break;
				case "TR04":
					array_push($country , "Ağrı");
				break;
				case "TR05":
					array_push($country , "Amasya");
				break;
				case "TR06":
					array_push($country , "Ankara");
				break;
				case "TR07":
					array_push($country , "Antalya");
				break;
				case "TR08":
					array_push($country , "Artvin");
				break;
				case "TR09":
					array_push($country , "Aydın");
				break;
				case "TR10":
					array_push($country , "Balıkesir");
				break;
				case "TR11":
					array_push($country , "Bilecik");
				break;
				case "TR12":
					array_push($country , "Bingöl");
				break;
				case "TR13":
					array_push($country , "Bitlis");
				break;
				case "TR14":
					array_push($country , "Bolu");
				break;
				case "TR15":
					array_push($country , "Burdur");
				break;
				case "TR16":
					array_push($country , "Bursa");
				break;
				case "TR17":
					array_push($country , "Çanakkale");
				break;
				case "TR18":
					array_push($country , "Çankırı");
				break;
				case "TR19":
					array_push($country , "Çorum");
				break;
				case "TR20":
					array_push($country , "Denizli");
				break;
				case "TR21":
					array_push($country , "Diyarbakır");
				break;
				case "TR22":
					array_push($country , "Edirne");
				break;
				case "TR23":
					array_push($country , "Elazığ");
				break;
				case "TR24":
					array_push($country , "Erzincan");
				break;
				case "TR25":
					array_push($country , "Erzurum");
				break;
				case "TR26":
					array_push($country , "Eskişehir");
				break;
				case "TR27":
					array_push($country , "Gaziantep");
				break;
				case "TR28":
					array_push($country , "Giresun");
				break;
				case "TR29":
					array_push($country , "Gümüşhane");
				break;
				case "TR30":
					array_push($country , "Hakkari");
				break;
				case "TR31":
					array_push($country , "Hatay");
				break;
				case "TR32":
					array_push($country , "Isparta");
				break;
				case "TR33":
					array_push($country , "İçel");
				break;
				case "TR34":
					array_push($country , "İstanbul");
				break;
				case "TR35":
					array_push($country , "İzmir");
				break;
				case "TR36":
					array_push($country , "Kars");
				break;
				case "TR37":
					array_push($country , "Kastamonu");
				break;
				case "TR38":
					array_push($country , "Kayseri");
				break;
				case "TR39":
					array_push($country , "Kırklareli");
				break;
				case "TR40":
					array_push($country , "Kırşehir");
				break;
				case "TR41":
					array_push($country , "Kocaeli");
				break;
				case "TR42":
					array_push($country , "Konya");
				break;
				case "TR43":
					array_push($country , "Kütahya");
				break;
				case "TR44":
					array_push($country , "Malatya");
				break;
				case "TR45":
					array_push($country , "Manisa");
				break;
				case "TR46":
					array_push($country , "Kahramanmaraş");
				break;
				case "TR47":
					array_push($country , "Mardin");
				break;
				case "TR48":
					array_push($country , "Muğla");
				break;
				case "TR49":
					array_push($country , "Muş");
				break;
				case "TR50":
					array_push($country , "Nevşehir");
				break;
				case "TR51":
					array_push($country , "Niğde");
				break;
				case "TR52":
					array_push($country , "Ordu");
				break;
				case "TR53":
					array_push($country , "Rize");
				break;
				case "TR54":
					array_push($country , "Sakarya");
				break;
				case "TR55":
					array_push($country , "Samsun");
				break;
				case "TR56":
					array_push($country , "Siirt");
				break;
				case "TR57":
					array_push($country , "Sinop");
				break;
				case "TR58":
					array_push($country , "Sivas");
				break;
				case "TR59":
					array_push($country , "Tekirdağ");
				break;
				case "TR60":
					array_push($country , "Tokat");
				break;
				case "TR61":
					array_push($country , "Trabzon");
				break;
				case "TR62":
					array_push($country , "Tunceli");
				break;
				case "TR63":
					array_push($country , "Şanlıurfa");
				break;
				case "TR64":
					array_push($country , "Uşak");
				break;
				case "TR65":
					array_push($country , "Van");
				break;
				case "TR66":
					array_push($country , "Yozgat");
				break;
				case "TR67":
					array_push($country , "Zonguldak");
				break;
				case "TR68":
					array_push($country , "Aksaray");
				break;
				case "TR69":
					array_push($country , "Bayburt");
				break;
				case "Bayburt":
					array_push($country , "Karaman");
				break;
				case "TR71":
					array_push($country , "Kırıkkale");
				break;
				case "TR72":
					array_push($country , "Batman");
				break;
				case "TR73":
					array_push($country , "Şırnak");
				break;
				case "TR74":
					array_push($country , "Bartın");
				break;
				case "TR75":
					array_push($country , "Ardahan");
				break;
				case "TR76":
					array_push($country , "Iğdır");
				break;
				case "TR77":
					array_push($country , "Yalova");
				break;
				case "TR78":
					array_push($country , "Karabük");
				break;
				case "TR79":
					array_push($country , "Kilis");
				break;
				case "TR80":
					array_push($country , "Osmaniye");
				break;
				case "TR81":
					array_push($country , "Düzce");
				break;
			}
		}
		if($codecountry=='US'){
			array_push($country , "United States");
			switch ($codestate) {
				case "AL":
					array_push($country , "Alabama");
				break;
				case "AK":
					array_push($country , "Alaska");
				break;
				case "AZ":
					array_push($country , "Arizona");
				break;
				case "AR":
					array_push($country , "Arkansas");
				break;
				case "CA":
					array_push($country , "California");
				break;
				case "CO":
					array_push($country , "Colorado");
				break;
				case "CT":
					array_push($country , "Connecticut");
				break;
				case "DE":
					array_push($country , "Delaware");
				break;
				case "DC":
					array_push($country , "District Of Columbia");
				break;
				case "FL":
					array_push($country , "Florida");
				break;
				case "GA":
					array_push($country , "Georgia");
				break;
				case "HI":
					array_push($country , "Hawaii");
				break;
				case "Hawaii":
					array_push($country , "Idaho");
				break;
				case "IL":
					array_push($country , "Illinois");
				break;
				case "IN":
					array_push($country , "Indiana");
				break;
				case "IA":
					array_push($country , "Iowa");
				break;
				case "KS":
					array_push($country , "Kansas");
				break;
				case "KY":
					array_push($country , "Kentucky");
				break;
				case "LA":
					array_push($country , "Louisiana");
				break;
				case "ME":
					array_push($country , "Maine");
				break;
				case "MD":
					array_push($country , "Maryland");
				break;
				case "MA":
					array_push($country , "Massachusetts");
				break;
				case "MI":
					array_push($country , "Michigan");
				break;
				case "MN":
					array_push($country , "Minnesota");
				break;
				case "MS":
					array_push($country , "Mississippi");
				break;
				case "MO":
					array_push($country , "Missouri");
				break;
				case "MT":
					array_push($country , "Montana");
				break;
				case "NE":
					array_push($country , "Nebraska");
				break;
				case "NV":
					array_push($country , "Nevada");
				break;
				case "NH":
					array_push($country , "New Hampshire");
				break;
				case "NJ":
					array_push($country , "New Jersey");
				break;
				case "NM":
					array_push($country , "New Mexico");
				break;
				case "NY":
					array_push($country , "New York");
				break;
				case "NC":
					array_push($country , "North Carolina");
				break;
				case "ND":
					array_push($country , "North Dakota");
				break;
				case "OH":
					array_push($country , "Ohio");
				break;
				case "OK":
					array_push($country , "Oklahoma");
				break;
				case "OR":
					array_push($country , "Oregon");
				break;
				case "PA":
					array_push($country , "Pennsylvania");
				break;
				case "RI":
					array_push($country , "Rhode Island");
				break;
				case "SC":
					array_push($country , "South Carolina");
				break;
				case "SD":
					array_push($country , "South Dakota");
				break;
				case "TN":
					array_push($country , "Tennessee");
				break;
				case "TX":
					array_push($country , "Texas");
				break;
				case "UT":
					array_push($country , "Utah");
				break;
				case "VT":
					array_push($country , "Vermont");
				break;
				case "VA":
					array_push($country , "Virginia");
				break;
				case "WA":
					array_push($country , "Washington");
				break;
				case "WV":
					array_push($country , "West Virginia");
				break;
				case "WI":
					array_push($country , "Wisconsin");
				break;
				case "WY":
					array_push($country , "Wyoming");
				break;
				case "AA":
					array_push($country , "Armed Forces (AA)");
				break;
				case "AE":
					array_push($country , "Armed Forces (AE)");
				break;
				case "AP":
					array_push($country , "Armed Forces (AP)");
				break;
				case "AS":
					array_push($country , "American Samoa");
				break;
				case "GU":
					array_push($country , "Guam");
				break;
				case "MP":
					array_push($country , "Northern Mariana Islands");
				break;
				case "PR":
					array_push($country , "Puerto Rico");
				break;
				case "UM":
					array_push($country , "US Minor Outlying Islands");
				break;
				case "VI":
					array_push($country , "US Virgin Islands");
				break;
			}
		}
		if($codecountry=='AF'){
			array_push($country , "Afghanistan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AX'){
			array_push($country , "Åland Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AL'){
			array_push($country , "Albania");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='DZ'){
			array_push($country , "Algeria");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AD'){
			array_push($country , "Andorra");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AO'){
			array_push($country , "Angola");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AI'){
			array_push($country , "Anguilla");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AQ'){
			array_push($country , "Antarctica");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AG'){
			array_push($country , "Antigua and Barbuda");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AR'){
			array_push($country , "Argentina");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AM'){
			array_push($country , "Armenia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AW'){
			array_push($country , "Aruba");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AT'){
			array_push($country , "Austria");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AZ'){
			array_push($country , "Azerbaijan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BS'){
			array_push($country , "Bahamas");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BH'){
			array_push($country , "Bahrain");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BB'){
			array_push($country , "Barbados");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BY'){
			array_push($country , "Belarus");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BE'){
			array_push($country , "Belgium");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PW'){
			array_push($country , "Belau");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BZ'){
			array_push($country , "Belize");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BJ'){
			array_push($country , "Benin");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BM'){
			array_push($country , "Bermuda");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BT'){
			array_push($country , "Bhutan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BO'){
			array_push($country , "Bolivia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BQ'){
			array_push($country , "Bonaire, Saint Eustatius and Saba");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BA'){
			array_push($country , "Bosnia and Herzegovina");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BW'){
			array_push($country , "Botswana");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BV'){
			array_push($country , "Bouvet Island");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='IO'){
			array_push($country , "British Indian Ocean Territory");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='VG'){
			array_push($country , "British Virgin Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BN'){
			array_push($country , "Brunei");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BF'){
			array_push($country , "Burkina Faso");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BI'){
			array_push($country , "Burundi");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KH'){
			array_push($country , "Cambodia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CM'){
			array_push($country , "Cameroon");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CV'){
			array_push($country , "Cape Verde");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KY'){
			array_push($country , "Cayman Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CF'){
			array_push($country , "Central African Republic");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TD'){
			array_push($country , "Chad");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CL'){
			array_push($country , "Chile");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CX'){
			array_push($country , "Christmas Island");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CC'){
			array_push($country , "Cocos [ Keeling] Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CO'){
			array_push($country , "Colombia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KM'){
			array_push($country , "Comoros");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CG'){
			array_push($country , "Congo [ Brazzaville]");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CD'){
			array_push($country , "Congo [ Kinshasa]");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CK'){
			array_push($country , "Cook Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CR'){
			array_push($country , "Costa Rica");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='HR'){
			array_push($country , "Croatia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CU'){
			array_push($country , "Cuba");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CW'){
			array_push($country , "CuraÇao");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CY'){
			array_push($country , "Cyprus");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CZ'){
			array_push($country , "Czech Republic");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='DK'){
			array_push($country , "Denmark");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='DJ'){
			array_push($country , "Djibouti");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='DM'){
			array_push($country , "Dominica");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='DO'){
			array_push($country , "Dominican Republic");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='EC'){
			array_push($country , "Ecuador");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='EG'){
			array_push($country , "Egypt");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SV'){
			array_push($country , "El Salvador");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GQ'){
			array_push($country , "Equatorial Guinea");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='ER'){
			array_push($country , "Eritrea");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='EE'){
			array_push($country , "Estonia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='ET'){
			array_push($country , "Ethiopia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='FK'){
			array_push($country , "Falkland Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='FO'){
			array_push($country , "Faroe Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='FJ'){
			array_push($country , "Fiji");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='FI'){
			array_push($country , "Finland");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='FR'){
			array_push($country , "France");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GF'){
			array_push($country , "French Guiana");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PF'){
			array_push($country , "French Polynesia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TF'){
			array_push($country , "French Southern Territories");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GA'){
			array_push($country , "Gabon");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GM'){
			array_push($country , "Gambia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GE'){
			array_push($country , "Georgia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='DE'){
			array_push($country , "Germany");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GH'){
			array_push($country , "Ghana");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GI'){
			array_push($country , "Gibraltar");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GR'){
			array_push($country , "Greece");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GL'){
			array_push($country , "Greenland");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GD'){
			array_push($country , "Grenada");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GP'){
			array_push($country , "Guadeloupe");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GT'){
			array_push($country , "Guatemala");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GG'){
			array_push($country , "Guernsey");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GN'){
			array_push($country , "Guinea");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GW'){
			array_push($country , "Guinea-Bissau");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GY'){
			array_push($country , "Guyana");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='HT'){
			array_push($country , "Haiti");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='HM'){
			array_push($country , "Heard Island and McDonald Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='HN'){
			array_push($country , "Honduras");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='IS'){
			array_push($country , "Iceland");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='IR'){
			array_push($country , "Iran");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='IQ'){
			array_push($country , "Iraq");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='IE'){
			array_push($country , "Republic of Ireland");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='IM'){
			array_push($country , "Isle of Man");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='IL'){
			array_push($country , "Israel");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CI'){
			array_push($country , "Ivory Coast");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='JM'){
			array_push($country , "Jamaica");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='JE'){
			array_push($country , "Jersey");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='JO'){
			array_push($country , "Jordan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KZ'){
			array_push($country , "Kazakhstan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KE'){
			array_push($country , "Kenya");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KI'){
			array_push($country , "Kiribati");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KW'){
			array_push($country , "Kuwait");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KG'){
			array_push($country , "Kyrgyzstan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LA'){
			array_push($country , "Laos");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LV'){
			array_push($country , "Latvia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LB'){
			array_push($country , "Lebanon");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LS'){
			array_push($country , "Lesotho");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LR'){
			array_push($country , "Liberia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LY'){
			array_push($country , "Libya");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LI'){
			array_push($country , "Liechtenstein");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LT'){
			array_push($country , "Lithuania");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LU'){
			array_push($country , "Luxembourg");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MO'){
			array_push($country , "Macao S.A.R., China");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MK'){
			array_push($country , "Macedonia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MG'){
			array_push($country , "Madagascar");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MW'){
			array_push($country , "Malawi");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MV'){
			array_push($country , "Maldives");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='ML'){
			array_push($country , "Mali");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MT'){
			array_push($country , "Malta");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MH'){
			array_push($country , "Marshall Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MQ'){
			array_push($country , "Martinique");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MR'){
			array_push($country , "Mauritania");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MU'){
			array_push($country , "Mauritius");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='YT'){
			array_push($country , "Mayotte");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MX'){
			array_push($country , "Mexico");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='FM'){
			array_push($country , "Micronesia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MD'){
			array_push($country , "Moldova");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MC'){
			array_push($country , "Monaco");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MN'){
			array_push($country , "Mongolia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='ME'){
			array_push($country , "Montenegro");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MS'){
			array_push($country , "Montserrat");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MA'){
			array_push($country , "Morocco");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MZ'){
			array_push($country , "Mozambique");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MM'){
			array_push($country , "Myanmar");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NA'){
			array_push($country , "Namibia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NR'){
			array_push($country , "Nauru");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NP'){
			array_push($country , "Nepal");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NL'){
			array_push($country , "Netherlands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AN'){
			array_push($country , "Netherlands Antilles");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NC'){
			array_push($country , "New Caledonia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NI'){
			array_push($country , "Nicaragua");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NE'){
			array_push($country , "Niger");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NG'){
			array_push($country , "Nigeria");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NU'){
			array_push($country , "Niue");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NF'){
			array_push($country , "Norfolk Island");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KP'){
			array_push($country , "North Korea");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='NO'){
			array_push($country , "Norway");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='OM'){
			array_push($country , "Oman");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PK'){
			array_push($country , "Pakistan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PS'){
			array_push($country , "Palestinian Territory");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PA'){
			array_push($country , "Panama");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PG'){
			array_push($country , "Papua New Guinea");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PY'){
			array_push($country , "Paraguay");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PH'){
			array_push($country , "Philippines");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PN'){
			array_push($country , "Pitcairn");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PL'){
			array_push($country , "Poland");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PT'){
			array_push($country , "Portugal");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='QA'){
			array_push($country , "Qatar");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='RE'){
			array_push($country , "Reunion");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='RO'){
			array_push($country , "Romania");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='RU'){
			array_push($country , "Russia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='RW'){
			array_push($country , "Rwanda");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='BL'){
			array_push($country , "Saint Barthélemy");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SH'){
			array_push($country , "Saint Helena");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KN'){
			array_push($country , "Saint Kitts and Nevis");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LC'){
			array_push($country , "Saint Lucia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='MF'){
			array_push($country , "Saint Martin [ French part]");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SX'){
			array_push($country , "Saint Martin [ Dutch part]");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='PM'){
			array_push($country , "Saint Pierre and Miquelon");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='VC'){
			array_push($country , "Saint Vincent and the Grenadines");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SM'){
			array_push($country , "San Marino");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='ST'){
			array_push($country , "São Tomé and Príncipe");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SA'){
			array_push($country , "Saudi Arabia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SN'){
			array_push($country , "Senegal");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='RS'){
			array_push($country , "Serbia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SC'){
			array_push($country , "Seychelles");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SL'){
			array_push($country , "Sierra Leone");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SG'){
			array_push($country , "Singapore");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SK'){
			array_push($country , "Slovakia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SI'){
			array_push($country , "Slovenia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SB'){
			array_push($country , "Solomon Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SO'){
			array_push($country , "Somalia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GS'){
			array_push($country , "South Georgia/Sandwich Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='KR'){
			array_push($country , "South Korea");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SS'){
			array_push($country , "South Sudan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='LK'){
			array_push($country , "Sri Lanka");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SD'){
			array_push($country , "Sudan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SR'){
			array_push($country , "Suriname");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SJ'){
			array_push($country , "Svalbard and Jan Mayen");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SZ'){
			array_push($country , "Swaziland");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SE'){
			array_push($country , "Sweden");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='CH'){
			array_push($country , "Switzerland");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='SY'){
			array_push($country , "Syria");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TW'){
			array_push($country , "Taiwan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TJ'){
			array_push($country , "Tajikistan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TZ'){
			array_push($country , "Tanzania");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TL'){
			array_push($country , "Timor-Leste");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TG'){
			array_push($country , "Togo");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TK'){
			array_push($country , "Tokelau");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TO'){
			array_push($country , "Tonga");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TT'){
			array_push($country , "Trinidad and Tobago");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TN'){
			array_push($country , "Tunisia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TM'){
			array_push($country , "Turkmenistan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TC'){
			array_push($country , "Turks and Caicos Islands");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='TV'){
			array_push($country , "Tuvalu");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='UG'){
			array_push($country , "Uganda");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='UA'){
			array_push($country , "Ukraine");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='AE'){
			array_push($country , "United Arab Emirates");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='GB'){
			array_push($country , "United Kingdom [ UK]");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='UY'){
			array_push($country , "Uruguay");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='UZ'){
			array_push($country , "Uzbekistan");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='VU'){
			array_push($country , "Vanuatu");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='VA'){
			array_push($country , "Vatican");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='VE'){
			array_push($country , "Venezuela");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='VN'){
			array_push($country , "Vietnam");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='WF'){
			array_push($country , "Wallis and Futuna");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='EH'){
			array_push($country , "Western Sahara");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='WS'){
			array_push($country , "Western Samoa");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='YE'){
			array_push($country , "Yemen");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='ZM'){
			array_push($country , "Zambia");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}
		if($codecountry=='ZW'){
			array_push($country , "Zimbabwe");
			switch ($codestate) {
				default:
					array_push($country , "-");
				break;
			}
		}

		return $country;
} 


?>
