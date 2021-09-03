<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YITH WooCommerce Subscription
 *
 * @class   YITH_WC_Subscription_Limit
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YITH_WC_Subscription_Limit' ) ) {

	/**
	 * Class YITH_WC_Subscription_Limit
	 */
	class YITH_WC_Subscription_Limit {


		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Subscription_Limit
		 */
		protected static $instance;


		/**
		 * List of limited products
		 *
		 * @var array $limited_products
		 */
		protected static $limited_products = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Subscription_Limit
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}


		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used.
		 */
		public function __construct() {
			add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'show_message_to_limited_product' ), 29 );
		}


		/**
		 * Show a message if the product can't be purchased because is limited.
		 *
		 * @return void
		 */
		public static function show_message_to_limited_product() {
			global $product;

			$message = '';

			if ( ! $product || ! self::is_limited( $product ) ) {
				return;
			}

			echo apply_filters( 'ywsbs_show_message_to_limited_product', esc_html__( 'You have already an active subscription with this product.', 'yith-woocommerce-subscription' ), $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Filter is_purchasable property of a product.
		 *
		 * @param bool       $is_purchasable Current is purchasable value.
		 * @param WC_Product $product        Current product.
		 */
		public static function is_purchasable( $is_purchasable, $product ) {

			if ( $is_purchasable && self::is_limited( $product ) ) {
				$is_purchasable = false;
			}

			return $is_purchasable;
		}

		/**
		 * Check if th is_purchasable property.
		 *
		 * @param  WC_Product $product Current product.
		 * @return bool|string
		 */
		public static function is_limited( $product ) {

			if ( isset( self::$limited_products[ $product->get_id() ] ) ) {
				return self::$limited_products[ $product->get_id() ];
			}

			$is_limited    = false;
			$limited_value = ywsbs_is_limited_product( $product );

			if ( $limited_value !== 'no' ) {
				$user_id = get_current_user_id();
				if ( 'one-active' === $limited_value ) {
					$one_active_status = apply_filters( 'ywsbs_limit_one_active_status', array( 'active', 'paused', 'suspended', 'overdue', 'trial', 'pending' ) );
					if ( YWSBS_Subscription_User::has_subscription( $user_id, $product->get_id(), $one_active_status ) ) {
						$is_limited = true;
					}
				} else {
					if ( YWSBS_Subscription_User::has_subscription( $user_id, $product->get_id() ) ) {
						$is_limited = true;
					}
				}
			}

			self::$limited_products[ $product->get_id() ] = $is_limited;

			return apply_filters( 'ywsbs_is_limited', $is_limited, $product->get_id() );

		}


	}
}

/**
 * Unique access to instance of YITH_WC_Subscription_Limit class
 *
 * @return YITH_WC_Subscription_Limit
 */
function YITH_WC_Subscription_Limit() { //phpcs:ignore
	return YITH_WC_Subscription_Limit::get_instance();
}
