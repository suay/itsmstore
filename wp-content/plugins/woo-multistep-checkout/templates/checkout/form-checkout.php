<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

?>

<?php 
	$thwmscf_settings = get_option('THWMSC_SETTINGS');

	$enable_login_step = isset($thwmscf_settings['enable_login_step']) ? $thwmscf_settings['enable_login_step'] : true;
	$thwmscf_tab_align = !empty($thwmscf_settings['tab_align']) ? $thwmscf_settings['tab_align'] : '';
	$thwmscf_title_login = !empty($thwmscf_settings['title_login']) ? wptexturize($thwmscf_settings['title_login']) : "Login";
	$thwmscf_title_billing = !empty($thwmscf_settings['title_billing']) ? wptexturize($thwmscf_settings['title_billing']) : "Billing";
	$thwmscf_title_shipping = !empty($thwmscf_settings['title_shipping']) ? wptexturize($thwmscf_settings['title_shipping']) : "Shipping";
	$thwmscf_title_order_review = !empty($thwmscf_settings['title_order_review']) ? wptexturize($thwmscf_settings['title_order_review']) : "Your order";

	$button_prev_text = !empty($thwmscf_settings['button_prev_text']) ? wptexturize($thwmscf_settings['button_prev_text']) : "Previous";
	$button_next_text = !empty($thwmscf_settings['button_next_text']) ? wptexturize($thwmscf_settings['button_next_text']) : "Next";

	$thwmscf_layout = isset($thwmscf_settings['thwmscf_layout']) && $thwmscf_settings['thwmscf_layout'] ? wptexturize($thwmscf_settings['thwmscf_layout']) : 'thwmscf_horizontal_box';

	$back_to_cart_button = isset($thwmscf_settings['back_to_cart_button']) && $thwmscf_settings['back_to_cart_button'] ? wptexturize($thwmscf_settings['back_to_cart_button']) : '';

	$back_to_cart_button_text = isset($thwmscf_settings['back_to_cart_button_text']) && $thwmscf_settings['back_to_cart_button_text'] ? wptexturize($thwmscf_settings['back_to_cart_button_text']) : 'Back to cart';

	$billing_shipping_together = isset($thwmscf_settings['make_billing_shipping_together']) ? $thwmscf_settings['make_billing_shipping_together'] : false;

	if(empty($back_to_cart_button_text)){
		$back_to_cart_button_text = 'Back to cart';
	}
?>

<div id="thwmscf_wrapper" class="thwmscf-wrapper <?php echo esc_attr($thwmscf_layout); ?>">  
	<ul id="thwmscf-tabs" class="thwmscf-tabs <?php echo esc_attr($thwmscf_tab_align); ?>">	
		<?php 
		$step_number = 1;
		$step1_class = 'first active';
		$enable_login_reminder = false;	
		if($enable_login_step && !is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder')){
			$enable_login_reminder = true;
			$step1_class = '';	
		?>
			<li class="thwmscf-tab">
            	<a href="javascript:void(0)" id="step-0" data-step="0" class="first active">
            		<?php if($thwmscf_layout == 'thwmscf_time_line_step') { ?>
            			<span class="thwmscf-tab-label">
	            			<span class="thwmscf-index thwmscf-tab-icon">0</span>
	            			<?php echo wp_kses_post(__($thwmscf_title_login, 'woo-multistep-checkout')); ?>
	            		</span>
            		<?php } else {
            			echo wp_kses_post(__($thwmscf_title_login, 'woo-multistep-checkout'));
            		} ?>
            	</a>
            </li>
		<?php } ?>	
			
		<li class="thwmscf-tab">
        	<a href="javascript:void(0)" id="step-<?php echo $step_number; ?>" data-step="<?php echo $step_number; ?>" class="<?php echo $step1_class; ?>">
        		<?php if($thwmscf_layout == 'thwmscf_time_line_step') { ?>
        			<span class="thwmscf-tab-label">
    					<span class="thwmscf-index thwmscf-tab-icon"><?php echo $step_number; ?></span>
    					<?php echo wp_kses_post(__($thwmscf_title_billing, 'woo-multistep-checkout')); ?>
    				</span>
    			<?php } else { 
    				echo wp_kses_post(__($thwmscf_title_billing, 'woo-multistep-checkout')); 
    			} ?>
        	</a>
        </li>
        <?php $step_number++; ?>

        <?php if(!$billing_shipping_together) { ?>
			<li class="thwmscf-tab">
	        	<a href="javascript:void(0)" id="step-<?php echo $step_number; ?>" data-step="<?php echo $step_number; ?>">
	        		<?php if($thwmscf_layout == 'thwmscf_time_line_step') { ?>
	        			<span class="thwmscf-tab-label">
	        				<span class="thwmscf-index thwmscf-tab-icon"><?php echo $step_number; ?></span>
	        				<?php echo wp_kses_post(__($thwmscf_title_shipping, 'woo-multistep-checkout')); ?>
	        			</span>
	        		<?php } else {
	        			echo wp_kses_post(__($thwmscf_title_shipping, 'woo-multistep-checkout')); 
	        		} ?>
	        	</a>
	        </li>
    	<?php $step_number++; } ?>

		<li class="thwmscf-tab">
        	<a href="javascript:void(0)" id="step-<?php echo $step_number; ?>" data-step="<?php echo $step_number; ?>" class="last">
        		<?php if($thwmscf_layout == 'thwmscf_time_line_step') { ?>
        			<span class="thwmscf-tab-label">
        				<span class="thwmscf-index thwmscf-tab-icon"><?php echo $step_number; ?></span>
        				<?php echo wp_kses_post(__($thwmscf_title_order_review, 'woo-multistep-checkout')); ?>
        			</span>
        		<?php } else {
        			echo wp_kses_post(__($thwmscf_title_order_review, 'woo-multistep-checkout')); 
        		} ?>
        	</a>
        </li>	
	</ul>
	<div id="thwmscf-tab-panels" class="thwmscf-tab-panels">
		<?php 
		if($enable_login_reminder){
			?>
			<div class="thwmscf-tab-panel" id="thwmscf-tab-panel-0">
				<?php do_action( 'thwmscf_before_checkout_form' ); ?>
			</div>
			<?php 
		} 
		?>

		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php $step_number = 1;// do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<!--<div class="col2-set" id="customer_details">-->
					<div class="thwmscf-tab-panel" id="thwmscf-tab-panel-<?php echo $step_number; ?>">
						<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

						<div class="thwscf-billing">
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>

						<?php if($billing_shipping_together) { ?>
							<div class="thwscf-shipping">
								<?php do_action( 'woocommerce_checkout_shipping' ); ?>
							</div>

							<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
						<?php } ?>

					</div>

					<?php $step_number++; ?>

					<?php if(!$billing_shipping_together) { ?>
						<div class="thwmscf-tab-panel" id="thwmscf-tab-panel-<?php echo $step_number; ?>">
							<div class="thwscf-shipping">
								<?php do_action( 'woocommerce_checkout_shipping' ); ?>
							</div>

							<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
						</div>

						<?php $step_number++; ?>
					<?php } ?>
				<!--</div>-->

				<?php // do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

			<div class="thwmscf-tab-panel" id="thwmscf-tab-panel-<?php echo $step_number; ?>">
				<h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

			</div>
		</form>
	</div>
	<div class="thwmscf-buttons">
		<input type="button" id="action-prev" class="button-prev" value="<?php echo esc_attr(__( $button_prev_text, 'woo-multistep-checkout' )); ?>">
		<input type="button" id="action-next" class="button-next" value="<?php echo esc_attr(__( $button_next_text, 'woo-multistep-checkout' )); ?>">
		<?php 
		if($back_to_cart_button == 'yes'){
			?>
			<a class="button thwmscf-cart-url" href="<?php echo esc_url(wc_get_cart_url()); ?>"><?php echo wp_kses_post(__( $back_to_cart_button_text, 'woo-multistep-checkout' )); ?></a>
			<?php
		} ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
