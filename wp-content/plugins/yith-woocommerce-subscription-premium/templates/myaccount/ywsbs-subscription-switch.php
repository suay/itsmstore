<?php
/**
 * Subscription switch
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscription Current Subscription.
 * @var array              $switchable_variations How to show the actions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$old_variation = wc_get_product( $subscription->get_variation_id() );
add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_true' );

if( !empty($subscription->payment_due_date) && (date("Y-m-d") <= date('Y-m-d ',$subscription->payment_due_date)) ){

	// echo $old_variation->price;exit;
	// var_dump($subscription->id);exit;
	date_default_timezone_set("Asia/Bangkok");
	$datenow = date("Y-m-d H:i:s");
	$dateexpired = $subscription->payment_due_date;
	$date1_ts = strtotime($datenow);
	$date2_ts = $dateexpired;
	$diff = $date2_ts - $date1_ts;
	$datediff =  round($diff / 86400);
	//echo $datediff;
	//(new_packet_price/365)*(total_day_before_expired)
	$billing_address    = $subscription->get_address_fields( 'billing', true );
	$customer_email = $billing_address['email'];
	$billing_address_state = $billing_address['state'];
	$billing_address_country = $billing_address['country'];
	$old_price = $old_variation->price;

	$value_order = $subscription->get_order_ids();
	// var_dump($value_order);exit;
	global $wpdb;
	$notes = $wpdb->get_results( "SELECT meta_value FROM {$wpdb->prefix}postmeta where post_id = '".$value_order[0]."' and meta_key ='_order_website'");
	foreach ($notes as $notest) {
		$notest = $notest;
	}
	$sitename = $notes[0]->meta_value;

	//find date expired old
	$sqlfind_exp = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta where post_id='".$subscription->get_id()."' and meta_key='payment_due_date'");
	foreach ($sqlfind_exp as $valuefind) {
		$date_expirce_old = $valuefind;
		//$expirce_old = date('dmY',$date_expirce_old->meta_value);
	}
	
	// var_dump($ckdatenow,$check_expirce);
	// var_dump($value_order[0]);
	// if($ckdatenow <= $check_expirce): // 2021-07-01 < 2021-06-18
		
?>

<div class="ywsbs-dropdown-wrapper">
	<a href="#"
		onclick="return false;"><?php echo esc_html( get_option( 'ywsbs_text_switch_plan' ) ); ?></a>
	<div class="ywsbs-dropdown">
		<?php foreach ( $switchable_variations as $variation_id ):
			$variation = wc_get_product( $variation_id );
			$relashionship = YWSBS_Subscription_Switch::get_switch_relationship_text( $old_variation, $variation );
			$diff_prices = (($variation->get_price() - $old_price) / 365) * $datediff;
			$newprice_notformat =  round( $diff_prices ,2);
			//var_dump($variation->get_price(),$old_price,$datediff);
			//$newprice_notformat = round( (($variation->get_price() / 365) * $datediff) - $old_price ,2);
			//if($newprice_notformat > 0):
			//var_dump($variation->get_name(),$newprice_notformat,$relashionship);
		?>
		<div class="ywsbs-dropdown-item">
				<p>
				<strong><?php echo $relashionship .' '.$variation->get_name(); ?>
				- <?php echo wc_price( $variation->get_price() ) . ' ' . '<span class="price_time_opt"> / ' . YWSBS_Subscription_Helper::get_subscription_period_for_price( $variation ) . '</span>' ?></strong>
				</p>
				<p class="ywsbs_plan_description"></p>
				<?php
					$ispertype = $variation->ywsbs_price_is_per; //price per 1,2,3
					$typecurringpay = $variation->ywsbs_price_time_option; //price per days,weeks,months,years
					$payment_description = $relashionship;
					//Get is store currency code from woocommerece.
			        $objWC_2c2p_currency =  new WC_2c2p_currency();
       			   	$currenyCode = get_option('woocommerce_currency');
			        //Get the store currency code.
			        foreach ($objWC_2c2p_currency->get_currency_code() as $key => $value) {
			            if($key === $currenyCode){
			                $currency_num =  $value['Num'];
			            }
		           }
						//set find subscription setting
				        switch($typecurringpay){
				            case "days":
				                $valueperday = $ispertype."days";
				                if($ispertype > 1){
				                    $interval = $ispertype ;
				                }else{
				                    $interval = "1";
				                }
				                $setday = date('dmY',$date_expirce_old->meta_value);

				            break;
				            case "weeks":
				                $valueperweek = $ispertype."week";
				                if($ispertype > 1){
				                    $interval = $ispertype * 7;
				                }else{
				                    $interval = "7";
				                }
				                $setday = date('dmY', $date_expirce_old->meta_value);

				            break;
				            case "months":
				                $valuepermonth = $ispertype."month";
				                if($ispertype > 1){
				                    $interval = $ispertype * 30;
				                }else{
				                    $interval = "30";
				                }
				                $setday = date('dmY',$date_expirce_old->meta_value);

				            break;
				            case "years":
				                $valueperyear = $ispertype."year";
				                $interval = "365";
				                $setday = date('dmY',$date_expirce_old->meta_value);
				            break;
				        }
 
				        //<a href=".esc_url( ywsbs_get_switch_to_subscription_url( $subscription, $variation_id ) )."title=".esc_attr( get_option( 'ywsbs_text_buy_new_plan' ) ).esc_html( get_option( 'ywsbs_text_buy_new_plan' ) )." style=''></a>"
				        switch($relashionship){
				        	case "Downgrade to":
					        	$urldowngrade = "http://localhost:8000/itsmstore4/api/changeto.php";
					        	$subscription_id = $subscription->get_id();
					        	$detail_plan = $old_variation->id.':'.$old_variation->sku.':'.$old_variation->name.':'.$old_variation->price.$subscription->order_currency.'to'.$variation->id.':'.$variation->sku.':'.$variation->name.':'.$variation->price.$subscription->order_currency;
				        		$button_redirect = '<form id="myform" method="post" action="'.$urldowngrade.'" onsubmit="return confirm(\'Are you sure you want to Change Plan?\');">
								<input type="hidden" name="subscription_id" id="subscription_id" value="'.$subscription_id.'"/>
								<input type="hidden" name="order_id" id="order_id" value="'.$value_order[0].'"/>
								<input type="hidden" name="plan_old" id="plan_old" value="'.$old_variation->id.'"/>
								<input type="hidden" name="plan_new" id="plan_new" value="'.$variation->id.'"/>
								<input type="hidden" name="currency" id="currency" value="'.$subscription->order_currency.'"/>
								<input type="hidden" name="customer_email" id="customer_email" value="'.$customer_email.'"/>
								<input type="hidden" name="detail_plan" id="detail_plan" value="'.$detail_plan.'"/>
								<input type="hidden" name="intervals" id="intervals" value="'.$interval.'"/>
								<input type="hidden" name="setdays" id="setdays" value="'.$setday.'"/>
								<input type="submit" name="submit" value="Change to" />
								</form>';
				        		// $button_redirect = "<p><a href='http://localhost:8000/itsmstore4/api/changeto.php?' title='Change to'>Change to</a></p>";
								$button_action = $button_redirect;//"Downgrade Now";
				        	break;
				        	default:
					        	$newprice = number_format($newprice_notformat,2,".",",");
								$bookorder_id = $subscription->get_id();
								$newproduct_sku = $variation->sku;
								$newproductname = preg_split('/(-|_|,| )/', $newproduct_sku);// get last for check plan
					            if(is_numeric(end($newproductname))==true){
					                $agent = end($newproductname);
					            }      
					            $package = strtoupper($newproductname[1]);
					            $objWC_Gateway_2c2p = new WC_Gateway_2c2p();
								$pg_2c2p_setting_values = $objWC_Gateway_2c2p->wc_2c2p_get_setting();

						        $redirect_url = $objWC_Gateway_2c2p->wc_2c2p_response_url('U'.$bookorder_id);//"http://localhost:8000/itsmstore4/checkout/order-received/".$bookorder_id."/?key=wc_order_kLlLsFJ5xEt8i";
						       // $ss	= $objWC_Gateway_2c2p->wc_2c2p_response_url($bookorder_id);

						        $merchant_id = isset($pg_2c2p_setting_values['key_id']) ? sanitize_text_field($pg_2c2p_setting_values['key_id']) : "";
						        $secret_key = isset($pg_2c2p_setting_values['key_secret']) ? sanitize_text_field($pg_2c2p_setting_values['key_secret']) : "";
						        $default_lang  = WC_2C2P_Constant::WC_2C2P_VERSION;
						        $selected_lang = sanitize_text_field($pg_2c2p_setting_values['wc_2c2p_default_lang']);
						        $default_lang  = !empty($selected_lang) ? $selected_lang : $default_lang;
						        $payment_description = $payment_description;
					       		$order_id = "U".$subscription->get_id().date("His").date("Ymd").'O'.$value_order[0];
					       		$invoice_no = $subscription->get_id();
					        	$amount = sprintf("%012d",str_replace('.','', number_format($newprice_notformat,2, '.', '') ));
					        	$customer_email = $customer_email;
						        $result_url_1   = $redirect_url;
						        $result_url_2   = "http://provisioning.netkadev.com/api/responsebackupgrades";
						        $user_defined_2 = $sitename.':'.$package.':'.$agent.':'.$package.':'.$customer_email.':'.$billing_address_state.':'.$billing_address_country;
						        $user_defined_1 = $interval;
					       		$user_defined_3 = $old_variation->id.':'.$old_variation->sku.':'.$old_variation->name.':'.$old_variation->price.$subscription->order_currency.'to'.$variation->id.':'.$variation->sku.':'.$variation->name.':'.$variation->price.$subscription->order_currency;
					       		$recurring_amo_format = number_format($variation->price,2,"","");
					       		$recurring_amount = str_pad($recurring_amo_format, 12, '0', STR_PAD_LEFT);
					       		//Create key value pair array. for payment normal
						        if($pg_2c2p_setting_values['test_mode']=="t"){
						        	$urlpay2c2p = 'https://t.2c2p.com/RedirectV3/payment';
						        }else{
						        	$urlpay2c2p = 'https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment';
						        }

						         //IPP Options
						        $recurring = "Y";                   //Recurring flag
						        $order_prefix = $setday.date("s").$invoice_no; //(new DateTime('tomorrow'))->format('dmY').(time()%1000)Recurring invoice no prefix. for demo purpose we set it to date and time.
						        $recurring_amount = $recurring_amount; //Recurring charge amount
						        $allow_accumulate = "N";            //Allow accumulate amount flag
						        $max_accumulate_amount = $recurring_amount;    //Maximum accumulate amount allowed
						        $recurring_interval = $interval;          //Recurring interval by no of day
						        $recurring_count = "99";            //Number of recurring charges 
						        $charge_next_date = $setday;  //The first day to start recurring charges.
						        $charge_on_date = "";         //To set a specific date for recurring charges to trigger instead 

						        //var_dump($urlpay2c2p,$merchant_id,$secret_key);exit;
						        $version = "8.5";//WC_2C2P_Constant::WC_2C2P_VERSION;
						        $merchant_id = $merchant_id;
						        $payment_description = $payment_description;
						        $order_id = $order_id;
						        $invoice_no = $invoice_no;
						        $currency = $currency_num;
						        $amount = sprintf("%012d",str_replace('.','', $amount ));
						        $customer_email = $customer_email;        
						        $result_url_1   = $result_url_1; // Specify by plugin
						        $result_url_2   = $result_url_2; // Specify by plugin
						        $payment_option   = "CC"; // Pass by default Payment option as A        
						        $default_lang   = $default_lang; // Set language. 
						        $user_defined_1 = $user_defined_1;
						        $user_defined_2 = $user_defined_2; 
						        $user_defined_3 = $user_defined_3;
						        $user_defined_4 = '';
						        $user_defined_5 = '';

						        $params = $version.$merchant_id.$payment_description.$order_id.$invoice_no.$currency.$amount.$customer_email.$user_defined_1.$user_defined_2.$user_defined_3.$result_url_1.$result_url_2.$recurring.$order_prefix.$recurring_amount.$allow_accumulate.$max_accumulate_amount.$recurring_interval.$recurring_count.$charge_next_date.$charge_on_date.$payment_option.$default_lang;

						        $hash_value = hash_hmac('sha256',$params, $secret_key,false);
								$button_redirect = '
							        <form id="myform" method="post" action="'.$urlpay2c2p.'">
									<input type="hidden" name="version" value="'.$version.'"/>
									<input type="hidden" name="merchant_id" value="'.$merchant_id.'"/>
									<input type="hidden" name="currency" value="'.$currency.'"/>
									<input type="hidden" name="customer_email" value="'.$customer_email.'"/>
									<input type="hidden" name="result_url_1" value="'.$result_url_1.'"/>
									<input type="hidden" name="result_url_2" value="'.$result_url_2.'"/>
									<input type="hidden" name="payment_option" value="'.$payment_option.'"/>
									<input type="hidden" name="default_lang" value="'.$default_lang.'"/>
									<input type="hidden" name="user_defined_1" value="'.$user_defined_1.'"/>
									<input type="hidden" name="user_defined_2" value="'.$user_defined_2.'"/>
									<input type="hidden" name="user_defined_3" value="'.$user_defined_3.'"/>
									<input type="hidden" name="hash_value" value="'.$hash_value.'"/>
								    <input type="hidden" name="payment_description" value="'.$payment_description.'"/>
									<input type="hidden" name="order_id" value="'.$order_id.'"/>
									<input type="hidden" name="invoice_no" value="'.$invoice_no.'"/>
									<input type="hidden" name="amount" value="'.$amount.'"/>
									<input type="hidden" name="recurring" value="'.$recurring.'"/>
									<input type="hidden" name="order_prefix" value="'.$order_prefix.'"/>
									<input type="hidden" name="recurring_amount" value="'.$recurring_amount.'"/>
									<input type="hidden" name="allow_accumulate" value="'.$allow_accumulate.'"/>
									<input type="hidden" name="max_accumulate_amount" value="'.$max_accumulate_amount.'"/>
									<input type="hidden" name="recurring_interval" value="'.$recurring_interval.'"/>
									<input type="hidden" name="recurring_count" value="'.$recurring_count.'"/>
									<input type="hidden" name="charge_next_date" value="'.$charge_next_date.'"/>
									<input type="hidden" name="charge_on_date" value="'.$charge_on_date.'"/>
									<input type="submit" name="submit" value="Redirect to Payment" />
									</form>';
						        	//check price=0 not show redirect
						        	if($newprice_notformat > 0){
						        		$button_action = "<p>Pay The Difference : ".get_woocommerce_currency_symbol($subscription->order_currency).$newprice ."</p>".$button_redirect;
						        	}else{
						        		//ราคาติดลบ ให้เป็น downgrade ไป
						        		$urldowngrade = "http://localhost:8000/itsmstore4/api/changeto.php";
							        	$subscription_id = $subscription->get_id();
							        	$detail_plan = $old_variation->id.':'.$old_variation->sku.':'.$old_variation->name.':'.$old_variation->price.$subscription->order_currency.'to'.$variation->id.':'.$variation->sku.':'.$variation->name.':'.$variation->price.$subscription->order_currency;
						        		$button_redirect = '<form id="myform" method="post" action="'.$urldowngrade.'" onsubmit="return confirm(\'Are you sure you want to Change Plan?\');">
										<input type="hidden" name="subscription_id" id="subscription_id" value="'.$subscription_id.'"/>
										<input type="hidden" name="order_id" id="order_id" value="'.$value_order[0].'"/>
										<input type="hidden" name="plan_old" id="plan_old" value="'.$old_variation->id.'"/>
										<input type="hidden" name="plan_new" id="plan_new" value="'.$variation->id.'"/>
										<input type="hidden" name="currency" id="currency" value="'.$subscription->order_currency.'"/>
										<input type="hidden" name="customer_email" id="customer_email" value="'.$customer_email.'"/>
										<input type="hidden" name="detail_plan" id="detail_plan" value="'.$detail_plan.'"/>
										<input type="hidden" name="intervals" id="intervals" value="'.$interval.'"/>
										<input type="hidden" name="setdays" id="setdays" value="'.$setday.'"/>
										<input type="submit" name="submit" value="Change to" />
										</form>';
						        		$button_action = $button_redirect;
						        	}
				        	break;
				        }//end switch

				
				echo $button_action;
				
				?>
		</div> 
			
		<?php   
			//endif;
			endforeach;  ?>
	</div>
</div>
<?php } //end if check payment_due_date is empty ?>
<?php remove_filter( 'woocommerce_product_variation_title_include_attributes', '__return_true' ); ?>