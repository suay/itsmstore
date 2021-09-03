<?php

class wc_2c2p_construct_request_helper extends WC_Payment_Gateway 
{
    private $pg_2c2p_setting_values;

    function __construct() { 
        $objWC_Gateway_2c2p = new WC_Gateway_2c2p();
        $this->pg_2c2p_setting_values = $objWC_Gateway_2c2p->wc_2c2p_get_setting();
    }

    private $wc_2c2p_form_fields = array(
        "version" => "",
        "merchant_id" => "", 
        "payment_description" => "",
        "order_id" => "", 
        "invoice_no" => "",
        "currency" => "",
        "amount" => "", 
        "customer_email" => "",
        "pay_category_id" => "",
        "promotion" => "",
        "user_defined_1" => "",
        "user_defined_2" => "",
        "user_defined_3" => "",
        "user_defined_4" => "",
        "user_defined_5" => "",
        "result_url_1" => "",
        "result_url_2" => "",
        "payment_option" => "",
        "enable_store_card" => "",
        "stored_card_unique_id" => "",        
        "request_3ds"   => "",
        "payment_expiry" => "",
        "default_lang" => "",
        "statement_descriptor" => "",
        "hash_value" => "",
        "recurring" => "", // start recurring
        "order_prefix" => "",
        "recurring_amount" => "",
        "allow_accumulate" => "",
        "max_accumulate_amount" => "",
        "recurring_interval" => "",
        "recurring_count" => "",
        "charge_next_date" => "",
        "charge_on_date" => "" );
    
    public function wc_2c2p_construct_request($isloggedin,$paymentBody)
    {        
        //Check customer is logged in or not. If customer is logged in then pass stored card.
        if($isloggedin){
            if (strcasecmp($this->pg_2c2p_setting_values['wc_2c2p_stored_card_payment'], "yes") == 0) {
                $enable_store_card = "Y";
                //When user have stored_card_unique_id.
                if (!empty($paymentBody['stored_card_unique_id'])) {
                    $this->wc_2c2p_form_fields["stored_card_unique_id"] = $paymentBody['stored_card_unique_id'];
                }
                
                $this->wc_2c2p_form_fields["enable_store_card"] = $enable_store_card;
            }
        }
        
        $this->wc_2c2p_123_payment_expiry($paymentBody);
        $this->wc_2c2p_create_common_form_field($paymentBody);
        
        //Create obj of hash helper class.
        $objwc_2c2p_hash_helper = new wc_2c2p_hash_helper();
        $hashValue = $objwc_2c2p_hash_helper->wc_2c2p_create_hashvalue($this->wc_2c2p_form_fields);
        
        $this->wc_2c2p_form_fields['hash_value'] = $hashValue;
        
        $strHtml = "";
        foreach ($this->wc_2c2p_form_fields as $key => $value) {
            if (!empty($value)) {
                $strHtml .= '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
            }        
        }
        
        $strHtml .= '<input type="hidden" name="request_3ds" value="">';
        
        return $strHtml;    
    }
    function wc_2c2p_create_common_form_field($paymentBody){
        global $wpdb;
        $trimname = $wpdb->get_col("SELECT meta_value FROM wp_postmeta where meta_key='_order_website' order by meta_id desc limit 1");
        //check sitename 
        $isusername = preg_replace('/[ ]+/', '', trim($trimname[0]));

        $currentuser = wp_get_current_user();
        //find region and country
        $findregion = $wpdb->get_results("SELECT meta_key,meta_value FROM wp_usermeta where user_id='".$currentuser->ID."' and meta_key in ('billing_state','billing_country') order by meta_key asc ");
        foreach ($findregion as $value) {
            $isfind[] = $value->meta_value; //region , state
        }

       

        foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
            $productname = preg_split('/(-|_|,| )/', $values['data']->sku);// get last for check plan
            if(is_numeric(end($productname))==true){
                $agents = preg_split('/(-|_|,| )/', $values['data']->attributes['pa_agent']);
                $agent = end($agents);
            }else{
                $agent = "5";
            }       
            $package = strtoupper($productname[1]);
            $productstatus = $values['data']->status;
            $priceproduct = $values['data']->price; //0.00 for free trial 
            //set use will pay every day,week,month,year
            $ispertype  = $values['data']->ywsbs_price_is_per;  // valuu of per 1,2 
            $typecurringpay = $values['data']->ywsbs_price_time_option;  //type day,month,year
        }
        if($priceproduct=="0.00"){
            $typepay = "DEMO";
        }else{
            $typepay = $package;
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
                
                $setday = date('dmY',strtotime(date('Y/m/d') . $valueperday));

            break;
            case "weeks":

                $valueperweek = $ispertype."week";
                if($ispertype > 1){
                    $interval = $ispertype * 7;
                }else{
                    $interval = "7";
                }
                
                $setday = date('dmY',strtotime(date('Y/m/d') . $valueperweek));

            break;
            case "months":
                $valuepermonth = $ispertype."month";
                if($ispertype > 1){
                    $interval = $ispertype * 30;
                }else{
                    $interval = "30";
                }
                
                $setday = date('dmY',strtotime(date('Y/m/d') . $valuepermonth));

            break;
            case "years":
                $valueperyear = $ispertype."year";
                $interval = "365";
                $setday = date('dmY',strtotime(date('Y/m/d') . $valueperyear));
            break;
        }

        $objWC_Gateway_2c2p = new WC_Gateway_2c2p();
        $redirect_url   = $objWC_Gateway_2c2p->wc_2c2p_response_url($paymentBody['order_id']);
        
        $merchant_id    = isset($this->pg_2c2p_setting_values['key_id']) ? sanitize_text_field($this->pg_2c2p_setting_values['key_id']) : "";
        $secret_key     = isset($this->pg_2c2p_setting_values['key_secret']) ? sanitize_text_field($this->pg_2c2p_setting_values['key_secret']) : "";

        $default_lang  = WC_2C2P_Constant::WC_2C2P_VERSION;
        $selected_lang = sanitize_text_field($this->pg_2c2p_setting_values['wc_2c2p_default_lang']);
        $default_lang  = !empty($selected_lang) ? $selected_lang : $default_lang;

        //Get is store currency code from woocommerece.
        $currency       = sanitize_text_field($this->wc_2c2p_get_store_currency_code());         
        $payment_description = $paymentBody['payment_description'];
        $order_id       = $paymentBody['order_id'];
        $invoice_no     = $paymentBody['invoice_no'];
        $amount         = str_pad($paymentBody['amount'], 12, '0', STR_PAD_LEFT);        
        $customer_email = $paymentBody['customer_email'];
        $result_url_1   = $redirect_url;
        $result_url_2   = "http://provisioning.netkadev.com/api/responseback";//"http://localhost:8000/newpayment1/public/api/responseback";//"http://provisioning.itsmnetka.com/api/responseback";//$redirect_url;
        $user_defined_2 = $isusername.':'.$package.':'.$agent.':'.$typepay.':'.$customer_email.':'.$isfind[0].':'.$isfind[1];
        // set recurring 
        $user_defined_1 = $interval;

        //set recurring payment plan 30-04-2021
        //IPP Options
        $recurring = "Y";                   //Recurring flag
        $order_prefix = $setday.$invoice_no; //(new DateTime('tomorrow'))->format('dmY').(time()%1000)Recurring invoice no prefix. for demo purpose we set it to date and time.
        $recurring_amount = $amount; //Recurring charge amount
        $allow_accumulate = "N";            //Allow accumulate amount flag
        $max_accumulate_amount = $amount;    //Maximum accumulate amount allowed
        $recurring_interval = $interval;          //Recurring interval by no of day
        $recurring_count = "99";            //Number of recurring charges 
        $charge_next_date = $setday;  //The first day to start recurring charges.
        $charge_on_date = "";         //To set a specific date for recurring charges to trigger instead of using day interval.

        //Create key value pair array. for payment normal
        // $this->wc_2c2p_form_fields["version"] = WC_2C2P_Constant::WC_2C2P_VERSION;
        // $this->wc_2c2p_form_fields["merchant_id"] = $merchant_id;
        // $this->wc_2c2p_form_fields["payment_description"] = $payment_description;
        // $this->wc_2c2p_form_fields["order_id"] = $order_id;
        // $this->wc_2c2p_form_fields["invoice_no"] = $invoice_no;
        // $this->wc_2c2p_form_fields["currency"] = $currency;
        // $this->wc_2c2p_form_fields["amount"] = $amount;
        // $this->wc_2c2p_form_fields["customer_email"] = $customer_email;        
        // $this->wc_2c2p_form_fields["result_url_1"]   = $result_url_1; // Specify by plugin
        // $this->wc_2c2p_form_fields["result_url_2"]   = $result_url_2; // Specify by plugin
        // $this->wc_2c2p_form_fields["payment_option"]   = "A"; // Pass by default Payment option as A        
        // $this->wc_2c2p_form_fields["default_lang"]   = $default_lang; // Set language. 
        // $this->wc_2c2p_form_fields["user_defined_1"] = " ";
        // $this->wc_2c2p_form_fields["user_defined_2"] = $user_defined_2;   

        //Create key value pair array. for Recurring 
        $this->wc_2c2p_form_fields["version"] = WC_2C2P_Constant::WC_2C2P_VERSION;
        $this->wc_2c2p_form_fields["merchant_id"] = $merchant_id;
        $this->wc_2c2p_form_fields["payment_description"] = $payment_description;
        $this->wc_2c2p_form_fields["order_id"] = $order_id; 
        $this->wc_2c2p_form_fields["invoice_no"] = $invoice_no;
        $this->wc_2c2p_form_fields["currency"] = $currency;
        $this->wc_2c2p_form_fields["amount"] = $amount;
        $this->wc_2c2p_form_fields["customer_email"] = $customer_email;
        $this->wc_2c2p_form_fields["result_url_1"]   = $result_url_1; // Specify by plugin
        $this->wc_2c2p_form_fields["result_url_2"]   = $result_url_2; // Specify by plugin
        $this->wc_2c2p_form_fields["payment_option"]   = "A"; // Pass by default Payment option as A        
        $this->wc_2c2p_form_fields["default_lang"]   = $default_lang; // Set language. 
        $this->wc_2c2p_form_fields["user_defined_1"] = $user_defined_1; // Set value type recurring pay
        $this->wc_2c2p_form_fields["user_defined_2"] = $user_defined_2;
        $this->wc_2c2p_form_fields['recurring'] = $recurring;
        $this->wc_2c2p_form_fields['order_prefix'] = $order_prefix;
        $this->wc_2c2p_form_fields['recurring_amount'] = $recurring_amount;
        $this->wc_2c2p_form_fields['allow_accumulate'] = $allow_accumulate;
        $this->wc_2c2p_form_fields['max_accumulate_amount'] = $max_accumulate_amount;
        $this->wc_2c2p_form_fields['recurring_interval'] = $recurring_interval;
        $this->wc_2c2p_form_fields['recurring_count'] = $recurring_count;
        $this->wc_2c2p_form_fields['charge_next_date'] = $charge_next_date;
        $this->wc_2c2p_form_fields['charge_on_date'] = $charge_on_date;
        //check convert free to pay set trick not genpassword
        if( !empty( WC()->session->get('sitename') ) ){
             $this->wc_2c2p_form_fields["user_defined_4"] = "convert"; 
        }

// var_dump($paymentBody['order_id']);exit;
        // var_dump( WC()->session->get('sitename') );
        // var_dump($this->wc_2c2p_form_fields);exit;
    }

    function wc_2c2p_123_payment_expiry($paymentBody){
        $payment_expiry = $this->pg_2c2p_setting_values['wc_2c2p_123_payment_expiry'];

        if(!isset($payment_expiry) || empty($payment_expiry) || !is_numeric($payment_expiry)) {            
            //Set default 123 payment expiry. If validation is failed from merchant configuration sections.
            $payment_expiry = 8;
        }

        if(!($payment_expiry >=8 && $payment_expiry <= 720)) {            
            //Set default 123 payment expiry. If validation is failed from merchant configuration sections.
            $payment_expiry = 8;   
        }

        $date           = date("Y-m-d H:i:s");
        $strTimezone    = date_default_timezone_get();
        $date           = new DateTime($date, new DateTimeZone($strTimezone));
        $date->modify("+" . $payment_expiry . "hours");
        $payment_expiry = $date->format("Y-m-d H:i:s");

        $this->wc_2c2p_form_fields["payment_expiry"] = $payment_expiry;
    }

    //Get the store currency code.
    function wc_2c2p_get_store_currency_code(){
        $objWC_2c2p_currency = new WC_2c2p_currency();
        $currenyCode = get_option('woocommerce_currency');

        if(!isset($currenyCode))
            return "";

        foreach ($objWC_2c2p_currency->get_currency_code() as $key => $value) {
            if($key === $currenyCode){
                return  $value['Num'];
            }
           
        }
		 return "";
    }
}

?>