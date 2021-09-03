<?php
if(!defined('ABSPATH')){ exit; }

if(!class_exists('THWMSCF_Settings')):

class THWMSCF_Settings {
	const WMSC_SETTINGS = 'THWMSC_SETTINGS';
	protected static $_instance = null;	
	private $tabs = '';
	private $settings = '';
	
	private $cell_props = array();
	private $cell_props_L = array();
	private $cell_props_R = array();
	private $cell_props_CB = array(); 

	public function __construct(){
		$this->tabs = array( 'msc_settings' => 'Multistep Checkout');
		
		$this->cell_props = array( 
			'label_cell_props' => 'style="width: 20%;" class="titledesc" scope="row"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '250px', 'label_cell_th' => true 
		);
		$this->cell_props_L = array( 
			'label_cell_props' => 'style="width: 20%;" class="titledesc" scope="row"', 
			'input_cell_props' => 'style="width: 25%;" class="forminp"', 
			'input_width' => '250px', 'label_cell_th' => true 
		);
		$this->cell_props_R = array( 
			'label_cell_props' => 'style="width: 15%;" class="titledesc" scope="row"', 
			'input_cell_props' => 'style="width: 30%;" class="forminp" ', 
			'input_width' => '250px', 'label_cell_th' => true 
		);
		//$this->cell_props_R = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$this->cell_props_CB = array( 'cell_props' => 'colspan="3"' );

		$this->settings = $this->get_settings();

		add_action('admin_menu', array($this, 'admin_menu'));
		add_filter('woocommerce_screen_ids', array($this, 'add_screen_id'));
		
		add_filter('plugin_action_links_'.THWMSCF_BASE_NAME, array($this, 'add_settings_link'));
		
		$this->init();
	}

	public static function instance(){
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * menu function.
	 */
	public function admin_menu() {
		$this->screen_id = add_submenu_page('woocommerce', __('Woo Multistep Checkout', 'woo-multistep-checkout'), __('Multistep Checkout', 'woo-multistep-checkout'), 
		'manage_woocommerce', 'woo_multistep_checkout', array($this, 'multistep_checkout'));

		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
	}	
	
	public function add_settings_link($links) {
		$settings_link = '<a href="'. esc_url(admin_url('admin.php?page=woo_multistep_checkout')) .'">'. __('Settings') .'</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	function enqueue_admin_scripts($hook) {
		if(strpos($hook, 'page_woo_multistep_checkout') === false) {
			return;
		}

		wp_enqueue_style('woocommerce_admin_styles');		
		wp_enqueue_style('thwmscf-admin-style', plugins_url('/assets/css/thwmscf-admin.css', dirname(__FILE__)), THWMSCF_VERSION);  
		wp_enqueue_script('thwmscf-admin-js', THWMSCF_ASSETS_URL.'js/thwmscf-admin.js',array('jquery','wp-color-picker'), THWMSCF_VERSION, true);
	}

	/**
	 * add_screen_id function.
	 */
	function add_screen_id($ids){
		$ids[] = 'woocommerce_multistep_checkout';
		$ids[] = strtolower(__('WooCommerce', 'woo-multistep-checkout')) .'_multistep_checkout';

		return $ids;
	}

	function multistep_checkout() { 		
		$this->wmsc_design();
	}

	public function get_settings(){		
		$settings_default = array(
			'enable_wmsc' => 'yes',
			'enable_login_step' => 'yes',
			'title_login' => 'Login',
			'title_billing' => 'Billing details',
			'title_shipping' => 'Shipping details',
			'title_order_review' => 'Your order',
			'step_bg_color'   => '#B2B2B0',
			'step_text_color' => '#8B8B8B',
			'step_bg_color_active'   => '#018DC2',
			'step_text_color_active' => '#FFFFFF',
			'tab_panel_bg_color' => '#FBFBFB',
		);
		$saved_settings = $this->get_wmsc_settings();
		
		$settings = !empty($saved_settings) ? $saved_settings : $settings_default ;
		return apply_filters('thwmcf_plugin_settings', $settings);

	}

	public function get_tabs(){
		return $this->tabs; 
	}

	function get_current_tab(){
		return isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'msc_settings';
	}

	public function get_settings_fields(){
		$tab_postion = array(
			'align-left' => 'Left',
			'align-center' => 'Center'
		);

		$layout_options = array(			
			'thwmscf_horizontal_box' => array('name' => __('Horizontal Box Layout', 'woo-multistep-checkout'), 'layout_image' => 'horizontal_box.png'),
			'thwmscf_vertical_box' 	 => array('name' => __('Vertical Box Layout', 'woo-multistep-checkout'), 'layout_image' => 'vertical_box.png'),
			'thwmscf_time_line_step' 	 => array('name' => __('Time Line Layout', 'woo-multistep-checkout'), 'layout_image' => 'timeline.png'),
		);

		$layout_field = array(
			'enable_wmsc' => array(
				'name'=>'enable_wmsc', 'label'=>__('Enable Multi-step', 'woo-multistep-checkout'), 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1	
			),	
			'title_display_texts' => array('title'=>__('Step Display Texts', 'woo-multistep-checkout'), 'type'=>'separator', 'colspan'=>'6'),
			'enable_login_step' => array(
				'name'=>'enable_login_step', 'label'=>__('Display login step', 'woo-multistep-checkout'), 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1, 'hint_text'=>'The login step will depend on the woocommerce Accounts & Privacy tab settings.', 'onchange'=>'thwmscfDisplayLogin(this)',
			),
			'enable_step_validation' => array(
				'name'=>'enable_step_validation', 'label'=>__('Enable step validation', 'woo-multistep-checkout'), 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1
			),
			'make_billing_shipping_together' => array(
				'name'=>'make_billing_shipping_together', 'label'=>__('Combine Billing step and Shipping step', 'woo-multistep-checkout'), 'type'=>'checkbox', 'value'=>'yes', 'checked'=>0, 'onchange'=>'thwmscfShippingTitle(this)'
			),
			'title_login' => array(
				'name'=>'title_login', 'label'=>__('Login', 'woo-multistep-checkout'), 'type'=>'text', 'value'=>'Login', 'post_sanitize'=>1,
			),
			'title_billing' => array(
				'name'=>'title_billing', 'label'=>__('Billing details', 'woo-multistep-checkout'), 'type'=>'text', 'value'=>'Billing details', 'post_sanitize'=>1,
			),
			'title_shipping' => array(
				'name'=>'title_shipping', 'label'=>__('Shipping details', 'woo-multistep-checkout'), 'type'=>'text', 'value'=>'Shipping details', 'post_sanitize'=>1,
			),
			'title_order_review' => array(
				'name'=>'title_order_review', 'label'=>__('Your order', 'woo-multistep-checkout'), 'type'=>'text', 'value'=>'Your order', 'post_sanitize'=>1,
			),
			'title_display_styles' => array('title'=>__('Display Styles', 'woo-multistep-checkout'), 'type'=>'separator', 'colspan'=>'6'),
			'tab_align' => array(  
				'name'=>'tab_align', 'label'=>__('Tab Position', 'woo-multistep-checkout'), 'type'=>'select', 'value'=>'center', 'hint_text'=>'For the vertical layout, this will treated as text alignment', 'options'=> $tab_postion										
			),
			'tab_panel_bg_color' => array( 
				'name'=>'tab_panel_bg_color', 'label'=>__('Content background color', 'woo-multistep-checkout'), 'type'=>'colorpicker', 'value'=>'#FBFBFB'
			),
			'step_bg_color' => array( 
				'name'=>'step_bg_color', 'label'=>__('Step background color', 'woo-multistep-checkout'), 'type'=>'colorpicker', 'value'=>'#B2B2B0'
			),  
			'step_text_color' => array(
				'name'=>'step_text_color', 'label'=>__('Step text color', 'woo-multistep-checkout'), 'type'=>'colorpicker', 'value'=>'#8B8B8B'
			),
			'step_bg_color_active' => array(       
				'name'=>'step_bg_color_active', 'label'=>__('Step background color - Active', 'woo-multistep-checkout'), 'type'=>'colorpicker', 'value'=>'#018DC2' 
			),
			'step_text_color_active' => array(    
				'name'=>'step_text_color_active', 'label'=>__('Step text color - Active', 'woo-multistep-checkout'), 'type'=>'colorpicker', 'value'=>'#FFFFFF'
			),

			'thwmscf_layout' => array( 
				'name'=>'thwmscf_layout', 'label'=>__('Multistep Layout', 'woo-multistep-checkout'), 'type'=>'radio', 'value'=>'thwmscf_horizontal_box', 'options'=> $layout_options, 'onchange'=>'thwmscLayoutChange(this)',
			),
			'next_previuos_button' => array('title'=>__('Button Settings', 'woo-multistep-checkout'), 'type'=>'separator', 'colspan'=>'6'),
			'button_prev_text' => array(
				'name'=>'button_prev_text', 'label'=>__('Button Previous Text', 'woo-multistep-checkout'), 'type'=>'text', 'value'=>'Previous', 'placeholder'=>'',
			),
			'button_next_text' => array(
				'name'=>'button_next_text', 'label'=>__('Button Next Text', 'woo-multistep-checkout'), 'type'=>'text', 'value'=>'Next', 'placeholder'=>'',
			),
			'back_to_cart_button' => array(
				'name'=>'back_to_cart_button', 'label'=>__('Enable Back to cart button', 'woo-multistep-checkout'), 'type'=>'checkbox', 'value'=>'yes', 'checked'=>0, 'onchange'=>'thwmscfBackToCart(this)',
			),
			'back_to_cart_button_text' => array(
				'name'=>'back_to_cart_button_text', 'label'=>__('Back to cart button text', 'woo-multistep-checkout'), 'type'=>'text', 'value'=>'Back to cart', 'post_sanitize'=>1,
			),
		);

		return $layout_field;  
	}

	public function get_wmsc_settings(){
		$settings = get_option(self::WMSC_SETTINGS);
		return empty($settings) ? false : $settings;
	}
	
	public function update_settings($settings){
		$result = update_option(self::WMSC_SETTINGS, $settings);
		return $result;
	}

	public function reset_settings(){
		check_admin_referer( 'manage_msc_settings', 'manage_msc_nonce' );

		if(!current_user_can('manage_woocommerce')){
			wp_die();
		}

		delete_option(self::WMSC_SETTINGS);

		return '<div class="updated"><p>'. __('Settings successfully reset', 'woo-multistep-checkout') .'</p></div>';
	}

	public function render_tabs_and_details(){
		$tabs = $this->get_tabs();
		$tab  = $this->get_current_tab();
		
		echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
		foreach( $tabs as $key => $value ) {
			$active = ( $key == $tab ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab '.$active.'" href="'. esc_url(admin_url('admin.php?page=woo_multistep_checkout&tab='.$key)) .'">'.$value.'</a>';
		}
		echo '</h2>';
		
		$this->output_premium_version_notice();		
	}

	public function output_premium_version_notice(){
		?>
        <div id="message" class="wc-connect updated thpladmin-notice">
            <div class="squeezer">
            	<table>
                	<tr>
                    	<td width="70%">
                        	<p><strong><i>WooCommerce Multi-Step Checkout</i></strong> premium version provides more features to customise checkout page step layout & design.</p>
                            <ul>
                            	<li>More layout options.</li>
                            	<li>More styling options.</li>
                            	<li>Option to enable validations at each step.</li>
                            	<li>Option to add custom step and display custom sections & fields created using our WooCommerce Checkout Field Editor plugin.</li>
                            	<li>Supports customization made with other checkout field editors and deeply integrated with our highly rated WooCommerce Checkout Field Editor plugin.</li>
                            </ul>
                        </td>
                        <td>
                        	<a target="_blank" href="https://www.themehigh.com/product/woocommerce-multi-step-checkout/" class="">
                            	<img src="<?php echo esc_url(plugins_url( '../assets/css/upgrade-btn.png', __FILE__ )); ?>" />
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
	}

	public function wmsc_design(){
		echo '<div class="wrap woocommerce"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>';

		$this->render_tabs_and_details();
		$tab  = $this->get_current_tab();

		if($tab == 'msc_settings'){
			$this->general_settings();
		}
		
		echo '</div>';
	}

	function general_settings(){ 
		if(isset($_POST['save_settings']))
			echo $this->save_settings();

		if(isset($_POST['reset_settings']))
			echo $this->reset_settings();

		$fields = $this->get_settings_fields();
		$settings = $this->get_settings();
		
		foreach( $fields as $name => &$field ) { 
			if($field['type'] != 'separator'){
				if(is_array($settings) && isset($settings[$name])){
					if($field['type'] === 'checkbox'){
						if(isset($field['value']) && $field['value'] === $settings[$name]){
							$field['checked'] = 1;
						}else{
							$field['checked'] = 0;
						}
					}else{
						$field['value'] = $settings[$name];
					}
				}
			}
		}

		$back_to_cart_button = isset($settings['back_to_cart_button']) && $settings['back_to_cart_button'] ? wptexturize($settings['back_to_cart_button']) : '';
		$enable_login_step = isset($settings['enable_login_step']) && $settings['enable_login_step'] ? wptexturize($settings['enable_login_step']) : '';
		$billing_shipping_together = isset($settings['make_billing_shipping_together']) && $settings['make_billing_shipping_together'] ? wptexturize($settings['make_billing_shipping_together']) : '';
		$layout = isset($settings['thwmscf_layout']) && $settings['thwmscf_layout'] ? $settings['thwmscf_layout'] : '';

		$cart_text_display = $back_to_cart_button !== 'yes' ? 'display:none' : '';
		$display_login_step = $enable_login_step !== 'yes' ? 'display:none' : '';
		$step_style = $billing_shipping_together == 'yes' ? 'display:none' : '';
		$tab_style = $layout == 'thwmscf_time_line_step' ? 'display:none' : '';
		?>		
		<div style="padding-left: 30px;">               
		    <form id="wmsc_setting_form" method="post" action="">
		    	<?php wp_nonce_field( 'manage_msc_settings', 'manage_msc_nonce' ); ?>
				<table class="form-table thpladmin-form-table">
                    <tbody>
						<tr>
							<?php          
							$this->render_form_field_element($fields['enable_wmsc'], $this->cell_props_L);
							$this->render_form_field_blank();
							// $this->render_form_field_element($fields['enable_step_validation'], $this->cell_props_L);
							?>
						</tr> 
						<tr>
							<?php          
							$this->render_form_field_element($fields['enable_step_validation'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						<tr>
							<?php          
							$this->render_form_field_element($fields['make_billing_shipping_together'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						<tr>
							<?php          
							$this->render_form_field_element($fields['thwmscf_layout'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						<?php $this->render_form_section_separator($fields['title_display_texts']); ?>
						<tr>
							<?php          
							$this->render_form_field_element($fields['enable_login_step'], $this->cell_props_R);
							$this->render_form_field_blank();
							?>
						</tr>
						<tr class="display-login-step" style="<?php echo $display_login_step; ?>">
							<?php          
							$this->render_form_field_element($fields['title_login'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						<tr>
							<?php          
							$this->render_form_field_element($fields['title_billing'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						<tr class="display-shipping-title" style="<?php echo $step_style; ?>;">
							<?php          
							$this->render_form_field_element($fields['title_shipping'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						<tr>
							<?php          
							$this->render_form_field_element($fields['title_order_review'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						
						<?php $this->render_form_section_separator($fields['title_display_styles']); ?>
						<tr class="display-tab-position" style="<?php echo $tab_style; ?>;">
							<?php
							$cell_props = $this->cell_props_L;
							// $cell_props['input_width'] = '182px';
							$this->render_form_field_element($fields['tab_align'], $cell_props);
							$this->render_form_field_blank();
							?>
						</tr>
						<tr>
							<?php          
							$this->render_form_field_element($fields['step_bg_color'], $this->cell_props_L);
							$this->render_form_field_element($fields['step_text_color'], $this->cell_props_R);
							?>
						</tr>
						<tr>
							<?php          
							$this->render_form_field_element($fields['step_bg_color_active'], $this->cell_props_L);
							$this->render_form_field_element($fields['step_text_color_active'], $this->cell_props_R);
							?>
						</tr>
						<tr>
							<?php          
							$this->render_form_field_element($fields['tab_panel_bg_color'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						<?php $this->render_form_section_separator($fields['next_previuos_button']); ?>
						<tr>
							<?php          
							$this->render_form_field_element($fields['button_prev_text'], $this->cell_props_L);
							$this->render_form_field_element($fields['button_next_text'], $this->cell_props_R);
							?>
						</tr>
						<tr>
							<?php          
							$this->render_form_field_element($fields['back_to_cart_button'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
						<tr class="back-to-cart-show" style="<?php echo $cart_text_display; ?>">
							<?php          
							$this->render_form_field_element($fields['back_to_cart_button_text'], $this->cell_props_L);
							$this->render_form_field_blank();
							?>
						</tr>
                    </tbody>
                </table>
				                
                <p class="submit">
					<input type="submit" name="save_settings" class="button-primary" value="Save changes">
					<input type="submit" name="reset_settings" class="button-secondary" value="Reset to default"
					onclick="return confirm('Are you sure you want to reset to default settings? all your changes will be deleted.');">
            	</p>
            </form>
    	</div>

	<?php }
	
	public function save_settings(){
		check_admin_referer( 'manage_msc_settings', 'manage_msc_nonce' );

		if(!current_user_can('manage_woocommerce')){
			wp_die();
		}

		$settings = array();
		$settings_fields = $this->get_settings_fields();
		
		foreach( $settings_fields as $name => $field ) {
			$type = $field['type'];
			if($type != 'separator'){
				$value = '';
				
				if($field['type'] === 'checkbox'){
					$value = !empty( $_POST['i_'.$name] ) ? 'yes' : '';

				}else if($field['type'] === 'multiselect_grouped'){
					$value = !empty( $_POST['i_'.$name] ) ? wc_clean(wp_unslash($_POST['i_'.$name])) : '';
					$value = is_array($value) ? implode(',', $value) : $value;

				}else if($field['type'] === 'textarea'){
					$value = !empty( $_POST['i_'.$name] ) ? sanitize_textarea_field(wp_unslash($_POST['i_'.$name])) : '';

				}else{
					if(isset($field['post_sanitize']) && $field['post_sanitize']){
						$value = !empty( $_POST['i_'.$name] ) ? wp_unslash(wp_filter_post_kses($_POST['i_'.$name])) : '';
					}else{
						$value = !empty( $_POST['i_'.$name] ) ? wc_clean(wp_unslash($_POST['i_'.$name])) : '';
					}
				}
				
				$settings[$name] = $value;
			}
		}
				
		$result = $this->update_settings($settings);
		if ($result == true) {
			echo '<div class="updated"><p>'. __('Your changes were saved.', 'woo-multistep-checkout') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).', 'woo-multistep-checkout') .'</p></div>';
		}
	}

	public function render_form_section_separator($props, $atts=array()){
		?>
		<tr valign="top"><td colspan="<?php echo $props['colspan']; ?>" style="height:10px;"></td></tr>
		<tr valign="top"><td colspan="<?php echo $props['colspan']; ?>" class="thpladmin-form-section-title" ><?php echo $props['title']; ?></td></tr>
		<tr valign="top"><td colspan="<?php echo $props['colspan']; ?>" style="height:0px;"></td></tr>
		<?php
	}

	public function render_form_field_element($field, $atts=array(), $render_cell=true){
		if($field && is_array($field)){
			$ftype = isset($field['type']) ? $field['type'] : 'text';
			
			if($ftype == 'checkbox'){
				$atts['input_cell_props'] = ' style="width: 25%;" class="forminp thwmscf_checkbox"';
				$this->render_form_field_element_checkbox($field, $atts, $render_cell);
				return true;
			}
		
			$args = shortcode_atts( array(   
				'label_cell_props' => '',
				'input_cell_props' => '',
				'label_cell_th' => false,
				'input_width' => '',
				'rows' => '5',
				'cols' => '100',
				'input_name_prefix' => 'i_'
			), $atts );
			
			$fname  = $args['input_name_prefix'].$field['name'];						
			$flabel = __($field['label'], 'woo-multistep-checkout');
			$fvalue = isset($field['value']) ? $field['value'] : '';
			
			if($ftype == 'multiselect' && is_array($fvalue)){  
				$fvalue = !empty($fvalue) ? implode(',', $fvalue) : $fvalue;
			}
			/*if($ftype == 'multiselect' || $ftype == 'multiselect_grouped'){
				$fvalue = !empty($fvalue) ? explode(',', $fvalue) : $fvalue;
			}*/
						
			$input_width  = $args['input_width'] ? 'width:'.$args['input_width'].';' : '';
			$field_props  = 'name="'. $fname .'" value="'. esc_attr($fvalue) .'" style="'. $input_width .'"';
			$field_props .= ( isset($field['placeholder']) && !empty($field['placeholder']) ) ? ' placeholder="'.$field['placeholder'].'"' : '';

			$tooltip   = isset($field['hint_text']) && !empty($field['hint_text']) ? sprintf(__('%s', 'woo-multistep-checkout'), $field['hint_text']) : '';
			
			$required_html = ( isset($field['required']) && $field['required'] ) ? '<abbr class="required" title="required">*</abbr>' : '';
			$field_html = '';
			
			if(isset($field['onchange']) && !empty($field['onchange'])){
				$field_props .= ' onchange="'.$field['onchange'].'"';
			}
			
			if($ftype == 'text'){
				$field_html = '<input type="text" '. $field_props .' />';
				
			}else if($ftype == 'number'){
				$field_html = '<input type="number" class="thwmsc_number" '. $field_props .' />';
				
			}else if($ftype == 'textarea'){
				$field_props  = 'name="'. $fname .'" style=""';
				$field_props .= ( isset($field['placeholder']) && !empty($field['placeholder']) ) ? ' placeholder="'.$field['placeholder'].'"' : '';
				$field_html = '<textarea '. $field_props .' rows="'.$args['rows'].'" cols="'.$args['cols'].'" >'. esc_textarea($fvalue) .'</textarea>';
				
			}else if($ftype == 'select'){
				$field_props .= 'class="thwmscf_select"';
				$field_html = '<select '. $field_props .' >';

				foreach($field['options'] as $value => $label){
					$selected = $value == $fvalue ? 'selected' : '';
					$field_html .= '<option value="'. trim($value) .'" '.$selected.'>'. __($label, 'woo-multistep-checkout') .'</option>';
				}

				$field_html .= '</select>';
				
			}else if($ftype == 'colorpicker'){
				$field_html = $this->render_form_field_element_colorpicker($field, $args);
			}else if($ftype == 'radio'){
				$args['input_cell_props'] = 'style="width: 27%;" class="forminp thwmscf_layout_wrap"';
				$field_html = $this->render_form_field_element_radio($field, $atts);
			}
			
			$label_cell_props = !empty($args['label_cell_props']) ? ' '.$args['label_cell_props'] : '';
			$input_cell_props = !empty($args['input_cell_props']) ? ' '.$args['input_cell_props'] : '';

			?>
            
			<td <?php echo $label_cell_props ?> > <?php 
				echo $flabel; echo $required_html; 
				
				if(isset($field['sub_label']) && !empty($field['sub_label'])){
					?>
                    <br /><span class="thpladmin-subtitle"><?php $this->_ewcfe($field['sub_label']); ?></span>
					<?php
				}
				?>
            </td>

            <?php echo $this->render_form_fragment_tooltip($tooltip, true); ?>

            <td <?php echo $input_cell_props ?> >
            	<?php echo $field_html; ?>
            </td>
            <?php
		}
	}

	public function render_form_fragment_tooltip($tooltip = false, $return = false){
		$tooltip_html = '';
		
		if($tooltip){
			$tooltip_html .= '<td style="width: 26px; padding:0px;">';
			$tooltip_html .= '<a href="javascript:void(0)" title="'.$tooltip.'" class="thpladmin_tooltip"><img src="'.THWMSCF_ASSETS_URL.'/images/help.png" title=""/></a>';
			$tooltip_html .= '</td>';
		}else{
			$tooltip_html .= '<td style="width: 26px; padding:0px;"></td>';
		}
		
		if($return){
			return $tooltip_html;
		}else{
			echo $tooltip_html;
		}
	}

	public function render_form_field_element_checkbox($field, $atts=array(), $render_cell=false){
		$args = shortcode_atts( array( 'cell_props'  => '', 'input_props' => '', 'label_props' => '', 'name_prefix' => 'i_', 'id_prefix' => 'a_f', 'input_cell_props' => ''), $atts );
		
		$fid    = $args['id_prefix'].$field['name'];
		$fname  = $args['name_prefix'].$field['name'];
		$fvalue = isset($field['value']) ? $field['value'] : '';
		$flabel = __($field['label'], 'woo-multistep-checkout');
		
		$field_props  = 'id="'. $fid .'" name="'. $fname .'"';
		$field_props .= !empty($fvalue) ? ' value="'. esc_attr($fvalue) .'"' : '';
		$field_props .= $field['checked'] ? ' checked' : '';
		$field_props .= $args['input_props'];
		$field_props .= isset($field['onchange']) && !empty($field['onchange']) ? ' onchange="'.$field['onchange'].'"' : '';

		$input_cell_props = isset($args['input_cell_props']) ? $args['input_cell_props'] : '';
		$field_html = '';
		$tooltip = isset($field['hint_text']) && !empty($field['hint_text']) ? sprintf(__('%s', 'woo-multistep-checkout'), $field['hint_text']) : '';
		
		if($render_cell === 'inline'){
			$field_html = '<td colspan="3"><input type="checkbox" '. $field_props .' /><label for="'. $fid .'" '. $args['label_props'] .' > '. $flabel .'</label></td>';
		}else{
			$field_html = '<td><label for="'. $fid .'" '. $args['label_props'] .' > '. $flabel .'</label></td>';
			// $field_html .= '<td style="width: 26px; padding:0px;"></td>';
			$field_html .= $this->render_form_fragment_tooltip($tooltip, true);
			$field_html .= '<td '. $input_cell_props .'><input type="checkbox" '. $field_props .' /><label for="' . $fid . '"class="thwmscf-checkbox-span"></label></td>';
		}
		echo $field_html;
	}

	private function render_form_field_element_radio($field, $atts = array()){
		$field_html = '';
		$args = shortcode_atts( array(
			'label_props' => '',
			'cell_props'  => 3,
			'render_input_cell' => false,
			'render_label_cell' => false,
			'input_cell_props'
		), $atts );

		// $cell_props_rd = $this->cell_props_CB;
		// $cell_props_rd['input_cell_props'] = 'class="forminp layout_wrap" colspan="4"';

		$atts = array(
			'input_width' => 'auto',
		);

		if($field && is_array($field)){
			
			$fvalue = isset($field['value']) ? $field['value'] : '';
			// $field_props = $this->prepare_form_field_props($field, $atts);			

			foreach($field['options'] as $value => $label){
				$checked ='';
				$img_layout = '';

				//$flabel = isset($label) && !empty($label) ? THWMSC_i18n::t($label) : '';
				$flabel = isset($label['name']) && !empty($label['name']) ? sprintf(__('%s', 'woocommerce-multistep-checkout'), $label['name']) : '';
				$onchange = ( isset($field['onchange']) && !empty($field['onchange']) ) ? ' onchange="'.$field['onchange'].'"' : '';
				$img_layout = isset($label['layout_image']) && !empty($label['layout_image']) ? $label['layout_image'] : '';

				$checked = $value === $fvalue ? 'checked' : '';				
				$field_html .='<label for="'. $value .'" '. $args['label_props'] .' > ';				

				$field_html .= '<input type="radio" name="i_' . $field['name'] . '" id="'. $value . '" value="'. trim($value) .'" ' . $checked . $onchange . '>';
				//$field_html .= '<span class ="layout-icon ' . $value . '"></span>';
				$field_html .= '<img src= "'. THWMSCF_ASSETS_URL . 'images/' . $img_layout.'">';
				$field_html .= $flabel.'</label>';
			}
		}
		return $field_html;
	}

	private function render_form_field_element_colorpicker($field, $atts = array()){
		$field_html = '';
		if($field && is_array($field)){
			$args = shortcode_atts( array(
				'input_width' => '',
				'input_name_prefix' => 'i_'
			), $atts );
			
			$fname  = $args['input_name_prefix'].$field['name'];
			$fvalue = isset($field['value']) ? $field['value'] : '';
			
			$input_width  = $args['input_width'] ? 'width:'.$args['input_width'].';' : '';
			$field_props  = 'name="'. $fname .'" value="'. esc_attr($fvalue) .'" style="'. $input_width .'"';
			$field_props .= ( isset($field['placeholder']) && !empty($field['placeholder']) ) ? ' placeholder="'.$field['placeholder'].'"' : '';
			
			$field_html  = '<span class="thpladmin-colorpickpreview '.$field['name'].'_preview" style=""></span>';
            $field_html .= '<input type="text" '. $field_props .' class="thpladmin-colorpick"/>';
		}
		return $field_html;
	}
	
	public function render_form_field_blank($colspan = 3){
		?>
        <td colspan="<?php echo $colspan; ?>">&nbsp;</td>  
        <?php
	}

	public function init() {
		if(!is_admin() || (defined( 'DOING_AJAX' ) && DOING_AJAX)){
			if(is_array($this->settings) && isset($this->settings['enable_wmsc']) && $this->settings['enable_wmsc'] == 'yes'){
				$this->frontend_design();
			}
		}
	}

	public function frontend_design(){
		$thwmscf_settings = get_option('THWMSC_SETTINGS');
		$enable_login_step = isset($thwmscf_settings['enable_login_step']) ? $thwmscf_settings['enable_login_step'] : true;

		add_action( 'wp_enqueue_scripts', array( $this, 'thwmsc_frontend_scripts' ) );	
	    add_filter( 'woocommerce_locate_template', array( $this, 'wmsc_multistep_template' ), 10, 3 );
	    if($enable_login_step){
	        remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
			add_action('thwmscf_before_checkout_form', 'woocommerce_checkout_login_form');
		}

		$current_theme = wp_get_theme();
		$theme_template = $current_theme->get_template();

		if($theme_template === 'astra'){
			$astra_priority = apply_filters('thwmscf_astra_theme_priority', 20);
			add_filter('astra_woo_shop_product_structure_override', '__return_true');
			add_action( 'woocommerce_checkout_shipping', array( WC()->checkout(), 'checkout_form_shipping' ), $astra_priority);
		}
	}
	
	public function before_checkout_form(){
		if(!is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder')){
			echo '<div class="thwmscf-tab-panel" id="thwmscf-tab-panel-0">';
			do_action( 'woocommerce_checkout_login_form' );
			echo '</div>';
		}
	}

	public function thwmsc_frontend_scripts(){
		if(!is_checkout()){
			return;
		}
		
		$in_footer = apply_filters( 'thwmscf_enqueue_script_in_footer', true );

        wp_register_style( 'thwmscf-checkout-css', THWMSCF_ASSETS_URL . 'css/thwmscf-frontend.css', array(), THWMSCF_VERSION );
        wp_register_script('thwmscf-frontend-js', THWMSCF_ASSETS_URL.'js/thwmscf-frontend.js', array(), THWMSCF_VERSION, $in_footer);  

        wp_enqueue_style('thwmscf-checkout-css');    

        $display_prop = $this->get_settings();

        if($display_prop){      
			$tab_panel_style = '';
			$tab_style = '';
			$tab_style_active = '';
			
			$tab_align = isset($display_prop['tab_align']) && $display_prop['tab_align'] ? 'text-align:'.$display_prop['tab_align'].';' : '';
			
			if(isset($display_prop['tab_panel_bg_color']) && $display_prop['tab_panel_bg_color']){
				$tab_panel_style = 'background:'.$display_prop['tab_panel_bg_color'].' !important;';
			}
			
			if(isset($display_prop['step_bg_color']) && $display_prop['step_bg_color']){
				$tab_style = 'background:'.$display_prop['step_bg_color'].' !important;';
			}
			if(isset($display_prop['step_text_color']) && $display_prop['step_text_color']){
				$tab_style .= $tab_style ? ' color:'.$display_prop['step_text_color'].'' : 'color:'.$display_prop['step_text_color'].'';
				$tab_style .= ' !important';
			}
			
			if(isset($display_prop['step_bg_color_active']) && $display_prop['step_bg_color_active']){
				$tab_style_active = 'background:'.$display_prop['step_bg_color_active'].' !important;';
			}
			if(isset($display_prop['step_text_color_active']) && $display_prop['step_text_color_active']){
				$tab_style_active .= $tab_style_active ? ' color:'.$display_prop['step_text_color_active'].'' : 'color:'.$display_prop['step_text_color_active'].'';
				$tab_style_active .= ' !important';
			}

            $plugin_style = "
                    ul.thwmscf-tabs{ $tab_align }    
                    li.thwmscf-tab a{ $tab_style }                       
                    li.thwmscf-tab a.active { $tab_style_active }
					.thwmscf-tab-panels{ $tab_panel_style }";

			if(isset($display_prop['thwmscf_layout']) && $display_prop['thwmscf_layout'] == 'thwmscf_time_line_step') {
		        $enable_login_step = isset($display_prop['enable_login_step']) ? $display_prop['enable_login_step'] : true;
		        $billing_shipping_together =  isset($display_prop['make_billing_shipping_together']) ? $display_prop['make_billing_shipping_together'] : false;
		        $line_border_color = isset($display_prop['step_bg_color']) && $display_prop['step_bg_color'] ? 'border-top :4px solid '.$display_prop['step_bg_color'].';' : '';
		        $line_border_color_active = isset($display_prop['step_bg_color_active']) && $display_prop['step_bg_color_active'] ? 'border-top :4px solid '.$display_prop['step_bg_color_active'].';' : '';
		        if ($billing_shipping_together) {
		        	$step_count = $enable_login_step && !is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder') ? 3 : 2;
		        } else {
		        	$step_count = $enable_login_step && !is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder') ? 4 : 3;
		        }
		        $width_time_line = 'width:'. 100/$step_count .'%';

		        $plugin_style .= "
		        	.thwmscf_time_line_step ul.thwmscf-tabs li{ $width_time_line }
					.thwmscf_time_line_step ul.thwmscf-tabs li a {  $line_border_color }
		        	.thwmscf_time_line_step ul.thwmscf-tabs li a.active { $line_border_color_active }";
			}

            wp_add_inline_style( 'thwmscf-checkout-css', $plugin_style );  
        }        

        if(is_array($this->settings) && isset($this->settings['enable_wmsc']) && $this->settings['enable_wmsc'] == 'yes'){
       		wp_enqueue_script('thwmscf-frontend-js');
			$enable_validation = isset($this->settings['enable_step_validation']) ? $this->settings['enable_step_validation'] : true;
			$validation_msg = __('Invalid or data missing in the required field(s)', 'woo-multistep-checkout');
			
       		$script_var = array(
	    	    'enable_validation' => apply_filters('thwmscf_enable_step_validation', $enable_validation),
	    		'validation_msg' => apply_filters('thwmscf_validation_error', $validation_msg),
	    	);
	    	wp_localize_script('thwmscf-frontend-js', 'thwmscf_script_var', $script_var);
	    }

	} 

	public function wmsc_multistep_template( $template, $template_name, $template_path ){
        if('checkout/form-checkout.php' == $template_name ){         
        	if(is_array($this->settings) && isset($this->settings['enable_wmsc']) && $this->settings['enable_wmsc'] == 'yes'){  	
        		$template = THWMSCF_TEMPLATE_PATH . 'checkout/form-checkout.php';   
        	}
        }
        return $template;
    }
}

endif;