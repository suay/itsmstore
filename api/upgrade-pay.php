<?php
/* 	backend call for supdate old plan to new plan
*	and save log difference type =>exm. log upgrade plan, log transaction recurring 
*	method : POST
*	input parameter type_pay = recurring or upgrade, old_order =  order before subscription, subscription_order = oder subscription,cus_email = customer email, detail_upgrade = detail plan for upgrade
*	return json
*	10-05-2021
*/
$data = json_decode(file_get_contents('php://input'), true); 

	if( empty($data['type_pay']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Type.') );
			exit;
	}

	if( empty($data['old_order']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Order.') );
			exit;
	}

	if( empty($data['subscription_order']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Subscription.') );
			exit;
	}

	if( empty($data['cus_email']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Email.') );
			exit;
	}

	if( empty($data['detail_upgrade']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Detail for Upgrade.') );
			exit;
	}

	$dataresponse = $data['res_recurring'];
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

	//check type pay if recurring save log only , upgrade => up
	if( $data['type_pay']=='recurring' ){
		//find subsctiption id before 
		// $find_subscriptionid = "SELECT meta_value FROM wp_woocommerce_order_items a left join wp_woocommerce_order_itemmeta b on(a.order_item_id=b.order_item_id) where a.order_id='".$data['old_order']."' and meta_key='_subscription_id'";
		// $resultfind_subscription = mysqli_query($mysqli, $find_subscriptionid);
		// while ($rowfind_sub = mysqli_fetch_array($resultfind_subscription, MYSQLI_ASSOC)) {
		// 	$subscription_id = $rowfind_sub['product_id'];
		// }

		//insert log response recurring
		$sqlre = "INSERT INTO log_recurring_res (id_recurring_res, order_id, data_res ) VALUES('','".$data['old_order']."','".json_encode($data['res_recurring'])."')";
		mysqli_query($mysqli, $sqlre);


		echo json_encode( array('mscode'=>'Success','message'=>'log Success.') );

	}else{
		//old7334 Plan Starter - 15 20THBto7334 Plan Starter - 20 25THB
		$datalist = explode('to', $data['detail_upgrade']);
		$findp =  explode(':',$datalist[0]);
		$idproduct_old = $findp[0];
		$sku_old = $findp[1];
		$nameproduct_old = $findp[2];
		$price_old = end($findp);
		$findp_new = explode(':', $datalist[1]);
		$idproduct_new = $findp_new[0];
		$sku_new = $findp_new[1];
		$nameproduct_new = $findp_new[2];
		$price_new = substr(end($findp_new), 0, -3); 
		$price_format = number_format($price_new,2,'.','');
		$end_newsku = explode('-', $sku_new);
		$lastend_new = end($end_newsku);

		//find variant id 
		$sql_variant = "SELECT product_id FROM wp_wc_product_meta_lookup where sku='".$sku_new."' and use_upgrade <>0" ;
		$resultvariant = mysqli_query($mysqli, $sql_variant);
		while ($rowvar = mysqli_fetch_array($resultvariant, MYSQLI_ASSOC)) {
			$varian_id = $rowvar['product_id'];
		}
		//put data old for edit variantion
		$sqlold_detail = "SELECT meta_value FROM `wp_postmeta` where post_id='".$data['subscription_order']."' and meta_key='variation'";
		$resultold = mysqli_query($mysqli, $sqlold_detail);
		while ($rowold = mysqli_fetch_array($resultold, MYSQLI_ASSOC)) {
			$detail_var = $rowold['meta_value'];
			$listdatas = explode(':', $detail_var);
			$findda = substr($listdatas[6], 0, -3);
			$findda1 = explode('-', $findda);
		}
		$full_detail = $listdatas[0].':'.$listdatas[1].':'.$listdatas[2].':'.$listdatas[3].':'.$listdatas[4].':'.$listdatas[5].':'.$findda1[0].'-'.$lastend_new.'";}';
					
		/* ===================== find log recurring ========================== */
		$findlog_recurring = "SELECT data_res FROM log_recurring_res where order_id='".$data['old_order']."' order by datetimes desc limit 1";
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
		// var_dump($recurring_unique_id);
		
		// exit;

// var_dump($datalist[0],$datalist[1],$idproduct_new,$sku_new,$nameproduct_new,$price_new,$varian_id);
// exit;
		// case upgrade
		//save log change plan
		$sqlupgrade = "INSERT INTO log_upgrade (respone_id, order_id, subscription_id, data_response ) VALUES('','".$data['old_order']."','".$data['subscription_order']."','".$data['detail_upgrade']."')";
		mysqli_query($mysqli, $sqlupgrade);

		//update tbl postmeta 
		//price 

		$sqlupdate_lineprice = "UPDATE wp_postmeta SET meta_value = '".$price_new."' WHERE meta_key in ('line_subtotal','line_total') and post_id='".$data['subscription_order']."'";
		mysqli_query($mysqli, $sqlupdate_lineprice);
		//update price order_total,subscription_total,order_subtotal
		$sql_orderprice = "UPDATE wp_postmeta SET meta_value = '".$price_format."' WHERE meta_key in ('order_total','subscription_total','order_subtotal') and post_id='".$data['subscription_order']."'";
		mysqli_query($mysqli, $sql_orderprice);
		//update product name
		$sql_productname = "UPDATE wp_postmeta SET meta_value = '".$nameproduct_new."' WHERE meta_key in ('product_name') and post_id='".$data['subscription_order']."'";
		mysqli_query($mysqli, $sql_productname);

		//sql_productid,
		$sql_productidupdate = "UPDATE wp_postmeta SET meta_value = '".$idproduct_new."' WHERE meta_key in ('product_id') and post_id='".$data['subscription_order']."'";
		mysqli_query($mysqli, $sql_productidupdate);

		//update and variant id
		$sql_variantupdate = "UPDATE wp_postmeta SET meta_value = '".$varian_id."' WHERE meta_key in ('variation_id') and post_id='".$data['subscription_order']."'";
		mysqli_query($mysqli, $sql_variantupdate);

		//update detail variant json
		$sql_variantdetail = "UPDATE wp_postmeta SET meta_value = '".$full_detail."' WHERE meta_key in ('variation') and post_id='".$data['subscription_order']."'";
		mysqli_query($mysqli, $sql_variantdetail);

		/* =================== curl api inquiry cancel =========================== */
		$datapost = json_encode( array('subscription_id'=>$data['subscription_order'],
									'type_inquiry'=>'C',
									//'order_id'=>$order_ids,
									'recurring_unique_id'=>$recurring_unique_id
								) );
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'http://localhost:8000/newpayment1/public/api/inquiry',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$datapost,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$result_res = json_decode($response);
		if($result_res['mscode']=='Success'){
			$msg = $result_res['message'];
		}else{
			$msg = $result_res['message'];
		}

	// 	/* ===================== call api recurring cancal========================== */
	// //Merchant's account information
	// $merchantID = "764764000004784";		//Get MerchantID when opening account with 2C2P
	// $secretKey = "D97EE54180CF124D12961DB49A21BBF4D6C1AE3A8545CF062DC0517EDD3A7F04";	//Get SecretKey from 2C2P PGW Dashboard
	// //production key
	// // $secretKey = "DF6B6D630191EBAB3BB0DF6E2A35DCB11B8F7D7468E815E676570D86FE682833";	//Get SecretKey from 2C2P PGW Dashboard

	// //Request Information  
	// $version = "2.4";
	// $processType = "C" ;
	// $recurringUniqueID = $recurring_unique_id;	 
	// $recurringStatus = "";	 
	// $amount = "";	 
	// $allowAccumulate = "";	 
	// $maxAccumulateAmount= "";	 
	// $recurringInterval = "";	 
	// $recurringCount = "";	 
	// $chargeNextDate="";
	// $chargeOnDate="";
	
	// //Construct signature string
	// $stringToHash = $version . $merchantID . $recurringUniqueID . $processType . $recurringStatus . $amount . $allowAccumulate . $maxAccumulateAmount . $recurringInterval . $recurringCount . $chargeNextDate . $chargeOnDate;
	// $hash = strtoupper(hash_hmac('sha256', $stringToHash ,$secretKey, false));	//Compute hash value

	// //Construct request message
	// $xml = "<RecurringMaintenanceRequest>
	// 		<version>$version</version> 
	// 		<merchantID>$merchantID</merchantID>
	// 		<recurringUniqueID>$recurringUniqueID</recurringUniqueID>
	// 		<processType>$processType</processType>
	// 		<recurringStatus>$recurringStatus</recurringStatus>
	// 		<amount>$amount</amount>
	// 		<allowAccumulate>$allowAccumulate</allowAccumulate>
	// 		<maxAccumulateAmount>$maxAccumulateAmount</maxAccumulateAmount>
	// 		<recurringInterval>$recurringInterval</recurringInterval>
	// 		<recurringCount>$recurringCount</recurringCount>
	// 		<chargeNextDate>$chargeNextDate</chargeNextDate>
	// 		<chargeOnDate>$chargeOnDate</chargeOnDate>
	// 		<hashValue>$hash</hashValue>
	// 		</RecurringMaintenanceRequest>";  

	// include_once('pkcs7.php');
	
	// $pkcs7 = new pkcs7();
	// $payload = $pkcs7->encrypt($xml,"./keys/demo2.crt"); //Encrypt payload
	
 	
				
	// include_once('HTTP.php');
	
	// //Send request to 2C2P PGW and get back response
	// $http = new HTTP();
 // 	$response = $http->post("https://demo2.2c2p.com/2C2PFrontend/PaymentActionV2/PaymentAction.aspx","paymentRequest=".$payload);
	 
	// //Decrypt response message and display  
	// $response = $pkcs7->decrypt($response,"./keys/demo2.crt","./keys/demo2.pem","2c2p");   
	// //echo "Response:<br/><textarea style='width:100%;height:80px'>". $response."</textarea>"; 
 
	// //Validate response Hash
	// $resXml=simplexml_load_string($response); 
	// $res_version = $resXml->version;
	// $res_timeStamp = $resXml->timeStamp;
	// $res_respCode = $resXml->respCode;
	// $res_respReason = $resXml->respReason;
	// $res_recurringUniqueID = $resXml->recurringUniqueID;
	// $res_recurringStatus = $resXml->recurringStatus;
	// $res_invoicePrefix = $resXml->invoicePrefix;
	// $res_currency = $resXml->currency;
	// $res_amount = $resXml->amount;
	// $res_maskedCardNo = $resXml->maskedCardNo;
	// $res_allowAccumulate = $resXml->allowAccumulate;
	// $res_maxAccumulateAmount = $resXml->maxAccumulateAmount; 
	// $res_recurringInterval = $resXml->recurringInterval;
	// $res_recurringCount = $resXml->recurringCount; 
	// $res_currentCount = $resXml->currentCount;
	// $res_chargeNextDate = $resXml->chargeNextDate;
	// $res_chargeOnDate = $resXml->chargeOnDate;
	
	
	// //Compute response hash
	// $res_stringToHash = $res_version . $res_respCode . $res_recurringUniqueID . $res_recurringStatus . $res_invoicePrefix . $res_currency . 
	// $res_amount . $res_maskedCardNo . $res_allowAccumulate . $res_maxAccumulateAmount . $res_recurringInterval . $res_recurringCount . $res_currentCount . $res_chargeNextDate . $res_chargeOnDate;
	
	// $res_responseHash = strtoupper(hash_hmac('sha256',$res_stringToHash,$secretKey, false));	//Compute hash value
	// //echo "<br/>hash: ".$res_responseHash."<br/>"; 
	// if(strtolower($resXml->hashValue) == strtolower($res_responseHash)){ 
	// 	$msg =  "valid response"; 
	// }else{ 
	// 	$msg =  "invalid response"; 
	// }
//$data['res_recurring'];
	// exit;
		/* ===================== update log recurring ================================== */
		$sqlinsertlog= "INSERT INTO log_recurring_res (id_recurring_res, order_id, data_res ) VALUES('','".$data['old_order']."','".json_encode($data['res_recurring'])."')";
		mysqli_query($mysqli, $sqlinsertlog);

		echo json_encode( array('mscode'=>'Success','message'=>$msg,'recurringuniqueid'=>$recurring_unique_id) );
	}


?>