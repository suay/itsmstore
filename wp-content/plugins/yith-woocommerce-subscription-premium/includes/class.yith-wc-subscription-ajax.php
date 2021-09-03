<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements Ajax calls of YITH WooCommerce Subscription
 *
 * @class   YITH_WC_Subscription_Ajax
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
if ( ! class_exists( 'YITH_WC_Subscription_Ajax' ) ) {
	/**
	 * Handle the assets
	 *
	 * @class YITH_WC_Subscription_Ajax
	 */
	class YITH_WC_Subscription_Ajax {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Subscription_Ajax
		 */
		private static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WC_Subscription_Ajax
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WC_Subscription_Ajax constructor.
		 */
		private function __construct() {
			$ajax_actions = array(
				'save_items',
				'recalculate',
				'cancel_subscription',
				'resume_subscription',
				'pause_subscription',
				'json_search_ywsbs_products',
				'search_categories',
				'update_delivery_status',
			);

			foreach ( $ajax_actions as $ajax_action ) {
				add_action( 'wp_ajax_ywsbs_' . $ajax_action, array( $this, $ajax_action ) );
				add_action( 'wp_ajax_nopriv_ywsbs_' . $ajax_action, array( $this, $ajax_action ) );
			}
		}

		/**
		 * Pause subscription
		 */
		public function pause_subscription() {

			check_ajax_referer( 'ywsbs_pause_subscription', 'security' );

			$posted       = $_POST;
			$subscription = false;

			if ( ! empty( $posted['subscription_id'] ) ) {
				$subscription = ywsbs_get_subscription( $posted['subscription_id'] );
			}

			if ( ! $subscription || empty( $posted['change_status'] ) ) {
				wp_send_json(
					array(
						'error' => sprintf( __( 'Error: Subscription not found or it is not possible complete your request.', 'yith-woocommerce-subscription' ) ),
					)
				);
			}

			if ( get_current_user_id() !== $subscription->get_user_id() ) {
				wp_send_json(
					array(
						'error' => sprintf( __( 'You cannot change the status of this subscription.', 'yith-woocommerce-subscription' ) ),
					)
				);
			}

			YITH_WC_Subscription()->manual_change_status( 'paused', $subscription, 'customer' );

			// translators: subscription id.
			wp_send_json(
				array(
					// translators: subscription number.
					'success' => sprintf( esc_html__( 'The subscription %s has been paused.', 'yith-woocommerce-subscription' ), $subscription->get_number() ),
				)
			);
		}

		/**
		 * Cancel subscription
		 */
		public function cancel_subscription() {

			check_ajax_referer( 'ywsbs_cancel_subscription', 'security' );

			$posted       = $_POST;
			$subscription = false;

			if ( ! empty( $posted['subscription_id'] ) ) {
				$subscription = ywsbs_get_subscription( $posted['subscription_id'] );
			}

			if ( ! $subscription || empty( $posted['change_status'] ) ) {
				wp_send_json(
					array(
						'error' => sprintf( __( 'Error: Subscription not found or it is not possible complete your request.', 'yith-woocommerce-subscription' ) ),
					)
				);
			}

			if ( get_current_user_id() !== $subscription->get_user_id() ) {
				wp_send_json(
					array(
						'error' => sprintf( __( 'You cannot change the status of this subscription.', 'yith-woocommerce-subscription' ) ),
					)
				);
			}

			YITH_WC_Subscription()->manual_change_status( 'cancelled', $subscription, 'customer' );

			wp_send_json(
				array(
					// translators: subscription number.
					'success' => sprintf( esc_html__( 'The subscription %s has been cancelled.', 'yith-woocommerce-subscription' ), $subscription->get_number() ),
				)
			);
		}

		/**
		 * Resume subscription
		 */
		public function resume_subscription() {

			check_ajax_referer( 'ywsbs_resume_subscription', 'security' );

			$posted       = $_POST;
			$subscription = false;

			if ( ! empty( $posted['subscription_id'] ) ) {
				$subscription = ywsbs_get_subscription( $posted['subscription_id'] );
			}

			if ( ! $subscription || empty( $posted['change_status'] ) ) {
				wp_send_json(
					array(
						'error' => sprintf( __( 'Error: Subscription not found or it is not possible complete your request.', 'yith-woocommerce-subscription' ) ),
					)
				);
			}

			if ( get_current_user_id() !== $subscription->get_user_id() ) {
				wp_send_json(
					array(
						'error' => sprintf( __( 'You cannot change the status of this subscription.', 'yith-woocommerce-subscription' ) ),
					)
				);
			}

			YITH_WC_Subscription()->manual_change_status( 'resumed', $subscription, 'customer' );

			wp_send_json(
				array(
					// translators: subscription number.
					'success' => sprintf( esc_html__( 'The subscription %s has been resumed.', 'yith-woocommerce-subscription' ), $subscription->get_number() ),
				)
			);

		}

		/**
		 * Save a new amount on subscription from subscription detail.
		 *
		 * @since 1.4.5
		 */
		public function save_items() {
			check_ajax_referer( 'save-item-nonce', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_REQUEST['subscription_id'] ) ) {
				wp_die( -1 );
			}
			if ( isset( $_REQUEST['items'] ) ) {
				parse_str( $_REQUEST['items'], $posted );
				$subscription = ywsbs_get_subscription( $_REQUEST['subscription_id'] );
				$subscription->update_prices( $posted );

				include YITH_YWSBS_TEMPLATE_PATH . '/admin/metabox/metabox_subscription_product.php';
			}

			wp_die();
		}

		/**
		 * Recalculate the taxes from the total amounts.
		 *
		 * @since 1.4.5
		 */
		public function recalculate() {
			check_ajax_referer( 'recalculate_nonce', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $_REQUEST['subscription_id'] ) ) {
				wp_die( -1 );
			}

			$subscription = ywsbs_get_subscription( $_REQUEST['subscription_id'] );
			$subscription->recalculate_prices();

			include YITH_YWSBS_TEMPLATE_PATH . '/admin/metabox/metabox_subscription_product.php';
			wp_die();

		}

		/**
		 * Update the status of the delivery schedules via Ajax.
		 *
		 * @throws Exception Throws Exception.
		 */
		public function update_delivery_status() {
			check_ajax_referer( 'delivery_nonce', 'security' );
			$posted = $_REQUEST;
			if ( ! current_user_can( 'edit_shop_orders' ) || ! isset( $posted['deliveryID'] ) ) {
				wp_send_json(
					array(
						'error' => esc_html_x(
							'You can have the permission to update the status',
							'Error message inside the subscription details (backend)',
							'yith-wocommerce-subscription'
						),
					)
				);
			}

			$update_result = YWSBS_Subscription_Delivery_Schedules()->update_status( $posted['deliveryID'], $posted['status'] );

			if ( isset( $posted['deliveryListTable'] ) ) {
				wp_send_json(
					array(
						'result' => $update_result['updated'],
						'sentOn' => ywsbs_get_formatted_date( $update_result['sent_on'], '-' ),
					)
				);
			} else {
				ob_start();
				wc_get_template( 'admin/metabox/metabox_subscription_delivery_schedules.php', array( 'delivery_schedules' => YWSBS_Subscription_Delivery_Schedules()->get_delivery_schedules_ordered( $posted['subscriptionID'] ) ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
				echo ob_get_clean(); //phpcs:ignore
			}

			exit;
		}

		/**
		 * Search products.
		 *
		 * @throws Exception Throws Exception.
		 */
		public function json_search_ywsbs_products() {

			check_ajax_referer( 'search-products', 'security' );

			if ( empty( $term ) && isset( $_GET['term'] ) ) {
				$term = (string) wc_clean( wp_unslash( $_GET['term'] ) );
			}

			if ( empty( $term ) ) {
				wp_die();
			}

			if ( ! empty( $_GET['limit'] ) ) {
				$limit = absint( $_GET['limit'] );
			} else {
				$limit = absint( apply_filters( 'woocommerce_json_search_limit', 30 ) );
			}

			$data_store = WC_Data_Store::load( 'product' );
			$ids        = $data_store->search_products( $term, '', true, false, $limit );

			$product_objects = array_filter( array_map( 'wc_get_product', $ids ), 'wc_products_array_filter_readable' );
			$products        = array();

			foreach ( $product_objects as $product_object ) {
				if ( ywsbs_is_subscription_product( $product_object->get_id() ) ) {

					$products[ $product_object->get_id() ] = rawurldecode( $product_object->get_name() );
				}
			}

			wp_send_json( apply_filters( 'woocommerce_json_search_found_products', $products ) );
		}


		/**
		 * Get Categories via Ajax
		 *
		 * @since 1.0
		 */
		public function search_categories() {

			check_ajax_referer( 'search-products', 'security' );

			if ( ! current_user_can( 'edit_products' ) ) {
				wp_die( -1 );
			}

			if ( ! isset( $_GET['term'] ) || ! wc_clean( stripslashes( $_GET['term'] ) ) ) {
				wp_die();
			}

			$search_text = wc_clean( stripslashes( $_GET['term'] ) );
			$found_tax   = array();
			$args        = array(
				'taxonomy'   => array( 'product_cat' ),
				'orderby'    => 'id',
				'order'      => 'ASC',
				'hide_empty' => true,
				'fields'     => 'all',
				'name__like' => $search_text,
			);

			$terms = get_terms( $args );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$term->formatted_name       .= $term->name . ' (' . $term->count . ')';
					$found_tax[ $term->term_id ] = $term->formatted_name;
				}
			}

			wp_send_json( $found_tax );
		}
	}
}
