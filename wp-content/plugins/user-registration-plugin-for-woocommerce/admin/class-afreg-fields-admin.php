<?php 
if ( ! defined( 'WPINC' ) ) {
	die; 
}

if ( !class_exists( 'Addify_Registration_Fields_Addon_Admin' ) ) { 

	class Addify_Registration_Fields_Addon_Admin extends Addify_Registration_Fields_Addon {

		public function __construct() {
			
			add_action( 'admin_enqueue_scripts', array( $this, 'afreg_admin_scripts' ) );
			//Custom meta boxes
			add_action( 'admin_init', array( $this, 'afreg_register_metaboxes' ), 10 );
			add_action( 'save_post', array($this, 'afreg_meta_box_save' ));
			add_filter('post_row_actions', array($this, 'afreg_remove_bulk_actions'), 10, 1);
			add_filter( 'manage_afreg_fields_posts_columns', array( $this, 'afreg_custom_columns' ) );
			add_action( 'manage_afreg_fields_posts_custom_column' , array($this, 'afreg_custom_column'), 10, 2 );
			add_filter('bulk_actions-edit-afreg_fields', array($this, 'afreg_bulk_action'));
			add_filter( 'handle_bulk_actions-edit-afreg_fields', array($this, 'afreg_bulk_action_handler'), 10, 3 );
			add_action( 'admin_notices', array( $this, 'afreg_bulk_action_admin_notice' ) );
			add_action( 'admin_menu', array( $this, 'afreg_custom_menu_admin' ) );
			add_action('admin_init', array($this, 'afreg_options'));
			add_action( 'edit_user_profile', array($this, 'afreg_profile_fields' ));
			add_action( 'edit_user_profile_update', array($this, 'afreg_update_profile_fields' ));

			add_filter( 'manage_users_columns', array($this, 'afreg_modify_user_table' ));
			add_filter( 'manage_users_custom_column', array($this, 'afreg_modify_user_table_row'), 10, 3 );
			add_filter( 'user_row_actions', array( $this, 'afreg_user_row_actions' ), 10, 2 );
			add_action( 'load-users.php', array( $this, 'afreg_update_action' ) );
			add_action( 'restrict_manage_users', array( $this, 'afreg_status_filter' ), 10, 1 );
			add_action( 'pre_user_query', array( $this, 'afreg_filter_user_by_status' ) );
			add_action( 'admin_footer-users.php', array( $this, 'afreg_admin_footer' ) );
			add_action( 'load-users.php', array( $this, 'afreg_bulk_action_user' ) );

			add_action('wp_ajax_afreg_save_df_form', array($this, 'afreg_save_df_form'));
			add_action('wp_ajax_nopriv_afreg_save_df_form', array($this, 'afreg_save_df_form'));

			add_action( 'woocommerce_admin_order_data_after_billing_address', array($this, 'afreg_custom_checkout_field_display_admin_order_meta'), 10, 1 );
		}

		public function afreg_admin_scripts() { 
			
			wp_enqueue_script( 'color-spectrum-js', plugins_url( '/js/afreg_color_spectrum.js', __FILE__ ), false, '1.0' );
			wp_enqueue_style( 'color-spectrum-css', plugins_url( '/css/afreg_color_spectrum.css', __FILE__ ), false, '1.0' );
			wp_enqueue_style( 'afreg-admin-css', plugins_url( '/css/afreg_admin.css', __FILE__ ), false, '1.0' );
			wp_enqueue_script( 'afreg-admin-js', plugins_url( '/js/afreg_admin.js', __FILE__ ), false, '1.0' );
			$current_link = '';
			$afreg_data   = array(
				'admin_url'  => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('afreg-ajax-nonce'),
				'url' => $current_link,
				
			);
			wp_localize_script( 'afreg-admin-js', 'afreg_php_vars', $afreg_data );
			
		}

		public function afreg_custom_checkout_field_display_admin_order_meta( $order) { 

			

			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type' => 'afreg_fields',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			

			$afreg_extra_fields = get_posts($afreg_args);

			foreach ($afreg_extra_fields as $afreg_field) {

				$afreg_field_type          = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
				$afreg_field_order_details = get_post_meta( intval($afreg_field->ID), 'afreg_field_order_details', true );
				$afregcheck                = get_user_meta( $order->get_customer_id(), 'afreg_additional_' . intval($afreg_field->ID), true );

				if (!empty($afregcheck) && 'on' == $afreg_field_order_details) { 

					$value = get_user_meta( $order->get_customer_id(), 'afreg_additional_' . intval($afreg_field->ID), true );
					
					if ( 'checkbox' == $afreg_field_type) {
						if ('yes' == $value) {
							echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_reg') . '</b>' . esc_html__('Yes', 'addify_reg') . '</p>';
						} else {
							echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_reg') . '</b>' . esc_html__('No', 'addify_reg') . '</p>';
						}
							
					} elseif ( 'fileupload' == $afreg_field_type) {

						$value = '<p>' . esc_url(AFREG_URL . 'uploaded_files/' . esc_attr($value));
						echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_reg') . '</b>' . esc_attr($value) . '</p>';

					} elseif ( in_array( $afreg_field_type , array( 'multiselect' , 'multi_checkbox' , 'select', 'radio') ) ) {
						 $val_array           = explode(', ' , $value );
						 $afreg_field_options = unserialize(get_post_meta(  intval($afreg_field->ID) , 'afreg_field_option', true )); 
						 $value               = '';
						foreach ( $val_array as $option_val ) {
							foreach ($afreg_field_options as $afreg_field_option ) { 
								if ( esc_attr( $option_val ) == $afreg_field_option['field_value'] ) {
									$value .=  $afreg_field_option['field_text'] . ', ';
								}
							}
						}

						echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_reg') . '</b>' . esc_attr($value) . '</p>';
					} elseif ('timepicker' == $afreg_field_type) {

						echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_reg') . '</b><input type="time" value="' . esc_attr($value) . '" readonly="readonly"></p>';
							
					} else {
						echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_reg') . '</b>' . esc_attr($value) . '</p>';
					}

				}
			}


		}

		public function afreg_register_metaboxes() {

			add_meta_box( 'afreg_field_details', esc_html__( 'Field Details', 'addify_reg' ), array( $this, 'afreg_field_details_callback' ), 'afreg_fields', 'normal', 'high' );
			add_meta_box( 'afreg_field_formating', esc_html__( 'Field Formating', 'addify_reg' ), array( $this, 'afreg_field_formating_callback' ), 'afreg_fields', 'normal', 'high' );
			add_meta_box( 'afreg_field_user_role', esc_html__( 'User Role Dependency', 'addify_reg' ), array( $this, 'afreg_field_user_role_callback' ), 'afreg_fields', 'normal', 'high' );
			add_meta_box( 'afreg_field_status', esc_html__( 'Field Status', 'addify_reg' ), array( $this, 'afreg_field_status_callback' ), 'afreg_fields', 'side', 'high' );
			
		}

		public function afreg_field_details_callback() {
			global $post;
			wp_nonce_field( 'afreg_nonce_action', 'afreg_nonce_field' );
			$afreg_field_type      = get_post_meta( $post->ID, 'afreg_field_type', true );
			$afreg_field_options   = unserialize(get_post_meta( $post->ID, 'afreg_field_option', true )); 
			$afreg_field_file_size = get_post_meta( $post->ID, 'afreg_field_file_size', true );
			$afreg_field_file_type = get_post_meta( $post->ID, 'afreg_field_file_type', true );
			
			?>
			<div class="addify_reg">
				<div class="meta_field_full">
					<label for="afreg_field_label"><?php echo esc_html__('Field Label', 'addify_reg'); ?></label>
					<p class="afreg_field_label_msg"><?php echo esc_html__( 'Enter the text in above title field, that will become field label.', 'addify_reg' ); ?></p>
				</div>

				<div class="meta_field_full">
					<label for="afreg_field_type"><?php echo esc_html__('Field Type', 'addify_reg'); ?></label>
					<select name="afreg_field_type" id="afreg_field_type" class="afreg_field_select" onchange="afreg_show_options(this.value)">
						<option value="text" <?php echo selected(esc_attr($afreg_field_type), 'text'); ?>><?php echo esc_html__('Text', 'addify_reg'); ?></option>
						<option value="textarea" <?php echo selected(esc_attr($afreg_field_type), 'textarea'); ?>><?php echo esc_html__('Textarea', 'addify_reg'); ?></option>
						<option value="email" <?php echo selected(esc_attr($afreg_field_type), 'email'); ?>><?php echo esc_html__('Email', 'addify_reg'); ?></option>
						<option value="select" <?php echo selected(esc_attr($afreg_field_type), 'select'); ?>><?php echo esc_html__('Selectbox', 'addify_reg'); ?></option>
						<option value="multiselect" <?php echo selected(esc_attr($afreg_field_type), 'multiselect'); ?>><?php echo esc_html__('Multi Selectbox', 'addify_reg'); ?></option>
						<option value="checkbox" <?php echo selected(esc_attr($afreg_field_type), 'checkbox'); ?>><?php echo esc_html__('Checkbox', 'addify_reg'); ?></option>
						<option value="multi_checkbox" <?php echo selected(esc_attr($afreg_field_type), 'multi_checkbox'); ?>><?php echo esc_html__('Multi Checkbox', 'addify_reg'); ?></option>
						<option value="radio" <?php echo selected(esc_attr($afreg_field_type), 'radio'); ?>><?php echo esc_html__('Radio Button', 'addify_reg'); ?></option>
						<option value="number" <?php echo selected(esc_attr($afreg_field_type), 'number'); ?>><?php echo esc_html__('Number', 'addify_reg'); ?></option>
						<option value="password" <?php echo selected(esc_attr($afreg_field_type), 'password'); ?>><?php echo esc_html__('Password', 'addify_reg'); ?></option>
						<option value="fileupload" <?php echo selected(esc_attr($afreg_field_type), 'fileupload'); ?>><?php echo esc_html__('File Upload (Supports my account registration page only)', 'addify_reg'); ?></option>
						<option value="color" <?php echo selected(esc_attr($afreg_field_type), 'color'); ?>><?php echo esc_html__('Color Picker', 'addify_reg'); ?></option>
						<option value="datepicker" <?php echo selected(esc_attr($afreg_field_type), 'datepicker'); ?>><?php echo esc_html__('Date Picker', 'addify_reg'); ?></option>
						<option value="timepicker" <?php echo selected(esc_attr($afreg_field_type), 'timepicker'); ?>><?php echo esc_html__('Time Picker', 'addify_reg'); ?></option>
						<option value="googlecaptcha" <?php echo selected(esc_attr($afreg_field_type), 'googlecaptcha'); ?>><?php echo esc_html__('Google reCAPTCHA (Supports my account registration page only', 'addify_reg'); ?></option>
					</select>
				</div>

				<div id="afreg_recaptcha" class="meta_field_full">
					<p class="afreg_field_label_msg"><?php echo esc_html__( 'For google reCaptcha field you must enter correct site key and secret key in our module settings. Without these keys google reCaptcha will not work.', 'addify_reg' ); ?></p>
				</div>

				<div class="meta_field_full afreg_fileupload">
					<label for="afreg_field_file_size"><?php echo esc_html__('File Upload Size(MB)', 'addify_reg'); ?></label>
					<input type="number" name="afreg_field_file_size" id="afreg_field_file_size" class="" value="<?php echo esc_attr($afreg_field_file_size); ?>" />
				</div>

				<div class="meta_field_full afreg_fileupload">
					<label for="afreg_field_file_type"><?php echo esc_html__('Allowed File Types(Add Comma(,) separated types. e.g png,jpg,gif)', 'addify_reg'); ?></label>
					<input type="text" name="afreg_field_file_type" id="afreg_field_file_type" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_file_type); ?>" />
				</div>

				<div class="meta_field_full" id="afreg_field_options">
					<label for="afreg_field_options"><?php echo esc_html__('Field Options', 'addify_reg'); ?></label>
					<div class="afreg_field_options">
						<table cellspacing="0" cellpadding="0" border="1" width="100%">
							<thead>
								<tr>
									<th><?php echo esc_html__('Option Value', 'addify_reg'); ?></th>
									<th><?php echo esc_html__('Field Label/Text', 'addify_reg'); ?></th>
									<th><?php echo esc_html__('Action', 'addify_reg'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$afreg_a = 0;
								if (!empty($afreg_field_options)) {
									foreach ($afreg_field_options as $afreg_field_option) { 
										?>
									<tr>
										<td>
											<input type="text" name="afreg_field_option[<?php echo intval($afreg_a); ?>][field_value]" id="afreg_field_option_value<?php echo intval($afreg_a); ?>" class="option_field" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" />
										</td>
										<td>
											<input type="text" name="afreg_field_option[<?php echo intval($afreg_a); ?>][field_text]" id="afreg_field_option_value<?php echo intval($afreg_a); ?>" class="option_field" value="<?php echo esc_attr($afreg_field_option['field_text']); ?>" />
										</td>
										<td><button type="button" class="button button-danger" onclick="jQuery(this).closest('tr').remove();"><?php echo esc_html__('Remove Option', 'addify_reg'); ?></button></td>
									</tr>
									<?php $afreg_a++; } } ?>
							</tbody>
							<tfoot>
								<tr id="NewField"></tr>
							</tfoot>
							
						</table>

						<div class="afreg_addbt"><button type="button" class="button-primary" onclick="afreg_add_option()"><?php echo esc_html__('Add New Option', 'addify_reg'); ?></button></div>
					</div>
				</div>

			</div>

			<?php 
		}

		public function afreg_field_formating_callback() {
			global $post;
			wp_nonce_field( 'afreg_nonce_action', 'afreg_nonce_field' );
			$afreg_field_required      = get_post_meta( $post->ID, 'afreg_field_required', true );
			$afreg_field_read_only     = get_post_meta( $post->ID, 'afreg_field_read_only', true );
			$afreg_field_order_details = get_post_meta( $post->ID, 'afreg_field_order_details', true );
			$afreg_field_width         = get_post_meta( $post->ID, 'afreg_field_width', true );
			$afreg_field_placeholder   = get_post_meta( $post->ID, 'afreg_field_placeholder', true );
			$afreg_field_description   = get_post_meta( $post->ID, 'afreg_field_description', true );
			$afreg_field_css           = get_post_meta( $post->ID, 'afreg_field_css', true );
			
			?>
			<div class="addify_reg">
				<div class="meta_field_formating afreg_recaptchahide">
					<label for="afreg_field_required"><?php echo esc_html__('Required Field', 'addify_reg'); ?></label>
					<input type="checkbox" name="afreg_field_required" id="afreg_field_required" <?php echo checked(esc_attr($afreg_field_required), 'on'); ?> />
				</div>

				<div class="meta_field_formating afreg_recaptchahide">
					<label for="afreg_field_read_only"><?php echo esc_html__('Read Only Field(Customer can not update this from My Account page)', 'addify_reg'); ?></label>
					<input type="checkbox" name="afreg_field_read_only" id="afreg_field_read_only" <?php echo checked(esc_attr($afreg_field_read_only), 'on'); ?> />
				</div>

				<div class="meta_field_formating afreg_recaptchahide">
					<label for="afreg_field_order_details"><?php echo esc_html__('Show in admin order detail page and order email', 'addify_reg'); ?></label>
					<input type="checkbox" name="afreg_field_order_details" id="afreg_field_order_details" <?php echo checked(esc_attr($afreg_field_order_details), 'on'); ?> />
				</div>

				<div class="meta_field_formating afreg_recaptchahide">
					<label for="afreg_field_width"><?php echo esc_html__('Field Width', 'addify_reg'); ?></label>
					<select name="afreg_field_width" id="afreg_field_width">
						<option value="full" <?php echo selected(esc_attr($afreg_field_width), 'full'); ?>><?php echo esc_html__('Full Width', 'addify_reg'); ?></option>
						<option value="half" <?php echo selected(esc_attr($afreg_field_width), 'half'); ?>><?php echo esc_html__('Half Width', 'addify_reg'); ?></option>
					</select>
					
				</div>

				<div class="meta_field_full afreg_recaptchahide">
					<label for="afreg_field_placeholder"><?php echo esc_html__('Field Placeholder Text', 'addify_reg'); ?></label>
					<input type="text" name="afreg_field_placeholder" id="afreg_field_placeholder" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_placeholder); ?>" />
				</div>

				<div class="meta_field_full">
					<label for="afreg_field_description"><?php echo esc_html__('Field Description', 'addify_reg'); ?></label>
					<input type="text" name="afreg_field_description" id="afreg_field_description" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_description); ?>" />
				</div>

				<div class="meta_field_full afreg_recaptchahide">
					<label for="afreg_field_css"><?php echo esc_html__('Field Custom Css Class', 'addify_reg'); ?></label>
					<input type="text" name="afreg_field_css" id="afreg_field_css" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_css); ?>" />
				</div>

			</div>

			<?php 
		}

		public function afreg_field_user_role_callback() {

			global $post;
			wp_nonce_field( 'afreg_nonce_action', 'afreg_nonce_field' );
			$afreg_field_user_roles = get_post_meta( $post->ID, 'afreg_field_user_roles', true );
			$afreg_is_dependable    = get_post_meta( $post->ID, 'afreg_is_dependable', true );
			?>
				<div class="addify_reg">

					<div class="meta_field_formating afreg_recaptchahide">
						<label for="afreg_field_css"><?php echo esc_html__('is Dependable?', 'addify_reg'); ?></label>
						<input type="checkbox" name="afreg_is_dependable" id="afreg_is_dependable" <?php echo checked(esc_attr($afreg_is_dependable), 'on'); ?> />
					</div>
					
					<div class="meta_field_formating afreg_recaptchahide">
						<label for="afreg_field_required"><?php echo esc_html__('Select User Roles', 'addify_reg'); ?></label>

						<div class="all_cats_role">
						<ul>
							<?php

							global $wp_roles;
							$roles = $wp_roles->get_names();

							$field_roles = unserialize($afreg_field_user_roles);

							if ( !empty( $roles)) {

								foreach ($roles as $key => $value) {
									if ( 'administrator' != $key) {
										?>
									<li class="par_cat">
										
										<input type="checkbox" class="parent" name="afreg_field_user_roles[]" id="afreg_field_user_roles" value="<?php echo esc_attr( $key ); ?>"
										<?php
										if ( !empty($field_roles) && in_array( $key, $field_roles)) {
											echo 'checked';
										}
										?>
										/>
										<?php echo esc_attr($value); ?>

									</li>
										<?php
									}
								}
							}
							?>
						</ul>
					</div>

					<p class="description afreg_enable_user_role"><?php echo esc_html__('Select user roles on which you want to show this field, leave empty for show in all.', 'addify_reg'); ?></p>
			
						
					</div>

				</div>
			<?php
		}

		public function afreg_field_status_callback() {

			global $post;
			wp_nonce_field( 'afreg_nonce_action', 'afreg_nonce_field' );
			?>
				<div class="addify_reg">

					<div class="meta_field_full">
						<label for="afreg_field_sort_order"><?php echo esc_html__('Field Sort Order', 'addify_reg'); ?></label>
						<input type="number" min="0" name="afreg_field_sort_order" id="afreg_field_sort_order" value="<?php echo esc_attr($post->menu_order); ?>" />
					</div>

					<div class="meta_field_formating">
						<label for="afreg_field_status"><?php echo esc_html__('Field Status', 'addify_reg'); ?></label>
						<select name="afreg_field_status" id="afreg_field_status">
							<option value="publish" <?php echo selected(esc_attr($post->post_status), 'publish'); ?>><?php echo esc_html__('Active', 'addify_reg'); ?></option>
							<option value="draft" <?php echo selected(esc_attr($post->post_status), 'draft'); ?>><?php echo esc_html__('Inactive', 'addify_reg'); ?></option>
						</select>
					</div>
				</div>
			<?php
		}

		public function afreg_meta_box_save( $post_id ) {

			// return if we're doing an auto save
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( get_post_status( $post_id ) === 'auto-draft' ) {
				return;
			}
		   
			if ( isset( $_POST['afreg_field_type'] ) ) { 

				if (!empty($_REQUEST['afreg_nonce_field'])) {

					$retrieved_nonce = sanitize_text_field($_REQUEST['afreg_nonce_field']);
				} else {
						$retrieved_nonce = 0;
				}

				if (!wp_verify_nonce($retrieved_nonce, 'afreg_nonce_action')) {

					die('Failed security check');
				}

				update_post_meta( intval($post_id), 'afreg_field_type', sanitize_text_field( $_POST['afreg_field_type'] ) );
			}

			remove_action( 'save_post', array($this, 'afreg_meta_box_save'));

			if ( isset($_POST['afreg_field_status']) ) {
				wp_update_post( array( 'ID' => intval($post_id), 'post_status' => sanitize_text_field($_POST['afreg_field_status']) ) );
			}

			if ( isset($_POST['afreg_field_sort_order']) ) {
				wp_update_post( array( 'ID' => intval($post_id), 'menu_order' => sanitize_text_field($_POST['afreg_field_sort_order']) ) );
			}

			add_action( 'save_post', array($this, 'afreg_meta_box_save' ));

			if ( isset( $_POST['afreg_field_option'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_option', serialize(sanitize_meta( '', $_POST['afreg_field_option'], '')));
			} else {
				delete_post_meta( intval($post_id), 'afreg_field_option' );
			}


			if ( isset( $_POST['afreg_field_required'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_required', sanitize_text_field( $_POST['afreg_field_required'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_required', 'off' );	
			}

			if ( isset( $_POST['afreg_field_read_only'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_read_only', sanitize_text_field( $_POST['afreg_field_read_only'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_read_only', 'off' );
			}

			if ( isset( $_POST['afreg_field_order_details'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_order_details', sanitize_text_field( $_POST['afreg_field_order_details'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_order_details', 'off' );
			}

			if ( isset( $_POST['afreg_field_width'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_width', sanitize_text_field( $_POST['afreg_field_width'] ) );
			}

			if ( isset( $_POST['afreg_field_placeholder'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_placeholder', sanitize_text_field( $_POST['afreg_field_placeholder'] ) );
			}

			if ( isset( $_POST['afreg_field_description'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_description', sanitize_text_field( $_POST['afreg_field_description'] ) );
			}

			if ( isset( $_POST['afreg_field_css'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_css', sanitize_text_field( $_POST['afreg_field_css'] ) );
			}

			if ( isset( $_POST['afreg_field_file_size'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_file_size', sanitize_text_field( $_POST['afreg_field_file_size'] ) );
			}

			if ( isset( $_POST['afreg_field_file_type'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_file_type', sanitize_text_field( $_POST['afreg_field_file_type'] ) );
			}

			if ( isset( $_POST['afreg_field_user_roles'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_user_roles', serialize(sanitize_meta( '', $_POST['afreg_field_user_roles'], '')));
			} else {

				update_post_meta( intval($post_id), 'afreg_field_user_roles', '' );
			}

			if ( isset( $_POST['afreg_is_dependable'] ) ) {
				update_post_meta( intval($post_id), 'afreg_is_dependable', sanitize_text_field( $_POST['afreg_is_dependable'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_is_dependable', 'off' );
			}

		}

		public function afreg_remove_bulk_actions( $actions ) {
			unset($actions['view']);
			return $actions;
		}

		public function afreg_custom_columns( $columns) {
			
			unset($columns['date']);
			$columns['afreg_field_type']       = esc_html__( 'Field Type', 'addify_reg' );
			$columns['afreg_field_status']     = esc_html__( 'Status', 'addify_reg' );
			$columns['afreg_field_sort_order'] = esc_html__( 'Sort Order', 'addify_reg' );
			

			return $columns;
		}

		public function afreg_custom_column( $column, $post_id ) {
			$afreg_post = get_post($post_id);
			switch ( $column ) {
				case 'afreg_field_type':
					echo esc_attr(ucwords(str_replace('_', ' ', get_post_meta($post_id, 'afreg_field_type', true))));
					break;

				case 'afreg_field_status':
					if ('publish' == $afreg_post->post_status) {
						echo esc_html__( 'Active', 'addify_reg' );
					} else {
						esc_html__( 'Inactive', 'addify_reg' );
					}
					break;

				case 'afreg_field_sort_order':
					echo esc_attr($afreg_post->menu_order);
					break;

			}
		}

		public function afreg_bulk_action( $bulk_actions) {
			$bulk_actions['afreg_active']   = esc_html__( 'Active', 'addify_reg' );
			$bulk_actions['afreg_inactive'] = esc_html__( 'Inactive', 'addify_reg' );
			return $bulk_actions;
		}

		public function afreg_bulk_action_handler( $redirect_to, $action_name, $post_ids ) {

			if ( 'afreg_active' === $action_name ) {

				foreach ( $post_ids as $post_id ) { 
					wp_update_post( array( 'ID' => intval($post_id), 'post_status' => 'publish' ) );
				} 

				$redirect_to = add_query_arg( 'afreg_active', count( $post_ids ), $redirect_to ); 
				return $redirect_to; 

			} elseif ( 'afreg_inactive' === $action_name ) {

				foreach ( $post_ids as $post_id ) { 
					wp_update_post( array( 'ID' => intval($post_id), 'post_status' => 'draft' ) );
				} 

				$redirect_to = add_query_arg( 'afreg_inactive', count( $post_ids ), $redirect_to ); 
				return $redirect_to;
			} else {
				return $redirect_to;
			}

		} 

		public function afreg_bulk_action_admin_notice() { 

			$afreg_allowed_tags = array(
			'a' => array(
			'class' => array(),
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
			),
			'b' => array(),
			
			'div' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
			),
			'p' => array(
			'class' => array(),
			),
			'strong' => array(),
			
			);

			if ( ! empty( $_REQUEST['afreg_active'] ) ) { 
				$posts_count     = intval( $_REQUEST['afreg_active'] ); 
				$afreg_woo_check = '<div id="message" class="updated notice notice-success is-dismissible"><p>' . $posts_count . ' field(s) are set to active.</p><button type="button" class="notice-dismiss"></button></div>';
				echo wp_kses( __( $afreg_woo_check, 'addify_reg' ), $afreg_allowed_tags);

			} elseif (! empty( $_REQUEST['afreg_inactive'] ) ) {
				$posts_count     = intval( $_REQUEST['afreg_inactive'] ); 
				$afreg_woo_check = '<div id="message" class="updated notice notice-success is-dismissible"><p>' . $posts_count . ' field(s) are set to inactive.</p><button type="button" class="notice-dismiss"></button></div>';
				echo wp_kses( __( $afreg_woo_check, 'addify_reg' ), $afreg_allowed_tags);
			}
		} 

		public function afreg_custom_menu_admin() {	

			add_submenu_page(
				'edit.php?post_type=afreg_fields',
				esc_html__( 'Enable Default Fields', 'addify_reg' ),
				esc_html__( 'Enable Default Fields', 'addify_reg' ),
				'manage_options',
				'afreg-default-fields',
				array($this, 'afreg_default_fields')
			);
			
			add_submenu_page(
				'edit.php?post_type=afreg_fields',
				esc_html__( 'Settings', 'addify_reg' ),
				esc_html__( 'Settings', 'addify_reg' ),
				'manage_options',
				'afreg-fields-settings',
				array($this, 'afreg_settings_page')
			);
		}

		public function afreg_settings_page() {

			if ( isset( $_GET[ 'tab' ] ) ) {  
				$active_tab = sanitize_text_field($_GET[ 'tab' ]);  
			} else {
				$active_tab = 'tab_one';
			}
			?>
				<div class="wrap">

					<h2><?php echo esc_html__('Registration Fields Settings', 'addify_reg'); ?></h2>
					<?php settings_errors(); ?> 

					<h2 class="nav-tab-wrapper">  
					
						<a href="?post_type=afreg_fields&page=afreg-fields-settings&tab=tab_one" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_one' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('General Settings', 'addify_reg'); ?></a> 
						<a href="?post_type=afreg_fields&page=afreg-fields-settings&tab=tab_two" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_two' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('User Role Settings', 'addify_reg'); ?></a> 
						<a href="?post_type=afreg_fields&page=afreg-fields-settings&tab=tab_three" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_three' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Approve New User Settings', 'addify_reg'); ?></a> 
						<a href="?post_type=afreg_fields&page=afreg-fields-settings&tab=tab_four" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_four' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Email Settings', 'addify_reg'); ?></a> 
					</h2>

					<form method="post" action="options.php"> 
						<?php
						if ( 'tab_one' == $active_tab ) {  
							settings_fields( 'setting-group-1' );
							do_settings_sections( 'addify-registration-1' );
						}

						if ( 'tab_two' == $active_tab ) {  
							settings_fields( 'setting-group-2' );
							do_settings_sections( 'addify-registration-2' );
						}

						if ( 'tab_three' == $active_tab ) {  
							settings_fields( 'setting-group-3' );
							do_settings_sections( 'addify-registration-3' );
						}

						if ( 'tab_four' == $active_tab ) {  
							settings_fields( 'setting-group-4' );
							do_settings_sections( 'addify-registration-4' );
						}
						?>
						<?php submit_button(); ?> 
					</form> 

				</div>
			<?php 

		}

		public function afreg_options() {

			add_settings_section(  
				'page_1_section',         // ID used to identify this section and with which to register options  
				'',   // Title to be displayed on the administration page  
				array($this, 'afreg_page_1_section_callback'), // Callback used to render the description of the section  
				'addify-registration-1'                           // Page on which to add this section of options  
			);

			add_settings_field (   
				'afreg_additional_fields_section_title',                      // ID used to identify the field throughout the theme  
				esc_html__('Additional Fields Section Title', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_additional_fields_section_title_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-1',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is the title for the section where additional fields are displayed on front end registration form.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-1',  
				'afreg_additional_fields_section_title'  
			);

			add_settings_section(  
				'page_2_section',         // ID used to identify this section and with which to register options  
				'',   // Title to be displayed on the administration page  
				array($this, 'afreg_page_2_section_callback'), // Callback used to render the description of the section  
				'addify-registration-1'                           // Page on which to add this section of options  
			);

			add_settings_field (   
				'afreg_site_key',                      // ID used to identify the field throughout the theme  
				esc_html__('Site Key', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_site_key_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-1',                          // The page on which this option will be displayed  
				'page_2_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is gogle reCaptcha site key, you can get this from google. With this key google reCaptcha will not work.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-1',  
				'afreg_site_key'  
			);

			add_settings_field (   
				'afreg_secret_key',                      // ID used to identify the field throughout the theme  
				esc_html__('Secret Key', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_secret_key_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-1',                          // The page on which this option will be displayed  
				'page_2_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is gogle reCaptcha secret key, you can get this from google. With this key google reCaptcha will not work.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-1',  
				'afreg_secret_key'  
			);

			//Tab 2
			add_settings_section(  
				'page_1_section',         // ID used to identify this section and with which to register options  
				'',   // Title to be displayed on the administration page  
				array($this, 'afreg_page_22_section_callback'), // Callback used to render the description of the section  
				'addify-registration-2'                           // Page on which to add this section of options  
			);

			add_settings_field (   
				'afreg_enable_user_role',                      // ID used to identify the field throughout the theme  
				esc_html__('Enable User Role Selection', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_enable_user_role_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-2',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('Enable/Disable User Role selection on registraiton page. If this is enable then a user role dropdown will be shown on registration page.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-2',  
				'afreg_enable_user_role'  
			);

			add_settings_field (   
				'afreg_user_role_field_text',                      // ID used to identify the field throughout the theme  
				esc_html__('User Role Field Label', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_user_role_field_text_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-2',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('Field label for user role selection select box.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-2',  
				'afreg_user_role_field_text'  
			);

			add_settings_field (   
				'afreg_user_roles',                      // ID used to identify the field throughout the theme  
				esc_html__('Select User Roles', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_user_roles_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-2',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('Select which user roles you want to show in dropdown on registration page. Note: Administrator role is not avaiable for show in dropdown.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-2',  
				'afreg_user_roles'  
			);

			//Tab 3
			add_settings_section(  
				'page_1_section',         // ID used to identify this section and with which to register options  
				'',   // Title to be displayed on the administration page  
				array($this, 'afreg_page_3_section_callback'), // Callback used to render the description of the section  
				'addify-registration-3'                           // Page on which to add this section of options  
			);

			add_settings_field (   
				'afreg_enable_approve_user',                      // ID used to identify the field throughout the theme  
				esc_html__('Enable Approve New User', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_enable_approve_user_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-3',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('Enable/Disable Approve new user. When this option is enabled all new registered users will be set to Pending until admin approves', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-3',  
				'afreg_enable_approve_user'  
			);

			add_settings_field (   
				'afreg_enable_approve_user_checkout',                      // ID used to identify the field throughout the theme  
				esc_html__('Enable Approve New User at Checkout Page', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_enable_approve_user_checkout_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-3',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__(' Enable/Disable Approve new user at checkout page. ', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-3',  
				'afreg_enable_approve_user_checkout'  
			);

			add_settings_field (   
				'afreg_exclude_user_roles_approve_new_user',                      // ID used to identify the field throughout the theme  
				esc_html__('Exclude User Roles', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_exclude_user_roles_approve_new_user_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-3',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('Select which user roles users you want to exclude from manual approval. These user roles users will be automatically approved.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-3',  
				'afreg_exclude_user_roles_approve_new_user'  
			);

			add_settings_section(  
				'page_2_section',         // ID used to identify this section and with which to register options  
				'',   // Title to be displayed on the administration page  
				array($this, 'afreg_page_33_section_callback'), // Callback used to render the description of the section  
				'addify-registration-3'                           // Page on which to add this section of options  
			);

			add_settings_field (   
				'afreg_user_pending_approval_message',                      // ID used to identify the field throughout the theme  
				esc_html__('Message for Users when Account is Created', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_user_pending_approval_message_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-3',                          // The page on which this option will be displayed  
				'page_2_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('First message that will be displayed to user when he/she completes the registration process, this message will be displayed only when manual approval is required. ', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-3',  
				'afreg_user_pending_approval_message'  
			);

			add_settings_field (   
				'afreg_user_approval_message',                      // ID used to identify the field throughout the theme  
				esc_html__('Message for Users when Account is pending for approval', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_user_approval_message_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-3',                          // The page on which this option will be displayed  
				'page_2_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This will be displayed when user will attempt to login after registration and his/her account is still pending for admin approval. ', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-3',  
				'afreg_user_approval_message'  
			);

			add_settings_field (   
				'afreg_user_disapproved_message',                      // ID used to identify the field throughout the theme  
				esc_html__('Message for Users when Account is disapproved', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_user_disapproved_message_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-3',                          // The page on which this option will be displayed  
				'page_2_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('Message for Users when Account is Disapproved By Admin.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-3',  
				'afreg_user_disapproved_message'  
			);

			//Tab 4

			add_settings_section(  
				'page_1_section',         // ID used to identify this section and with which to register options  
				'',   // Title to be displayed on the administration page  
				array($this, 'afreg_page_4_section_callback'), // Callback used to render the description of the section  
				'addify-registration-4'                           // Page on which to add this section of options  
			);

			add_settings_field (   
				'afreg_admin_email',                      // ID used to identify the field throughout the theme  
				esc_html__('Admin Email Address', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_admin_email_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This email address will be used for getting new user email notification for admin, if this is empty then defualt wordpress admin email address will be used.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_admin_email'  
			);


			//enable admin email notification
			add_settings_field (   
				'afreg_enable_admin_email',                      // ID used to identify the field throughout the theme  
				esc_html__('Enable admin email notification', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_enable_admin_email_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('Enable or Disable new user notification to admin from this module. ', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_enable_admin_email'  
			);


			add_settings_field (   
				'afreg_admin_email_subject',                      // ID used to identify the field throughout the theme  
				esc_html__('Admin Email Subject', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_admin_email_subject_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This email subject is used when new user notification is sent to admin. ', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_admin_email_subject'  
			);

			add_settings_field (   
				'afreg_admin_email_text',                      // ID used to identify the field throughout the theme  
				esc_html__('Admin Email Text', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_admin_email_text_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This email text will be used when new user notification is sent to admin. If Approve new user is active then you can write text about new user approval.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_admin_email_text'  
			);


			//enable admin email notification
			add_settings_field (   
				'afreg_enable_pending_user_email',                      // ID used to identify the field throughout the theme  
				esc_html__('Enable welcome email notification', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_enable_pending_user_email_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('Enable or Disable welcome email notification from this module. ', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_enable_pending_user_email'  
			);


			add_settings_field (   
				'afreg_pending_approval_email_subject',                      // ID used to identify the field throughout the theme  
				esc_html__('Welcome/Pending Email Subject', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_pending_approval_email_subject_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is the email subject; this subject is used when the email is sent to the user on account creation to include fields data and manual approval message.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_pending_approval_email_subject'  
			);

			add_settings_field (   
				'afreg_pending_approval_email_text',                      // ID used to identify the field throughout the theme  
				esc_html__('Welcome/Pending Email Body Text', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_pending_approval_email_text_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is the email body; when a new customer registers this email be automatically sent and the custom fields will be included in that email. This body text will be included along with the default fields data.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_pending_approval_email_text'  
			);


			add_settings_field (   
				'afreg_approved_email_subject',                      // ID used to identify the field throughout the theme  
				esc_html__('Approved Email Subject', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_approved_email_subject_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is the approved email subject, this subject is when used when account is approved by administrator.  ', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_approved_email_subject'  
			);

			add_settings_field (   
				'afreg_approved_email_text',                      // ID used to identify the field throughout the theme  
				esc_html__('Approved Email Text', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_approved_email_text_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is the approved email message, this message is used when account is approved by administrator. ', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_approved_email_text'  
			);


			add_settings_field (   
				'afreg_disapproved_email_subject',                      // ID used to identify the field throughout the theme  
				esc_html__('Disapproved Email Subject', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_disapproved_email_subject_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is the disapproved email subject, this subject is used when account is disapproved by administrator.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_disapproved_email_subject'  
			);

			add_settings_field (   
				'afreg_disapproved_email_text',                      // ID used to identify the field throughout the theme  
				esc_html__('Disapproved Email Text', 'addify_reg'),    // The label to the left of the option interface element  
				array($this, 'afreg_disapproved_email_text_callback'),   // The name of the function responsible for rendering the option interface  
				'addify-registration-4',                          // The page on which this option will be displayed  
				'page_1_section',         // The name of the section to which this field belongs  
				array(                              // The array of arguments to pass to the callback. In this case, just a description.  
					esc_html__('This is the disapproved email message, this message is used when account is disapproved by administrator.', 'addify_reg'),
				)  
			);  
			register_setting(  
				'setting-group-4',  
				'afreg_disapproved_email_text'  
			);

		}

		public function afreg_page_1_section_callback() { 
			?>

		   <p><?php echo esc_html__('Manage registration module general settings from here.', 'addify_reg'); ?></p>

			<?php 
		} // function afreg_page_1_section_callback

		public function afreg_additional_fields_section_title_callback( $args) {  
			?>
			<input type="text" id="afreg_additional_fields_section_title" class="setting_fields" name="afreg_additional_fields_section_title" value="<?php echo esc_attr(__(get_option('afreg_additional_fields_section_title') , 'addify_reg')); ?>">
			<p class="description afreg_additional_fields_section_title"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_additional_fields_section_title_callback 

		public function afreg_page_2_section_callback() { 
			?>

		   <h3><?php echo esc_html__('Google reCaptcha Settings', 'addify_reg'); ?></h3>

			<?php 
		} // function afreg_page_2_section_callback

		public function afreg_site_key_callback( $args) {  
			?>
			<input type="text" id="afreg_site_key" class="setting_fields" name="afreg_site_key" value="<?php echo esc_attr(get_option('afreg_site_key')); ?>">
			<p class="description afreg_site_key"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_site_key_callback 

		public function afreg_secret_key_callback( $args) {  
			?>
			<input type="text" id="afreg_secret_key" class="setting_fields" name="afreg_secret_key" value="<?php echo esc_attr(get_option('afreg_secret_key')); ?>">
			<p class="description afreg_secret_key"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_secret_key_callback 


		//Tab 2

		public function afreg_page_22_section_callback() { 
			?>

		   <p><?php echo esc_html__('Manage user role settings from here. Choose wheather you want to show user role dropdown on registraiton page or not and choose which user roles you want to show in dropdown on registration page.', 'addify_reg'); ?></p>

			<?php 
		} // function afreg_page_22_section_callback

		public function afreg_user_role_field_text_callback( $args) {  
			?>
			<input type="text" id="afreg_user_role_field_text" class="setting_fields" name="afreg_user_role_field_text" value="<?php echo esc_attr(get_option('afreg_user_role_field_text')); ?>">
			<p class="description afreg_user_role_field_text"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_user_role_field_text_callback

		public function afreg_enable_user_role_callback( $args) {  
			?>
			<input type="checkbox" id="afreg_enable_user_role" class="setting_fields" name="afreg_enable_user_role" value="yes" <?php checked('yes', esc_attr( get_option('afreg_enable_user_role'))); ?> >
			<p class="description afreg_enable_user_role"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_enable_user_role_callback


		public function afreg_user_roles_callback( $args) {  
			?>
			
			<div class="all_cats">
				<ul>
					<?php

					global $wp_roles;
					$roles = $wp_roles->get_names();

					if ( !empty( $roles)) {

						foreach ($roles as $key => $value) {
							if ( 'administrator' != $key) {
								?>
							<li class="par_cat">
								
								<input type="checkbox" class="parent" name="afreg_user_roles[]" id="afreg_user_roles" value="<?php echo esc_attr( $key ); ?>"
								<?php
								if ( !empty(get_option( 'afreg_user_roles'))) {
									if ( in_array( $key, get_option( 'afreg_user_roles') )) {
										echo 'checked';
									}
								}
								?>
								/>
								<?php echo esc_attr($value); ?>

							</li>
								<?php
							} 
						}
					}
					?>
				</ul>
			</div>

			<p class="description afreg_enable_user_role"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_user_roles_callback


		//Tab 3

		public function afreg_page_3_section_callback() { 
			?>

		   <p><?php echo esc_html__('Manage Approve new user settings from here.', 'addify_reg'); ?></p>
		   <h3><?php echo esc_html__('Approve New User Settings', 'addify_reg'); ?></h3>

			<?php 
		} // function afreg_page_3_section_callback


		public function afreg_enable_approve_user_callback( $args) {  
			?>
			<input type="checkbox" id="afreg_enable_approve_user" class="setting_fields" name="afreg_enable_approve_user" value="yes" <?php checked('yes', esc_attr( get_option('afreg_enable_approve_user'))); ?> >
			<p class="description afreg_enable_approve_user"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_enable_approve_user_callback

		public function afreg_enable_approve_user_checkout_callback( $args) {  
			?>
			<input type="checkbox" id="afreg_enable_approve_user_checkout" class="setting_fields" name="afreg_enable_approve_user_checkout" value="yes" <?php checked('yes', esc_attr( get_option('afreg_enable_approve_user_checkout'))); ?> >
			<p class="description afreg_enable_approve_user"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_enable_approve_user_callback

		

		public function afreg_exclude_user_roles_approve_new_user_callback( $args) {  
			?>
			
			<div class="all_cats">
				<ul>
					<?php

					global $wp_roles;
					$roles = $wp_roles->get_names();

					if ( !empty( $roles)) {

						foreach ($roles as $key => $value) {
							if ( 'administrator' != $key) {
								?>
							<li class="par_cat">
								
								<input type="checkbox" class="parent" name="afreg_exclude_user_roles_approve_new_user[]" id="afreg_exclude_user_roles_approve_new_user" value="<?php echo esc_attr( $key ); ?>"
								<?php
								if ( !empty(get_option( 'afreg_exclude_user_roles_approve_new_user'))) {
									if ( in_array( $key, get_option( 'afreg_exclude_user_roles_approve_new_user') )) {
										echo 'checked';
									}
								}
								?>
								/>
								<?php echo esc_attr($value); ?>

							</li>
								<?php
							} 
						}
					}
					?>
				</ul>
			</div>

			<p class="description afreg_exclude_user_roles_approve_new_user"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_user_roles_callback

		public function afreg_page_33_section_callback() { 
			?>

		   <h3><?php echo esc_html__('Approve New User Messages Settings', 'addify_reg'); ?></h3>

			<?php 
		} // function afreg_page_33_section_callback


		public function afreg_user_pending_approval_message_callback( $args) {  
			?>
			<textarea name="afreg_user_pending_approval_message" id="afreg_user_pending_approval_message" rows="10" cols="70"><?php echo esc_textarea( get_option( 'afreg_user_pending_approval_message' ) ); ?></textarea>
			<p class="description afreg_user_pending_approval_message"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_user_pending_approval_message_callback

		public function afreg_user_approval_message_callback( $args) {  
			?>
			<textarea name="afreg_user_approval_message" id="afreg_user_approval_message" rows="10" cols="70"><?php echo esc_textarea( get_option( 'afreg_user_approval_message' ) ); ?></textarea>
			<p class="description afreg_user_approval_message"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_user_approval_message_callback

		public function afreg_user_disapproved_message_callback( $args) {  
			?>
			<textarea name="afreg_user_disapproved_message" id="afreg_user_disapproved_message" rows="10" cols="70"><?php echo esc_textarea( get_option( 'afreg_user_disapproved_message' ) ); ?></textarea>
			<p class="description afreg_user_disapproved_message"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_user_disapproved_message_callback


		//Tab 4
		public function afreg_page_4_section_callback() { 
			?>

		   <h3><?php echo esc_html__('Manage Email Settings', 'addify_reg'); ?></h3>

			<?php 
		} // function afreg_page_4_section_callback


		public function afreg_admin_email_callback( $args) {  
			?>
			<input type="text" id="afreg_admin_email" class="setting_fields" name="afreg_admin_email" value="<?php echo esc_attr(get_option('afreg_admin_email')); ?>">
			<p class="description afreg_admin_email"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_admin_email_callback



		public function afreg_enable_admin_email_callback( $args) {  
			?>
			<input type="checkbox" id="afreg_enable_admin_email" class="setting_fields" name="afreg_enable_admin_email" value="yes" <?php echo checked('yes', esc_attr( get_option('afreg_enable_admin_email'))); ?> >
			<p class="description afreg_enable_admin_email"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_enable_admin_emaill_callback

		

		public function afreg_admin_email_subject_callback( $args) {  
			?>
			<input type="text" id="afreg_admin_email_subject" class="setting_fields" name="afreg_admin_email_subject" value="<?php echo esc_attr(get_option('afreg_admin_email_subject')); ?>">
			<p class="description afreg_admin_email_subject"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_admin_email_subject_callback


		public function afreg_admin_email_text_callback( $args) {  
			?>
			
			<?php

			$content   = get_option('afreg_admin_email_text');
			$editor_id = 'afreg_admin_email_text';
			$settings  = array(
				'wpautop' => false,
				'tinymce' => true,
				'textarea_rows' => 10,
				'quicktags' => array('buttons' => 'em,strong,link',),
				'quicktags' => true,
				'tinymce' => true,
			);

			wp_editor( $content, $editor_id, $settings );

			?>
			<p class="description afreg_admin_email_text"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_admin_email_text_callback



		public function afreg_enable_pending_user_email_callback( $args) {  
			?>
			<input type="checkbox" id="afreg_enable_pending_user_email" class="setting_fields" name="afreg_enable_pending_user_email" value="yes" <?php echo checked('yes', esc_attr( get_option('afreg_enable_pending_user_email'))); ?> >
			<p class="description afreg_enable_pending_user_email"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_enable_admin_emaill_callback 


		public function afreg_pending_approval_email_subject_callback( $args) {  
			?>
			<input type="text" id="afreg_pending_approval_email_subject" class="setting_fields" name="afreg_pending_approval_email_subject" value="<?php echo esc_attr(get_option('afreg_pending_approval_email_subject')); ?>">
			<p class="description afreg_pending_approval_email_subject"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_pending_approval_email_subject_callback

		public function afreg_pending_approval_email_text_callback( $args) {  
			?>
			
			<?php

			$content   = get_option('afreg_pending_approval_email_text');
			$editor_id = 'afreg_pending_approval_email_text';
			$settings  = array(
				'wpautop' => false,
				'tinymce' => true,
				'textarea_rows' => 10,
				'quicktags' => array('buttons' => 'em,strong,link',),
				'quicktags' => true,
				'tinymce' => true,
			);

			wp_editor( $content, $editor_id, $settings );

			?>
			<p class="description afreg_pending_approval_email_text"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_pending_approval_email_text_callback


		public function afreg_approved_email_subject_callback( $args) {  
			?>
			<input type="text" id="afreg_approved_email_subject" class="setting_fields" name="afreg_approved_email_subject" value="<?php echo esc_attr(get_option('afreg_approved_email_subject')); ?>">
			<p class="description afreg_approved_email_subject"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_approved_email_subject_callback

		public function afreg_approved_email_text_callback( $args) {  
			?>
			
			<?php

			$content   = get_option('afreg_approved_email_text');
			$editor_id = 'afreg_approved_email_text';
			$settings  = array(
				'wpautop' => false,
				'tinymce' => true,
				'textarea_rows' => 10,
				'quicktags' => array('buttons' => 'em,strong,link',),
				'quicktags' => true,
				'tinymce' => true,
			);

			wp_editor( $content, $editor_id, $settings );

			?>
			<p class="description afreg_approved_email_text"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_approved_email_text_callback



		public function afreg_disapproved_email_subject_callback( $args) {  
			?>
			<input type="text" id="afreg_disapproved_email_subject" class="setting_fields" name="afreg_disapproved_email_subject" value="<?php echo esc_attr(get_option('afreg_disapproved_email_subject')); ?>">
			<p class="description afreg_disapproved_email_subject"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_disapproved_email_subject_callback

		public function afreg_disapproved_email_text_callback( $args) {  
			?>
			
			<?php

			$content   = get_option('afreg_disapproved_email_text');
			$editor_id = 'afreg_disapproved_email_text';
			$settings  = array(
				'wpautop' => false,
				'tinymce' => true,
				'textarea_rows' => 10,
				'quicktags' => array('buttons' => 'em,strong,link',),
				'quicktags' => true,
				'tinymce' => true,
			);

			wp_editor( $content, $editor_id, $settings );

			?>
			<p class="description afreg_disapproved_email_text"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
		} // end afreg_disapproved_email_text_callback









		public function afreg_profile_fields() {

			wp_nonce_field( 'afreg_nonce_action', 'afreg_nonce_field' );
			?>

				<h3><?php echo esc_html__(get_option('afreg_additional_fields_section_title'), 'addify_reg'); ?></h3>
				<div class="afreg_extra_fields">
				<table class="form-table">
					<?php 

						$afreg_args         = array( 
							'posts_per_page' => -1,
							'post_type' => 'afreg_fields',
							'post_status' => 'publish',
							'orderby' => 'menu_order',
							'order' => 'ASC'
						);
						$afreg_extra_fields = get_posts($afreg_args);
						if (!empty($afreg_extra_fields)) {

							foreach ($afreg_extra_fields as $afreg_field) {

								$afreg_field_type        = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
								$afreg_field_options     = unserialize(get_post_meta( intval($afreg_field->ID), 'afreg_field_option', true )); 
								$afreg_field_placeholder = get_post_meta( intval($afreg_field->ID), 'afreg_field_placeholder', true );
								$afreg_field_description = get_post_meta( intval($afreg_field->ID), 'afreg_field_description', true );

								if ( isset( $_GET['user_id'])) {

									$value = get_user_meta( intval($_GET['user_id']), 'afreg_additional_' . intval($afreg_field->ID), true );	
								} else {
									$value = '';
								}

								if (!empty(get_post_meta( intval($afreg_field->ID), 'afreg_is_dependable', true ))) {

									$afreg_is_dependable = get_post_meta( intval($afreg_field->ID), 'afreg_is_dependable', true );
								} else {
									$afreg_is_dependable = 'off';
								}

								$afreg_field_user_roles = get_post_meta( $afreg_field->ID, 'afreg_field_user_roles', true );
								$field_roles            = unserialize($afreg_field_user_roles);
								

								if ('text' == $afreg_field_type) { 
									?>
									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="text" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>
								<?php } elseif ( 'textarea' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<textarea class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php echo esc_attr($value); ?></textarea>
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'email' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="email" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'select' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<select class="input-select " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
												<?php foreach ($afreg_field_options as $afreg_field_option) { ?>
													<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php echo selected(esc_attr($value), esc_attr($afreg_field_option['field_value'])); ?>>
														<?php 
														if (!empty($afreg_field_option['field_text'])) {
															echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg');} 
														?>
													</option>
												<?php } ?>
											</select>
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'multiselect' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<select class="input-select " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>[]" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" multiple>
												<?php 
												foreach ($afreg_field_options as $afreg_field_option) {

													$db_values = explode(', ', $value);

													if (!empty($db_values)) { 
														?>
														<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" 
																				  <?php 
																					if (in_array(esc_attr($afreg_field_option['field_value']), $db_values)) {
																						echo 'selected';} 
																					?>
														>
															<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
													<?php } else { ?>
													<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>">
														<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
													</option>
												<?php } } ?>
											</select>
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'multi_checkbox' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<?php 
											foreach ($afreg_field_options as $afreg_field_option) {
												$db_values = explode(', ', $value);
												?>
												<input type="checkbox" class="input-checkbox " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>[]" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>"
												<?php
												if (in_array(esc_attr($afreg_field_option['field_value']), $db_values)) {
													echo 'checked';
												}
												?>
												 />
												<span class="afreg_radio">
												<?php 
												if (!empty($afreg_field_option['field_text'])) {
													echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg');} 
												?>
												</span>
											<?php } ?>
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'checkbox' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="checkbox" class="input-checkbox " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="yes" <?php echo checked('yes', esc_attr($value)); ?>  />
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'radio' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<?php foreach ($afreg_field_options as $afreg_field_option) { ?>
												<input type="radio" class="input-radio " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php echo checked(esc_attr($value), esc_attr($afreg_field_option['field_value'])); ?>  />
												<span class="afreg_radio">
												<?php 
												if (!empty($afreg_field_option['field_text'])) {
													echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg');} 
												?>
												</span>
											<?php } ?>
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'number' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="number" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'password' == $afreg_field_type) { ?>

									   <tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="password" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'fileupload' == $afreg_field_type) { ?>

									   <tr class="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										   <th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php echo esc_html__('Current', 'addify_reg'); ?> <?php 
											if (!empty($afreg_field->post_title)) {
												echo esc_html__($afreg_field->post_title , 'addify_reg' );
											} 
											?>
											</label></th>

										   <td>
											<?php 
					
											$curr_image = AFREG_URL . 'uploaded_files/' . $value;
											if (!empty($value)) {
												$ext = pathinfo($curr_image, PATHINFO_EXTENSION);
												if ( 'pdf' == $ext || 'PDF' == $ext) { 
													?>
												<a href="<?php echo esc_url(AFREG_URL); ?>uploaded_files/<?php echo esc_attr($value); ?>" target="_blank">
													<img src="<?php echo esc_url(AFREG_URL); ?>images/pdf.png" width="150" height="150" title="Click to View" />
												</a>
												<?php } else { ?>
												<img src="<?php echo esc_url(AFREG_URL); ?>uploaded_files/<?php echo esc_attr($value); ?>" width="150" height="150" />
											<?php } } ?>
										   </td>


									   </tr>

									   <tr class="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="file" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="" placeholder="
																													 <?php 
																														if (!empty($afreg_field_placeholder)) {
																															echo esc_html__($afreg_field_placeholder , 'addify_reg' );} 
																														?>
											" />
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'color' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="color" class="input-text color_sepctrumm" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="
																																	 <?php 
																																		if (!empty($afreg_field_placeholder)) {
																																			echo esc_html__($afreg_field_placeholder , 'addify_reg' );} 
																																		?>
											" />
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>

											<script>
						
											jQuery(".color_sepctrumm").spectrum({
												color: "<?php echo esc_attr($value); ?>",
												preferredFormat: "hex",
											});

											</script>
										</td>
									</tr>

								<?php } elseif ( 'datepicker' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="date" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="
																													 <?php 
																														if (!empty($afreg_field_placeholder)) {
																															echo esc_html__($afreg_field_placeholder , 'addify_reg' );} 
																														?>
											" />
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

								<?php } elseif ( 'timepicker' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_reg' );} 
																					?>
										</label></th>
										<td>
											<input type="time" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="
																													 <?php 
																														if (!empty($afreg_field_placeholder)) {
																															echo esc_html__($afreg_field_placeholder , 'addify_reg' );} 
																														?>
											" />
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
									</tr>

									<?php 
								}
								?>


								<!-- Dependable -->
								<?php if ('on' == $afreg_is_dependable && !empty($field_roles)) { ?>

									<style>
										#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?> { display: none; }
										.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?> { display: none; }
									</style>

								<?php } ?>

								<script>

									jQuery(document).ready(function() {

										var val = jQuery('#role option:selected').val();
										var field_roles = new Array();
										var is_dependable = '<?php echo esc_attr($afreg_is_dependable); ?>';
											
											<?php if ( !empty($field_roles)) { ?>
												<?php foreach ($field_roles as $key => $value) { ?>

													field_roles.push('<?php echo esc_attr($value); ?>');

												<?php } ?>

												var match_val = field_roles.includes(val);

												if (match_val == true && is_dependable == 'on') {


													jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();
													jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();

												} else if (match_val == false && is_dependable == 'on') {

													jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').hide();
													jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').hide();
												} else {

													jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();
													jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();

												}

											<?php } ?>

									});
									
									jQuery(document).on('change', '#role', function() {

										var val = this.value;
										var field_roles = new Array();
										var is_dependable = '<?php echo esc_attr($afreg_is_dependable); ?>';
											
											<?php if ( !empty($field_roles)) { ?>
												<?php foreach ($field_roles as $key => $value) { ?>

													field_roles.push('<?php echo esc_attr($value); ?>');

												<?php } ?>

												var match_val = field_roles.includes(val);

												if (match_val == true && is_dependable == 'on') {


													jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();
													jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();


												} else if (match_val == false && is_dependable == 'on') {

													jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').hide();
													jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').hide();

												} else {

													jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();
													jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();


												}

											<?php } ?>


									});

								</script>

								<?php 
							}
						}

						?>
					
				</table>
			</div>
			<?php 
		}

		public function afreg_update_profile_fields( $customer_id) {

			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type' => 'afreg_fields',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);

			$afreg_extra_fields = get_posts($afreg_args);

			if (!empty($afreg_extra_fields)) {

				if (!empty($_REQUEST['afreg_nonce_field'])) {

					$retrieved_nonce = sanitize_text_field($_REQUEST['afreg_nonce_field']);
				} else {
						$retrieved_nonce = 0;
				}

				if (!wp_verify_nonce($retrieved_nonce, 'afreg_nonce_action')) {

					die('Failed security check');
				}

				foreach ($afreg_extra_fields as $afreg_field) {

					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );

					if ( isset( $_POST['afreg_additional_' . intval($afreg_field->ID)] ) || isset( $_FILES['afreg_additional_' . intval($afreg_field->ID)] ) ) {

						if ( 'fileupload' == $afreg_field_type) {

							if (isset($_FILES['afreg_additional_' . intval($afreg_field->ID)]['name']) && '' != $_FILES['afreg_additional_' . intval($afreg_field->ID)]['name']) { 

								if ( isset( $_FILES['afreg_additional_' . intval($afreg_field->ID)]['name'])) {
									$file = time('m') . sanitize_text_field($_FILES['afreg_additional_' . intval($afreg_field->ID)]['name']);
								} else {
									$file = '';
								}
								
								$target_path = AFREG_PLUGIN_DIR . 'uploaded_files/';
								$target_path = $target_path . $file;
								if ( isset( $_FILES['afreg_additional_' . intval($afreg_field->ID)]['tmp_name'])) {
									$temp = move_uploaded_file(sanitize_text_field($_FILES['afreg_additional_' . intval($afreg_field->ID)]['tmp_name']), $target_path);
								} else {
									$temp = '';
								}
								
								update_user_meta($customer_id, 'afreg_additional_' . intval($afreg_field->ID), $file);

							}

						} elseif ( 'multiselect' == $afreg_field_type) { 
							$prefix   = '';
							$multival = '';
							foreach (sanitize_meta('', $_POST['afreg_additional_' . intval($afreg_field->ID)], '') as $value) {
								$multival .= $prefix . $value;
								$prefix    = ', ';
							}
							update_user_meta( $customer_id, 'afreg_additional_' . intval($afreg_field->ID), sanitize_text_field($multival) );

						} elseif ( 'multi_checkbox' == $afreg_field_type) { 
							$prefix   = '';
							$multival = '';
							foreach (sanitize_meta('', $_POST['afreg_additional_' . intval($afreg_field->ID)], '') as $value) {
								$multival .= $prefix . $value;
								$prefix    = ', ';
							}
							update_user_meta( $customer_id, 'afreg_additional_' . intval($afreg_field->ID), sanitize_text_field($multival) );

						} else {

							update_user_meta( $customer_id, 'afreg_additional_' . intval($afreg_field->ID), sanitize_text_field($_POST['afreg_additional_' . intval($afreg_field->ID)]));
						}

					} else {

						update_user_meta( $customer_id, 'afreg_additional_' . intval($afreg_field->ID), '');
					}
				}

			}
		}

		public function afreg_modify_user_table( $column ) {


			$column['user_status'] = esc_html__( 'User Status', 'addify_reg' );
			return $column;
		}

		public function afreg_modify_user_table_row( $val, $column_name, $user_id ) {
			switch ($column_name) {
				case 'user_status':
					$user_status = get_user_meta($user_id, 'afreg_new_user_status', true);
					return ucfirst($user_status);
				default:
			}
			return $val;
		}

		public function afreg_user_row_actions( $actions, $user) {

			if ( get_current_user_id() == $user->ID ) {
				return $actions;
			}

			if ( is_super_admin( $user->ID ) ) {
				return $actions;
			}

			$user_status = get_user_meta( $user->ID, 'afreg_new_user_status', true);

			$approve_link = add_query_arg( array( 'action' => 'approved', 'user' => $user->ID ) );
			$approve_link = remove_query_arg( array( 'new_role' ), $approve_link );
			$approve_link = wp_nonce_url( $approve_link, 'addify-afreg-fields' );

			$deny_link = add_query_arg( array( 'action' => 'disapproved', 'user' => $user->ID ) );
			$deny_link = remove_query_arg( array( 'new_role' ), $deny_link );
			$deny_link = wp_nonce_url( $deny_link, 'addify-afreg-fields' );

			$approve_action = '<a href="' . esc_url( $approve_link ) . '">' . esc_html__( 'Approve', 'addify_reg' ) . '</a>';
			$deny_action    = '<a href="' . esc_url( $deny_link ) . '">' . esc_html__( 'Disapprove', 'addify_reg' ) . '</a>';

			if ( 'pending' == $user_status ) {
				$actions[] = $approve_action;
				$actions[] = $deny_action;
			} elseif ( 'approved' == $user_status ) {
				$actions[] = $deny_action;
			} elseif ( 'disapproved' == $user_status ) {
				$actions[] = $approve_action;
			}

			return $actions;

		}

		public function afreg_update_action() {

			//Email link approval
			if ( isset( $_GET['action_email'] ) && in_array( $_GET['action_email'], array( 'approved', 'disapproved' ) ) && !isset( $_GET['new_role'] ) ) {

				$sendback = remove_query_arg( array( 'approved', 'disapproved', 'deleted', 'ids', 'afreg-status-query-submit', 'new_role' ), wp_get_referer() );
				if ( !$sendback ) {
					$sendback = admin_url( 'users.php' );
				}

				$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
				$pagenum       = $wp_list_table->get_pagenum();
				$sendback      = add_query_arg( 'paged', $pagenum, $sendback );

				$status = sanitize_key( $_GET['action_email'] );

				if ( isset( $_GET['user'])) {
					$user = absint( $_GET['user'] );
				} else {
					$user = 0;
				}
				

				update_user_meta( $user, 'afreg_new_user_status', $status);


				$from_name  = get_option('woocommerce_email_from_name');
				$from_email = get_option('woocommerce_email_from_address');
				$users      = new WP_User($user);
				$user_login = stripslashes($users->user_login);
				$user_email = stripslashes($users->user_email);

				// More headers
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type:text/html' . "\n";
				$headers .= 'From: ' . $from_name . ' < ' . $from_email . ' > ' . "\r\n";

				if ( 'approved' == $_GET['action_email'] ) {

					//Send Message to user that their account is approved.  
					
					$subject = get_option('afreg_approved_email_subject');

					$afreg_approved_email_text = __(get_option('afreg_approved_email_text'), 'addify_reg');


					$messagee = '<p>' . wp_kses_post($afreg_approved_email_text) . '</p>';

					$message = $this->afreg_email_template('Welcome to ' . get_option('blogname'), $messagee);

					wp_mail( $user_email, esc_html__($subject, 'addify_reg'), $message, $headers );


					$sendback = add_query_arg( array( 'approved' => 1, 'ids' => $user ), $sendback );


					?>
					<script>
						window.location = '<?php echo esc_url($sendback); ?>';
					</script>
					<?php

				} elseif ('disapproved' == $_GET['action_email']) {

					//Send Message to user that their account is disapproved.  
					
					$subject = get_option('afreg_disapproved_email_subject');

					$afreg_disapproved_email_text = __(get_option('afreg_disapproved_email_text'), 'addify_reg');

					$messagee = '<p>' . wp_kses_post($afreg_disapproved_email_text) . '</p>';

					$message = $this->afreg_email_template('Welcome to ' . get_option('blogname'), $messagee);
					

					wp_mail( $user_email, esc_html__($subject, 'addify_reg'), $message, $headers );


					$sendback = add_query_arg( array( 'approved' => 1, 'ids' => $user ), $sendback );


					?>
					<script>
						window.location = '<?php echo esc_url($sendback); ?>';
					</script>
					<?php

				} 


			}


			if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'approved', 'disapproved' ) ) && !isset( $_GET['new_role'] ) ) {
				check_admin_referer( 'addify-afreg-fields' );

				$sendback = remove_query_arg( array( 'approved', 'disapproved', 'deleted', 'ids', 'afreg-status-query-submit', 'new_role' ), wp_get_referer() );
				if ( !$sendback ) {
					$sendback = admin_url( 'users.php' );
				}

				$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
				$pagenum       = $wp_list_table->get_pagenum();
				$sendback      = add_query_arg( 'paged', $pagenum, $sendback );

				$status = sanitize_key( $_GET['action'] );

				if ( isset( $_GET['user'])) {
					$user = absint( $_GET['user'] );
				} else {
					$user = 0;
				}
				

				update_user_meta( $user, 'afreg_new_user_status', $status);


				$from_name  = get_option('woocommerce_email_from_name');
				$from_email = get_option('woocommerce_email_from_address');
				$users      = new WP_User($user);
				$user_login = stripslashes($users->user_login);
				$user_email = stripslashes($users->user_email);

				// More headers
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type:text/html' . "\n";
				$headers .= 'From: ' . $from_name . ' < ' . $from_email . ' > ' . "\r\n";

				if ( 'approved' == $_GET['action'] ) {

					//Send Message to user that their account is approved.  
					
					$subject = get_option('afreg_approved_email_subject');

					$afreg_approved_email_text = __(get_option('afreg_approved_email_text'), 'addify_reg');


					$messagee = '<p>' . wp_kses_post($afreg_approved_email_text) . '</p>';

					$message = $this->afreg_email_template('Welcome to ' . get_option('blogname'), $messagee);

					wp_mail( $user_email, esc_html__($subject, 'addify_reg'), $message, $headers );


					$sendback = add_query_arg( array( 'approved' => 1, 'ids' => $user ), $sendback );


					

				} elseif ( 'disapproved' == $_GET['action'] ) {



					//Send Message to user that their account is disapproved.  
					$subject = get_option('afreg_disapproved_email_subject');

					$afreg_disapproved_email_text = __(get_option('afreg_disapproved_email_text'), 'addify_reg');

					$messagee = '<p>' . wp_kses_post($afreg_disapproved_email_text) . '</p>';

					$message = $this->afreg_email_template('Welcome to ' . get_option('blogname'), $messagee);
					

					wp_mail( $user_email, esc_html__($subject, 'addify_reg'), $message, $headers );

					

					$sendback = add_query_arg( array( 'disapproved' => 1, 'ids' => $user ), $sendback );
					
				}

				wp_redirect( $sendback );
				exit;

				
			}
		}

		public function afreg_status_filter( $s_filter) {


			$id = 'afreg_approve_new_user_filter-' . $s_filter;

			$f_button = submit_button( esc_html__( 'Filter', 'addify_reg' ), 'button', 'afreg-status-query-submit', false, array( 'id' => 'afreg-status-query-submit' ) );
			$f_status = $this->changed_status();

			?>
			<label class="screen-reader-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html__( 'View all users', 'addify_reg' ); ?></label>
			<select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" class="anusec">
				<option value=""><?php echo esc_html__( 'View all users', 'addify_reg' ); ?></option>
			<?php foreach ( $this->get_all_statuses() as $status ) { ?>
				<option value="<?php echo esc_attr( $status ); ?>"<?php echo selected( $status, $f_status ); ?>>
					
					<?php

					if ( 'disapproved' == $status) {
						echo esc_html__('Disapproved', 'addify_reg');
					} else {
						echo esc_html__( ucfirst($status) );
					}
					

					?>
						
					</option>
			<?php } ?>
			</select>
			<?php echo esc_attr(apply_filters( 'afreg_approve_new_user_filter_button', $f_button )); ?>
			
			<?php


		}

		public function changed_status() {
			if ( ! empty( $_REQUEST['afreg_approve_new_user_filter-top'] ) || ! empty( $_REQUEST['afreg_approve_new_user_filter-bottom'] ) ) {
				$aa =  esc_attr( ( ! empty( $_REQUEST['afreg_approve_new_user_filter-top'] ) ) ? sanitize_text_field($_REQUEST['afreg_approve_new_user_filter-top']) : sanitize_text_field($_REQUEST['afreg_approve_new_user_filter-bottom'] ));
			} else {
				$aa =  null;
			}
			return $aa;

			
		}

		public function get_all_statuses() {
			return array( 'pending', 'approved', 'disapproved' );
		}

		public function afreg_filter_user_by_status( $qry) {

			global $wpdb;

			if ( !is_admin() ) {
				return;
			}

			
			if ( $this->changed_status() != null ) { 
				$filter = $this->changed_status();

				$qry->query_from .= " INNER JOIN {$wpdb->usermeta} ON ( {$wpdb->users}.ID = $wpdb->usermeta.user_id )";

				if ( 'approved' == $filter ) {
					$qry->query_fields = "DISTINCT SQL_CALC_FOUND_ROWS {$wpdb->users}.ID";
					$qry->query_from  .= " LEFT JOIN {$wpdb->usermeta} AS mt1 ON ({$wpdb->users}.ID = mt1.user_id AND mt1.meta_key = 'afreg_new_user_status')";
					$qry->query_where .= " AND ( ( $wpdb->usermeta.meta_key = 'afreg_new_user_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = 'approved' ) OR mt1.user_id IS NULL )";
				} else {
					$qry->query_where .= " AND ( ($wpdb->usermeta.meta_key = 'afreg_new_user_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = '{$filter}') )";
				}



			}
		}

		public function afreg_admin_footer() {
			$screen = get_current_screen();

			if ( 'users' == $screen->id ) { 
				?>
				<script type="text/javascript">
					jQuery(document).ready(function ($) {
						$('<option>').val('approved').text('<?php echo esc_html__( 'Approve', 'addify_reg' ); ?>').appendTo("select[name='action']");
						$('<option>').val('approved').text('<?php echo esc_html__( 'Approve', 'addify_reg' ); ?>').appendTo("select[name='action2']");

						$('<option>').val('disapproved').text('<?php echo esc_html__( 'Disapprove', 'addify_reg' ); ?>').appendTo("select[name='action']");
						$('<option>').val('disapproved').text('<?php echo esc_html__( 'Disapprove', 'addify_reg' ); ?>').appendTo("select[name='action2']");
					});
				</script>
				<?php 
			}
		}

		public function afreg_bulk_action_user() {
			$screen = get_current_screen();

			if ( 'users' == $screen->id ) {

				// get the action
				$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
				$action        = $wp_list_table->current_action();


				$allowed_actions = array( 'approved', 'disapproved' );
				if ( !in_array( $action, $allowed_actions ) ) {
					return;
				}




				// security check
				check_admin_referer( 'bulk-users' );

				// make sure ids are submitted
				if ( isset( $_REQUEST['users'] ) ) {
					$user_ids = array_map( 'intval', $_REQUEST['users'] );
				}

				if ( empty( $user_ids ) ) {
					return;
				}

				$sendback = remove_query_arg( array( 'approved', 'disapproved', 'deleted', 'ids', 'afreg_approve_new_user_filter', 'afreg_approve_new_user_filter2', 'afreg-status-query-submit', 'new_role' ), wp_get_referer() );
				if ( !$sendback ) {
					$sendback = admin_url( 'users.php' );
				}

				$pagenum  = $wp_list_table->get_pagenum();
				$sendback = add_query_arg( 'paged', $pagenum, $sendback );

				$from_name  = get_option('woocommerce_email_from_name');
				$from_email = get_option('woocommerce_email_from_address');

				// More headers
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type:text/html' . "\n";
				$headers .= 'From: ' . $from_name . ' < ' . $from_email . ' > ' . "\r\n";

				switch ( $action ) {
					case 'approved':
						$approved = 0;
						foreach ( $user_ids as $user_id ) {


							//Send Message to user that their account is approved. 
							$users      = new WP_User( $user_id );
							$user_login = stripslashes( $users->data->user_login );
							$user_email = stripslashes( $users->data->user_email );
							

							$to      = $users->data->user_email;
							$subject = get_option('afreg_approved_email_subject');

							$afreg_approved_email_text = __(get_option('afreg_approved_email_text'), 'addify_reg');


							$messagee = '<p>' . wp_kses_post($afreg_approved_email_text) . '</p>';

							$message = $this->afreg_email_template('Welcome to ' . get_option('blogname'), $messagee);

							wp_mail( $to, esc_html__($subject, 'addify_reg'), $message, $headers );

							
							
							update_user_meta( $user_id, 'afreg_new_user_status', 'approved');
							$approved++;
						}

						$sendback = add_query_arg( array( 'approved' => $approved, 'ids' => join( ',', $user_ids ) ), $sendback );
						break;

					case 'disapproved':
						$disapproved = 0;
						foreach ( $user_ids as $user_id ) {


							//Send Message to user that their account is disapproved. 
							$users = new WP_User( $user_id );

							$user_email = stripslashes( $users->data->user_email );
							
							$to      = $users->data->user_email;
							$subject = get_option('afreg_disapproved_email_subject');

							$afreg_disapproved_email_text = __(get_option('afreg_disapproved_email_text'), 'addify_reg');

							$messagee = '<p>' . wp_kses_post($afreg_disapproved_email_text) . '</p>';

							$message = $this->afreg_email_template('Welcome to ' . get_option('blogname'), $messagee);
							

							wp_mail( $to, esc_html__($subject, 'addify_reg'), $message, $headers );

							
							
							update_user_meta( $user_id, 'afreg_new_user_status', 'disapproved');
							$disapproved++;
						}

						$sendback = add_query_arg( array( 'disapproved' => $disapproved, 'ids' => join( ',', $user_ids ) ), $sendback );
						break;

					default:
						return;
				}

				$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );

				wp_redirect( $sendback );
				exit();
			}
		}


		public function afreg_default_fields() {

			require  AFREG_PLUGIN_DIR . 'admin/afreg_def_admin.php';
		}

		public function afreg_save_df_form() {

			

			if (isset($_POST['nonce']) && '' != $_POST['nonce']) {

				$nonce = sanitize_text_field( $_POST['nonce'] );
			} else {
				$nonce = 0;
			}

			if ( ! wp_verify_nonce( $nonce, 'afreg-ajax-nonce' ) ) {

				die ( 'Failed ajax security check!');
			}


			if (isset($_POST['post_ids']) && '' != $_POST['post_ids']) {
				$post_ids = sanitize_meta('', $_POST['post_ids'], '');			
			} else {
				$post_ids = array(); }

			if (isset($_POST['field_label']) && '' != $_POST['field_label']) {
				$field_label = sanitize_meta('', $_POST['field_label'], '');			
			} else {
				$field_label = array(); }

			if (isset($_POST['field_placeholder']) && '' != $_POST['field_placeholder']) {
				$field_placeholder = sanitize_meta('', $_POST['field_placeholder'], '');			
			} else {
				$field_placeholder = array(); }

			if (isset($_POST['field_required']) && '' != $_POST['field_required']) {
				$field_required = sanitize_meta('', $_POST['field_required'], '');			
			} else {
				$field_required = array(); }
			
			if (isset($_POST['field_width']) && '' != $_POST['field_width']) {
				$field_width = sanitize_meta('', $_POST['field_width'], '');			
			} else {
				$field_width = array(); }

			if (isset($_POST['field_message']) && '' != $_POST['field_message']) {
				$field_message = sanitize_meta('', $_POST['field_message'], '');			
			} else {
				$field_message = array(); }

			if (isset($_POST['field_status']) && '' != $_POST['field_status']) {
				$field_status = sanitize_meta('', $_POST['field_status'], '');			
			} else {
				$field_status = array(); }

			if (isset($_POST['field_sort_order']) && '' != $_POST['field_sort_order']) {
				$field_sort_order = sanitize_meta('', $_POST['field_sort_order'], '');			
			} else {
				$field_sort_order = array(); }

			$full_array = array_map(function( $a, $b, $c, $d, $e, $f, $g, $h) { 
				return $a . '-:-' . $b . '-:-' . $c . '-:-' . $d . '-:-' . $e . '-:-' . $f . '-:-' . $g . '-:-' . $h; 
			}, $post_ids, $field_label, $field_placeholder, $field_required, $field_width, $field_message, $field_status, $field_sort_order);

			if ('' != $full_array) {
				foreach ($full_array as $data) {
					
					$value         = explode('-:-', $data);
					$p_id          = intval($value[0]);
					$f_label       = sanitize_text_field($value[1]);
					$f_placeholder = sanitize_text_field($value[2]);
					$f_required    = sanitize_text_field($value[3]);
					$f_width       = sanitize_text_field($value[4]);
					$f_message     = sanitize_text_field($value[5]);
					$f_status      = sanitize_text_field($value[6]);
					$f_sort_order  = sanitize_text_field($value[7]);

					

					  $af_post = array(
						  'ID'           => $p_id,
						  'post_title'   => $f_label,
						  'post_status'  => $f_status,
						  'menu_order'   => $f_sort_order
					  );

					  // Update the post and post meta into the database
					  wp_update_post( $af_post );

					  update_post_meta( $p_id, 'placeholder', $f_placeholder );
					  update_post_meta( $p_id, 'is_required', $f_required );
					  update_post_meta( $p_id, 'width', $f_width );
					  update_post_meta( $p_id, 'message', $f_message );

				}
			}

			echo 'success';

			die();
		}


		public function afreg_email_template( $heading, $message) {

			$af_footer_data = get_option('woocommerce_email_footer_text');
			$new_footer     = str_replace('{site_address}', get_option('home'), $af_footer_data);
			$new_footer     = str_replace('{site_title}', get_option('blogname'), $af_footer_data);

			$new_footer = str_replace('{WooCommerce}', '<a href="https://woocommerce.com" style=" font-weight: normal; text-decoration: underline;">WooCommerce</a>', $new_footer);

			
			$html = '

			<style>
				a { color: ' . esc_attr(get_option('woocommerce_email_base_color')) . ';}
				h2 { color: ' . esc_attr(get_option('woocommerce_email_base_color')) . ';}
			</style>

			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				</head>
				<body>
					<div id="wrapper" dir="ltr" style="background-color: ' . esc_attr(get_option('woocommerce_email_background_color')) . '; margin: 0; padding: 70px 0; width: 100%; -webkit-text-size-adjust: none;">
						<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr>
									<td valign="top" align="center">
										<div id="template_header_image">
											<p style="margin-top: 0;"><img src="' . esc_url(get_option('woocommerce_email_header_image')) . '" alt="" style="border: none; display: inline-block; font-size: 14px; font-weight: bold; height: auto; outline: none; text-decoration: none; text-transform: capitalize; vertical-align: middle; max-width: 100%; margin-left: 0; margin-right: 0;"></p>
										</div>
										<table id="template_container" style="background-color: ' . esc_attr(get_option('woocommerce_email_body_background_color')) . '; border: 0px solid #cd3333; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); border-radius: 3px;" width="600" cellspacing="0" cellpadding="0" border="0">
											<tbody>
												<tr>
													<td valign="top" align="center">
													<!-- Header -->
														<table id="template_header" style="background-color: ' . esc_attr(get_option('woocommerce_email_base_color')) . '; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; border-radius: 3px 3px 0 0;" width="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
																<tr>
																	<td id="header_wrapper" style="padding: 36px 48px; display: block;">
																		<h1 style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #6a7d3a; color: #ffffff;">' . esc_html__($heading, 'addify_reg') . '</h1>
																	</td>
																</tr>
															</tbody>
														</table>
													<!-- End Header -->
													</td>
												</tr>
												<tr>
													<td valign="top" align="center">
													<!-- Body -->
														<table id="template_body" width="600" cellspacing="0" cellpadding="0" border="0">
															<tbody>
																<tr>
																	<td id="body_content" style="background-color: ' . esc_attr(get_option('woocommerce_email_body_background_color')) . ';" valign="top">
																	<!-- Content -->
																		<table width="100%" cellspacing="0" cellpadding="20" border="0">
																			<tbody>
																				<tr>
																					<td style="padding: 48px 48px 32px;" valign="top">
																						<div id="body_content_inner" style="color: ' . esc_attr(get_option('woocommerce_email_text_color')) . '; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;">
																							<p style="margin: 0 0 16px;">' . $message . '</p>
																							
																						</div>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	<!-- End Content -->
																	</td>
																</tr>
															</tbody>
														</table>
													<!-- End Body -->
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td valign="top" align="center">
									<!-- Footer -->
										<table id="template_footer" width="600" cellspacing="0" cellpadding="10" border="0">
											<tbody>
												<tr>
													<td style="padding: 0; border-radius: 6px;" valign="top">
														<table width="100%" cellspacing="0" cellpadding="10" border="0">
															<tbody>
																<tr>
																	<td colspan="2" id="credit" style="border-radius: 6px; border: 0; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 12px; line-height: 150%; text-align: center; padding: 24px 0;" valign="middle">
																		<p style="margin: 0 0 16px;">' . $new_footer . '</p>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									<!-- End Footer -->
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</body>
			</html>';

			return $html;



		}


	}

	new Addify_Registration_Fields_Addon_Admin();
}
