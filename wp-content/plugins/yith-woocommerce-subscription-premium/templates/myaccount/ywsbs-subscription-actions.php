<?php
/**
 * Subscription actions
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscription Current Subscription.
 * @var string             $style How to show the actions
 * @var array              $pause Pause info.
 * @var array              $cancel Cancel info
 * @var array              $resume Resume info
 * @var string             $close_modal_button Label of button inside the modal.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'dropdown' === $style ) : ?>
	<div class="ywsbs-dropdown-wrapper">
		<a href="#"
			onclick="return false;"><?php esc_html_e( 'Change status >', 'yith-woocommerce-subscription' ); ?></a>
		<div class="ywsbs-dropdown">
			<?php if ( $pause ) : ?>
				<div class="ywsbs-dropdown-item" class="open-modal" data-target="pause-subscription">
					<?php echo wp_kses_post( wpautop( $pause['dropdown_text'] ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( $resume ) : ?>
				<div class="ywsbs-dropdown-item" class="open-modal" data-target="resume-subscription">
					<?php echo wp_kses_post( wpautop( $resume['dropdown_text'] ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( $cancel ) : ?>
				<div class="ywsbs-dropdown-item" class="open-modal" data-target="cancel-subscription">
					<?php echo wp_kses_post( wpautop( $cancel['dropdown_text'] ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( $repay ) : ?>
				<div class="ywsbs-dropdown-item" class="open-modal" data-target="repay-subscription">
					<?php echo wp_kses_post( wpautop( $repay['dropdown_text'] ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php else : ?>
	<div class="ywsbs-change-status-buttons-wrapper">
		<?php if ( $pause ) : ?>
			<button class="open-modal"
				data-target="pause-subscription"><?php echo esc_html( $pause['button_label'] ); ?></button>
		<?php endif; ?>
		<?php if ( $resume ) : ?>
			<button class="open-modal"
				data-target="resume-subscription"><?php echo esc_html( $resume['button_label'] ); ?></button>
		<?php endif; ?>
		<?php if ( $cancel ) : ?>
			<button class="open-modal"
				data-target="cancel-subscription"><?php echo esc_html( $cancel['button_label'] ); ?></button>
		<?php endif; ?>
		<?php if ( $repay ) : ?>
		<?php 
		// var_dump($subscription);
			global $wpdb;
			$order_id = $subscription->get_order_ids();
			$subscription_id = $subscription->get_id();
			$objWC_Gateway_2c2p = new WC_Gateway_2c2p();
			$pg_2c2p_setting_values = $objWC_Gateway_2c2p->wc_2c2p_get_setting();
			$billing = $subscription->get_address_fields( 'billing', true );
			$redirect_url = $objWC_Gateway_2c2p->wc_2c2p_response_url('RE'.$subscription_id);
			$merchant_id = isset($pg_2c2p_setting_values['key_id']) ? sanitize_text_field($pg_2c2p_setting_values['key_id']) : "";
			$secret_key = isset($pg_2c2p_setting_values['key_secret']) ? sanitize_text_field($pg_2c2p_setting_values['key_secret']) : "";
			//Create key value pair array. for payment normal
	        if($pg_2c2p_setting_values['test_mode']=="t"){
	        	$urlpay2c2p = 'https://t.2c2p.com/RedirectV3/payment';
	        }else{
	        	$urlpay2c2p = 'https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment';
	        }
	        $notes = $wpdb->get_results( "SELECT meta_value FROM {$wpdb->prefix}postmeta where post_id = '".$order_id[0]."' and meta_key ='_order_website'");
			foreach ($notes as $notest) {
				$notest = $notest;
			}
			$sitename = $notes[0]->meta_value;
			$product = wc_get_product( $subscription->get_variation_id() );
			$productsku = $product->sku;
			$newproductname = preg_split('/(-|_|,| )/', $productsku);// get last for check plan
            if(is_numeric(end($newproductname))==true){
                $agent = end($newproductname);
            }      
            $package = strtoupper($newproductname[1]);
            $agent = $newproductname[2];
            $address_state = $billing['state'];
            $address_contry = $billing['country'];
	        //$version = "8.5";
			$default_version  = "8.5";//WC_2C2P_Constant::WC_2C2P_VERSION;
			$selected_lang = sanitize_text_field($pg_2c2p_setting_values['wc_2c2p_default_lang']);
			$default_lang = !empty($selected_lang) ? $selected_lang : $default_version;
			$payment_description = "Repayment again";
			$order_ids = "RE".$subscription_id.date("His").date("Ymd").'O'.$order_id[0];
			$totalprice = $subscription->line_total + $subscription->line_tax;
			$amount = sprintf("%012d",str_replace('.','', number_format($totalprice,2, '.', '') )); 
			$currencyCode = $subscription->order_currency;
			$customer_email = $billing['email'];
			$result_url_1 = $redirect_url;
			$result_url_2   = "http://provisioning.netkadev.com/api/responsebackrepay";
			$payment_option = "CC";
			$ispertype = $subscription->get_price_is_per(); //price per 1,2,3
			$typecurringpay = $subscription->get_price_time_option(); //price per days,weeks,months,years
			$payment_due_date = $subscription->get_next_payment_due_date();
			$check_showbutton = strtotime("+30 day",$subscription->get_next_payment_due_date() );//show button payment_due_date + 30 days
			$objWC_2c2p_currency =  new WC_2c2p_currency();
			//Get the store currency code.
	        foreach ($objWC_2c2p_currency->get_currency_code() as $key => $value) {
	            if($key === $currencyCode){
	                $currency =  $value['Num'];
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
	                $setday = date('dmY',strtotime($valueperday,$subscription->get_next_payment_due_date()) );

	            break;
	            case "weeks":
	                $valueperweek = $ispertype."week";
	                if($ispertype > 1){
	                    $interval = $ispertype * 7;
	                }else{
	                    $interval = "7";
	                }
	                $setday = date('dmY', strtotime($valueperweek,$subscription->get_next_payment_due_date()) );

	            break;
	            case "months":
	                $valuepermonth = $ispertype."month";
	                if($ispertype > 1){
	                    $interval = $ispertype * 30;
	                }else{
	                    $interval = "30";
	                }
	                $setday = date('dmY',strtotime($valuepermonth,$subscription->get_next_payment_due_date()) );

	            break;
	            case "years":
	                $valueperyear = $ispertype."year";
	                $interval = "365";
	                $setday = date('dmY',strtotime($valueperyear,$subscription->get_next_payment_due_date()) );
	            break;
	        }
	        $date_ex_showbutton = date("Y-m-d 23:00:00",$check_showbutton);
	        $user_defined_1 = $interval;
	        $user_defined_2 = $sitename.':'.$package.':'.$agent.':'.$package.':'.$customer_email.':'.$address_state.':'.$address_contry;
			$user_defined_3 = "";
			$user_defined_4 = "";
			$user_defined_5 = "";
	        // var_dump(date("Y-m-d",strtotime($valueperyear,$subscription->get_next_payment_due_date()) ) ,$setday );

			 //IPP Options
	        $recurring = "Y";                   //Recurring flag
	        $order_prefix = $setday.date("s").$subscription_id; //(new DateTime('tomorrow'))->format('dmY').(time()%1000)Recurring invoice no prefix. for demo purpose we set it to date and time.
	        $recurring_amount = $amount; //Recurring charge amount
	        $allow_accumulate = "N";            //Allow accumulate amount flag
	        $max_accumulate_amount = $amount;    //Maximum accumulate amount allowed
	        $recurring_interval = $interval;          //Recurring interval by no of day
	        $recurring_count = "60";            //Number of recurring charges 
	        $charge_next_date = $setday;  //The first day to start recurring charges.
	        $charge_on_date = "";         //To set a specific date for recurring charges to trigger instead

	        $params = $default_version.$merchant_id.$payment_description.$order_ids.$subscription_id.$currency.$amount.$customer_email.$user_defined_1.$user_defined_2.$user_defined_3.$result_url_1.$result_url_2.$recurring.$order_prefix.$recurring_amount.$allow_accumulate.$max_accumulate_amount.$recurring_interval.$recurring_count.$charge_next_date.$charge_on_date.$payment_option.$default_lang;

// var_dump($secret_key);exit;
			$hash_value = hash_hmac('sha256',$params, $secret_key,false);


			if( date("Y-m-d 23:00:00") < $date_ex_showbutton ) :
				$button_repay = '<form id="myform" method="post" action="'.$urlpay2c2p.'">
			<input type="text" name="version" value="'.$default_version.'"/>
			<input type="text" name="merchant_id" value="'.$merchant_id.'"/>
			<input type="text" name="currency" value="'.$currency.'"/>
			<input type="text" name="customer_email" value="'.$customer_email.'"/>
			<input type="text" name="result_url_1" value="'.$result_url_1.'"/>
			<input type="text" name="result_url_2" value="'.$result_url_2.'"/>
			<input type="text" name="payment_option" value="'.$payment_option.'"/>
			<input type="text" name="default_lang" value="'.$default_lang.'"/>
			<input type="text" name="user_defined_1" value="'.$user_defined_1.'"/>
			<input type="text" name="user_defined_2" value="'.$user_defined_2.'"/>
			<input type="text" name="user_defined_3" value="'.$user_defined_3.'"/>
			<input type="text" name="hash_value" value="'.$hash_value.'"/>
		    <input type="text" name="payment_description" value="'.$payment_description.'"/>
			<input type="text" name="order_id" value="'.$order_ids.'"/>
			<input type="text" name="invoice_no" value="'.$subscription_id.'"/>
			<input type="text" name="amount" value="'.$amount.'"/>
			<input type="text" name="recurring" value="'.$recurring.'"/>
			<input type="text" name="order_prefix" value="'.$order_prefix.'"/>
			<input type="text" name="recurring_amount" value="'.$recurring_amount.'"/>
			<input type="text" name="allow_accumulate" value="'.$allow_accumulate.'"/>
			<input type="text" name="max_accumulate_amount" value="'.$max_accumulate_amount.'"/>
			<input type="text" name="recurring_interval" value="'.$recurring_interval.'"/>
			<input type="text" name="recurring_count" value="'.$recurring_count.'"/>
			<input type="text" name="charge_next_date" value="'.$charge_next_date.'"/>
			<input type="text" name="charge_on_date" value="'.$charge_on_date.'"/>
			<input type="submit" name="submit" value="Re-Payment" />
			</form>';


			echo $button_repay;
		?>
			
		<?php endif; ?>
			<!-- <button class="open-modal"
				data-target="repay-subscription"><?php echo esc_html( $repay['button_label'] ); ?></button> -->
		<?php endif; ?>

	</div>
<?php endif; ?>
