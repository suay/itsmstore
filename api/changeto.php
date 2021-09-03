<?php
/* 	backend call for supdate old plan to new plan
*	and save log difference type =>exm. log upgrade plan, log transaction recurring 
*	method : POST
*	input parameter type_pay = recurring or upgrade, old_order =  order before subscription, subscription_order = oder subscription,cus_email = customer email, detail_upgrade = detail plan for upgrade
*	return json
*	10-05-2021
*/
date_default_timezone_set("Asia/Bangkok");

	if( empty($_REQUEST['subscription_id']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Subscription Id.') );
			exit;
	}

	if( empty($_REQUEST['plan_old']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Old Plan.') );
			exit;
	}

	if( empty($_REQUEST['plan_new']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request New Plan.') );
			exit;
	}

	if( empty($_REQUEST['customer_email']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Email.') );
			exit;
	}

	if( empty($_REQUEST['detail_plan']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Detail for Change to.') );
			exit;
	}

	if( empty($_REQUEST['order_id']) ){
			echo json_encode( array('mscode'=>'Error','message'=>'Request Order.') );
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

	// var_dump($_REQUEST['detail_plan']);exit;
		//old7334 Plan Starter - 15 20THBto7334 Plan Starter - 20 25THB
		$datalist = explode('to', $_REQUEST['detail_plan']);
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
		$amountfor_up = str_pad(number_format($price_new,2,"",""), 12, '0', STR_PAD_LEFT);
// var_dump($amountfor_up);exit;
		//update detail change to plan
		//find variant id 
		$sql_variant = "SELECT product_id FROM wp_wc_product_meta_lookup where sku='".$sku_new."' and use_upgrade <>0";
		$resultvariant = mysqli_query($mysqli, $sql_variant);
		while ($rowvar = mysqli_fetch_array($resultvariant, MYSQLI_ASSOC)) {
			$varian_id = $rowvar['product_id'];
		}

		//put data old for edit variantion
		$sqlold_detail = "SELECT meta_value FROM wp_postmeta where post_id='".$_REQUEST['subscription_id']."' and meta_key='variation'";
		$resultold = mysqli_query($mysqli, $sqlold_detail);
		while ($rowold = mysqli_fetch_array($resultold, MYSQLI_ASSOC)) {
			$detail_var = $rowold['meta_value'];
			$listdatas = explode(':', $detail_var);
			$findda = substr($listdatas[6], 0, -3);
			$findda1 = explode('-', $findda);
		}
		$full_detail = $listdatas[0].':'.$listdatas[1].':'.$listdatas[2].':'.$listdatas[3].':'.$listdatas[4].':'.$listdatas[5].':'.$findda1[0].'-'.$lastend_new.'";}';

		$sqlupdate_lineprice = "UPDATE wp_postmeta SET meta_value = '".$price_new."' WHERE meta_key in ('line_subtotal','line_total') and post_id='".$_REQUEST['subscription_id']."'";
		// mysqli_query($mysqli, $sqlupdate_lineprice);
		//update price order_total,subscription_total,order_subtotal
		$sql_orderprice = "UPDATE wp_postmeta SET meta_value = '".$price_format."' WHERE meta_key in ('order_total','subscription_total','order_subtotal') and post_id='".$_REQUEST['subscription_id']."'";
		// mysqli_query($mysqli, $sql_orderprice);
		//update product name
		$sql_productname = "UPDATE wp_postmeta SET meta_value = '".$nameproduct_new."' WHERE meta_key in ('product_name') and post_id='".$_REQUEST['subscription_id']."'";
		// mysqli_query($mysqli, $sql_productname);

		//sql_productid,
		$sql_productidupdate = "UPDATE wp_postmeta SET meta_value = '".$idproduct_new."' WHERE meta_key in ('product_id') and post_id='".$_REQUEST['subscription_id']."'";
		// mysqli_query($mysqli, $sql_productidupdate);

		//update and variant id
		$sql_variantupdate = "UPDATE wp_postmeta SET meta_value = '".$varian_id."' WHERE meta_key in ('variation_id') and post_id='".$_REQUEST['subscription_id']."'";
		// mysqli_query($mysqli, $sql_variantupdate);

		//update detail variant json
		$sql_variantdetail = "UPDATE wp_postmeta SET meta_value = '".$full_detail."' WHERE meta_key in ('variation') and post_id='".$_REQUEST['subscription_id']."'";
		// mysqli_query($mysqli, $sql_variantdetail);


		/* ===================== find log recurring ========================== */
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

		/* =================== curl api inquiry cancel =========================== */
		$datapost = json_encode( array('subscription_id'=>$_REQUEST['subscription_id'],
									'type_inquiry'=>'U',
									//'order_id'=>$order_ids,
									'recurring_unique_id'=>$recurring_unique_id,
									'amount' => $amountfor_up,
									'intervals' => $_REQUEST['intervals'],
									'setdays' => $_REQUEST['setdays']
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



		//save log change plan
		$sqldowngrade = "INSERT INTO log_downgrade (id_logdown, order_id, subscription_id, 	detail_downgrade, request_downgrade, response_downgrade ) VALUES('','".$_REQUEST['order_id']."','".$_REQUEST['subscription_id']."','".$_REQUEST['detail_plan']."','".$datapost."','".$response."')";
		// mysqli_query($mysqli, $sqldowngrade);

			/* =================== curl api downgrade =========================== */
		
		$find_data = explode(":", $user_defined_2); //changja:M:5:M:wattawadee@netka.com:TH:TH-10
		$newp = explode("-", $sku_new);
		$newpackage = strtoupper($newp[1]); //package
		$newagent = $newp[2]; //agent
		$sitename = $find_data[0];
		$customer_email = $find_data[4];
		$region = $find_data[5];
		$country = $find_data[6];
		//$newdata_downgrade = $find_data[0].':'.$newpackage.':'.$newagent.':'.$newpackage.':'.$find_data[4].':'.$find_data[5].':'.$find_data[6]; //sitename:package:agent:typepackage:customer email:timezone:contry
		$rp = 0;
		$password = ''; 
		$postdowngrade = json_encode( array('plan_down' => $newpackage,
														'agent_down' => $newagent,
														'type_down' => $newpackage,
														'cus_email' => $customer_email,
														'region' => $region,
														'country' => $country,
														'rp' => $rp,
														'passwords' => $password,
														'sitename' => $sitename,
														'intervals' => $user_defined_1
													) );
		$curldo = curl_init();

		curl_setopt_array($curldo, array(
		  CURLOPT_URL => 'http://localhost:8000/newpayment1/public/api/downgrade',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$postdowngrade,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curldo);

		curl_close($curldo);


// var_dump($postdowngrade);
// exit;
		/* =================== curl send pdf receive =========================== */
		//send email receive
		$datapostdowngrade = 'old_order='.$_REQUEST['order_id'].'&subscription_order='.$_REQUEST['subscription_id'].'&cus_email='.$_REQUEST['customer_email'].'&detail_upgrade='.$_REQUEST['detail_plan'].'&paydiff='.$amountfor_up.'&order_id='.$order_id.'&rp=downgrade';
				
           $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://localhost:8000/itsmstore4/api/receiveupgrade.php',//https://netkasystem.com/itsmstore/api/
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $datapostdowngrade,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
              ),
            ));

          $response = curl_exec($curl);

          curl_close($curl);


		$redirect = "https://staging2.netkasystem.com/itsmstore/my-account/view-subscription/".$_REQUEST['subscription_id'];
		header( "refresh:5;url=$redirect" );

?>