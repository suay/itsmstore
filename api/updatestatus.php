<?php
/* 	backend call for supdate old plan to new plan
*	and save log difference type =>exm. log upgrade plan, log transaction recurring 
*	method : POST
*	input parameter type_pay = recurring or upgrade, old_order =  order before subscription, subscription_order = oder subscription,cus_email = customer email, detail_upgrade = detail plan for upgrade
*	return json
*	10-05-2021
*/
// $data = json_decode(file_get_contents('php://input'), true); 

	if( empty($_REQUEST['type']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Type.') );
			exit;
	}

	if( empty($_REQUEST['order_id']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Order.') );
			exit;
	}

	if( empty($_REQUEST['status_order']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Status order.') );
			exit;
	}

	if( empty($_REQUEST['status_subscription']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Status Subscription.') );
			exit;
	}

	// $dataresponse = $data['res_recurring'];
	$order_id = $_REQUEST['order_id'];
	$type = $_REQUEST['type'];
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
	/*================== type reject =====================*/
	if($type=="reject"){
		//find email
		$findcustomer_email = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta WHERE post_id =".$_REQUEST['order_id']." and meta_key='_billing_email'");
		$rowcusemail = mysqli_fetch_array($findcustomer_email, MYSQLI_ASSOC);
		$customer_email = $rowcusemail['meta_value'];

		//find website
		$findorder_website = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta WHERE post_id =".$_REQUEST['order_id']." and meta_key='_order_website'");
		$rowwebsites = mysqli_fetch_array($findorder_website, MYSQLI_ASSOC);
		$namecustomer = $rowwebsites['meta_value'];


		$resultfind_subid = mysqli_query($mysqli, "SELECT order_item_name,meta_key,meta_value FROM wp_woocommerce_order_items a left join wp_woocommerce_order_itemmeta b on(a.order_item_id=b.order_item_id) where a.order_id='".$_REQUEST['order_id']."' and meta_key='_subscription_id'");
		$rowsubid = mysqli_fetch_array($resultfind_subid, MYSQLI_ASSOC);
		$subscription_id = $rowsubid['meta_value'];

		//update status order
		$findorder_key = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta WHERE post_id =".$_REQUEST['order_id']." and meta_key='_order_key'");
		$roworderkey = mysqli_fetch_array($findorder_key, MYSQLI_ASSOC);
		$order_key = $roworderkey['meta_value'];
		//wc-wc-on-hold
		mysqli_query($mysqli,"UPDATE wp_posts SET post_status='wc-on-hold',post_modified = NOW(),post_modified_gmt=NOW() WHERE post_password='".$order_key."'");

		switch($_REQUEST['status_subscription']){
			case "suspended":
				$status_up = "suspended";
			break;
			case "suspend":
				$status_up = "suspended";
			break;
			default:
				$status_up = $_REQUEST['status_subscription'];
			break;
		}

		//update status subscription
		mysqli_query($mysqli,"UPDATE wp_postmeta SET meta_value = '".$status_up."' WHERE post_id =".$subscription_id." and meta_key='status'");
		
		
		//send email reject
		$subject = "Your NetkaQuartz Service Desk X on Cloud is Fail Recurring Payment."; //"REJECT FOR YOUR PAYMENT TO Netka System Co., Ltd.";
		
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
	                                              <p>Fail to made a credit card recurring payment to Netka System Co., Ltd.</p>
	                                              <p>Please check card limit  or check card expirce and re-pay to website click button re-pay.</p>
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
				$mailto    = $customer_email; //"wattawadee.raksachon@gmail.com"; //$_request['cus_email'];
				$header    = "From: " . $from_name . " <" . $from_mail . ">\n";
				$header .= "Reply-To: " . $replyto . "\n";
				$header .= "MIME-Version: 1.0\n";
				$header .= "Content-type:text/html;charset=UTF-8" . "\n";
				
				if ( mail($mailto, $subject, $message, $header) ){
					$message1 = 'Mail Send Successfully';
				}else{
					$message1 = 'Main not send';
				}
	  	
	  	ob_end_flush(); 

		echo json_encode( array('mscode'=>'Success','message'=>'successful') );

	}else if($type=="repay"){ /*================== type repay ==============*/
		//update status order
		$findorder_key = mysqli_query($mysqli, "SELECT meta_value FROM wp_postmeta WHERE post_id =".$_REQUEST['order_id']." and meta_key='_order_key'");
		$roworderkey = mysqli_fetch_array($findorder_key, MYSQLI_ASSOC);
		$order_key = $roworderkey['meta_value'];
		//wc-wc-completed
		//mysqli_query($mysqli,"UPDATE wp_posts SET post_status='wc-completed',post_modified = NOW(),post_modified_gmt=NOW() WHERE post_password='".$order_key."'");
		//update status subscription
		//mysqli_query($mysqli,"UPDATE wp_postmeta SET meta_value = '".$status_up."' WHERE post_id =".$subscription_id." and meta_key='status'");

		//cancell old recurring order
		$findlog_recurring = "SELECT data_res FROM log_recurring_res where order_id='".$_REQUEST['order_id']."' order by datetimes desc limit 1";
		$result_log = mysqli_query($mysqli, $findlog_recurring);
		while ($rowlog = mysqli_fetch_array($result_log, MYSQLI_ASSOC)) {
			$data_all = json_decode($rowlog['data_res']);
			$version = $data_all->version;
			$request_timestamp = $data_all->request_timestamp;
			$merchant_id = $data_all->merchant_id;
			$order_id = $data_all->order_id;
			$invoice_no = $data_all->invoice_no;
			$currency = $data_all->currency;
			$amount = $data_all->amount;
			$transaction_ref = $data_all->transaction_ref;
			$approval_code = $data_all->approval_code;
			$eci = $data_all->eci;
			$transaction_datetime = $data_all->transaction_datetime;
			$payment_channel = $data_all->payment_channel;
			$payment_status = $data_all->payment_status;
			$channel_response_code = $data_all->channel_response_code;
			$channel_response_desc = $data_all->channel_response_desc;
			$masked_pan = $data_all->masked_pan;
			$stored_card_unique_id = $data_all->stored_card_unique_id;
			$backend_invoice = $data_all->backend_invoice;
			$paid_channel = $data_all->paid_channel;
			$paid_agent = $data_all->paid_agent;
			$recurring_unique_id = $data_all->recurring_unique_id;
			$user_defined_1 = $data_all->user_defined_1;
			$user_defined_2 = $data_all->user_defined_2;
			$user_defined_3 = $data_all->user_defined_3;
			$user_defined_4 = $data_all->user_defined_4;
			$user_defined_5 = $data_all->user_defined_5;
			$browser_info = $data_all->browser_info;
			$hash_value = $data_all->hash_value;
		}

		//find subscription order
		$find_subscriptionid = mysqli_query($mysqli, "SELECT meta_value FROM wp_woocommerce_order_items a left join wp_woocommerce_order_itemmeta b on(a.order_item_id=b.order_item_id) where a.order_id='".$_REQUEST['order_id']."' and meta_key='_subscription_id'");
		$rowsubscriptionid = mysqli_fetch_array($find_subscriptionid, MYSQLI_ASSOC);
		$datapost = json_encode( array('subscription_id'=>$rowsubscriptionid['meta_value'],
									'type_inquiry'=>'C',
									//'order_id'=>$order_ids,
									'recurring_unique_id'=>$recurring_unique_id
								) );

		var_dump($datapost); 
		// $curl = curl_init();

		// curl_setopt_array($curl, array(
		//   CURLOPT_URL => 'http://localhost:8000/newpayment1/public/api/inquiry',
		//   CURLOPT_RETURNTRANSFER => true,
		//   CURLOPT_ENCODING => '',
		//   CURLOPT_MAXREDIRS => 10,
		//   CURLOPT_TIMEOUT => 0,
		//   CURLOPT_FOLLOWLOCATION => true,
		//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		//   CURLOPT_CUSTOMREQUEST => 'POST',
		//   CURLOPT_POSTFIELDS =>$datapost,
		//   CURLOPT_HTTPHEADER => array(
		//     'Content-Type: application/json'
		//   ),
		// ));

		// $response = curl_exec($curl);

		// curl_close($curl);

		//echo $response;
	}else{
		echo json_encode( array('mscode'=>'error','message'=>'someting went wrong.') );
	}
	


?>