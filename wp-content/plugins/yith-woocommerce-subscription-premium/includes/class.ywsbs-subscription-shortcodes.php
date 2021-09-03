<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Shortcode set all plugin shortcodes
 *
 * @class   YWSBS_Subscription_Shortcodes
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YWSBS_Subscription_Shortcodes class.
 *
 * @class   YWSBS_Subscription_Shortcodes
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 */
class YWSBS_Subscription_Shortcodes {


	/**
	 * Constructor for the shortcode class
	 */
	public function __construct() {
		add_shortcode( 'ywsbs_my_account_subscriptions', array( __CLASS__, 'my_account_subscriptions_shortcode' ) );
	}


	/**
	 * Add subscription section on my-account page
	 *
	 * @return  string
	 * @since   1.0.0
	 */
	public static function my_account_subscriptions_shortcode() {
		$subscriptions = YWSBS_Subscription_Helper()->get_subscriptions_by_user( get_current_user_id() );
		ob_start();
		wc_get_template( 'myaccount/my-subscriptions-view.php', array( 'subscriptions' => $subscriptions ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		return ob_get_clean();
	}
}
