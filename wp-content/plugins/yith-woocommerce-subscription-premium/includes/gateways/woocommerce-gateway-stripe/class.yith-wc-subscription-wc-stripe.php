<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH_WC_Subscription_WC_Stripe integration with WooCommerce Stripe Plugin
 *
 * @class   YITH_WC_Subscription_WC_Stripe
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Compatibility class for WooCommerce Gateway Stripe.
 *
 * @extends WC_Gateway_Stripe
 */
class YITH_WC_Subscription_WC_Stripe extends WC_Gateway_Stripe {


	/**
	 * Instance of YITH_WC_Subscription_WC_Stripe
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Stripe gateway id
	 *
	 * @var   string ID of specific gateway
	 * @since 1.0
	 */
	public static $gateway_id = 'stripe';

	/**
	 * Return the instance of Gateway
	 *
	 * @return YITH_Subscription_WC_Stripe
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->supports = array(
			'products',
			'refunds',
			'tokenization',
			'yith_subscriptions',
			'yith_subscriptions_scheduling',
			'yith_subscriptions_pause',
			'yith_subscriptions_multiple',
			'yith_subscriptions_payment_date',
			'yith_subscriptions_recurring_amount',
		);

		add_action( 'ywsbs_subscription_payment_complete', array( $this, 'add_payment_meta_data_to_subscription' ), 10, 2 );

		// Pay the renew orders.
		add_action( 'ywsbs_pay_renew_order_with_' . $this->id, array( $this, 'pay_renew_order' ), 10, 2 );
	}

	/**
	 * Process the payment based on type.
	 *
	 * @param int   $order_id Reference.
	 * @param bool  $retry Should we retry on fail.
	 * @param bool  $force_save_source Force save the payment source.
	 * @param mixed $previous_error Any error message from previous request.
	 * @param bool  $use_order_source Whether to use the source, which should already be attached to the order.
	 *
	 * @return array
	 * @throws Exception Trigger an error.
	 */
	public function process_payment( $order_id, $retry = true, $force_save_source = false, $previous_error = false, $use_order_source = false ) {
		if ( YWSBS_Subscription_Cart::cart_has_subscriptions() ) {
			return parent::process_payment( $order_id, $retry, true, $previous_error );
		}

		return parent::process_payment( $order_id, $retry, $force_save_source, $previous_error );
	}

	/**
	 * Register the payment information on subscription meta.
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 * @param WC_Order           $order Order.
	 */
	public function add_payment_meta_data_to_subscription( $subscription, $order ) {

		if ( ! $subscription || ! $order || $subscription->get_order_id() !== $order->get_id() || $subscription->get_payment_method() !== $this->id ) {
			return;
		}

		$subscription->set( '_stripe_customer_id', $order->get_meta( '_stripe_customer_id' ) );
		$subscription->set( '_stripe_source_id', $order->get_meta( '_stripe_source_id' ) );

	}

	/**
	 * Pay the renew order.
	 *
	 * It is triggered by ywsbs_pay_renew_order_with_{gateway_id} action.
	 *
	 * @param WC_Order $renewal_order Order to renew.
	 * @param bool     $manually Check if this is a manual renew.
	 *
	 * @return array|bool|WP_Error
	 * @throws WC_Stripe_Exception Trigger an error.
	 * @since  1.1.0
	 */
	public function pay_renew_order( $renewal_order = null, $manually = false ) {

		if ( is_null( $renewal_order ) ) {
			return false;
		}

		$is_a_renew      = $renewal_order->get_meta( 'is_a_renew' );
		$subscriptions   = $renewal_order->get_meta( 'subscriptions' );
		$subscription_id = $subscriptions ? $subscriptions[0] : false;
		$order_id        = $renewal_order->get_id();

		if ( ! $subscription_id ) {
			// translators: placeholder order id.
			WC_Stripe_Logger::log( sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-woocommerce-subscription' ), $order_id ) );
			// translators: placeholder order id.
			yith_subscription_log( sprintf( __( 'Sorry, any subscription is found for this order: %s', 'yith-woocommerce-subscription' ), $order_id ), 'subscription_payment' );
			return false;
		}

		foreach ( $subscriptions as $subscription_id ) {

			$subscription   = ywsbs_get_subscription( $subscription_id );
			$has_source     = $subscription->get( '_stripe_source_id' );
			$has_customer   = $subscription->get( '_stripe_customer_id' );
			$previous_error = false;

			if ( 'yes' !== $is_a_renew || empty( $has_source ) || empty( $has_customer ) ) {
				yith_subscription_log( 'Cannot pay order for the subscription ' . $subscription->id . ' stripe_customer_id=' . $has_customer . ' stripe_source_id=' . $has_source, 'subscription_payment' );
				ywsbs_register_failed_payment( $renewal_order, __( 'Error: Stripe customer and source info are missing.', 'yith-woocommerce-subscription' ) );
				return false;
			}

			$amount = $renewal_order->get_total();

			try {
				if ( $amount * 100 < WC_Stripe_Helper::get_minimum_amount() ) {
					/* translators: minimum amount */
					$message = sprintf( __( 'Sorry, the minimum allowed order total is %1$s to use this payment method.', 'woocommerce-gateway-stripe' ), wc_price( WC_Stripe_Helper::get_minimum_amount() / 100 ) );
					ywsbs_register_failed_payment( $renewal_order, $message );
					return new WP_Error( 'stripe_error', $message );
				}

				$order_id = $renewal_order->get_id();

				// Get source from order.
				$prepared_source = $this->prepare_order_source( $renewal_order );

				if ( ! $prepared_source->customer ) {
					return new WP_Error( 'stripe_error', __( 'Customer not found', 'woocommerce-gateway-stripe' ) );
				}

				WC_Stripe_Logger::log( "Info: Begin processing subscription payment for order {$order_id} for the amount of {$amount}" );

				if ( ( $this->is_no_such_source_error( $previous_error ) || $this->is_no_linked_source_error( $previous_error ) ) && apply_filters( 'wc_stripe_use_default_customer_source', true ) ) {
					// Passing empty source will charge customer default.
					$prepared_source->source = '';
				}

				$request            = $this->generate_payment_request( $renewal_order, $prepared_source );
				$request['capture'] = 'true';
				$request['amount']  = WC_Stripe_Helper::get_stripe_amount( $amount, $request['currency'] );
				$response           = WC_Stripe_API::request( $request );
				if ( ! empty( $response->error ) ) {
					$localized_messages = WC_Stripe_Helper::get_localized_messages();

					if ( 'card_error' === $response->error->type ) {
						$localized_message = isset( $localized_messages[ $response->error->code ] ) ? $localized_messages[ $response->error->code ] : $response->error->message;
					} else {
						$localized_message = isset( $localized_messages[ $response->error->type ] ) ? $localized_messages[ $response->error->type ] : $response->error->message;
					}

					$renewal_order->add_order_note( $localized_message );
					throw new WC_Stripe_Exception( print_r( $response, true ), $localized_message ); // phpcs:ignore
				}

				do_action( 'wc_gateway_stripe_process_payment', $response, $renewal_order );

				$this->process_response( $response, $renewal_order );
			} catch ( WC_Stripe_Exception $e ) {
				WC_Stripe_Logger::log( 'Error: ' . $e->getMessage() );
				ywsbs_register_failed_payment( $renewal_order, 'Error: ' . $e->getMessage() );
				do_action( 'wc_gateway_stripe_process_payment_error', $e, $renewal_order );
			}
		}

	}

	/**
	 * Get payment source from an order.
	 *
	 * Not using 2.6 tokens for this part since we need a customer AND a card
	 * token, and not just one.
	 *
	 * @param WC_Order $order Order.
	 * @return  object
	 * @since   3.1.0
	 * @version 4.0.0
	 */
	public function prepare_order_source( $order = null ) {
		$stripe_customer = new WC_Stripe_Customer();
		$stripe_source   = false;
		$token_id        = false;
		$source_object   = false;

		if ( $order ) {
			$order_id      = $order->get_id();
			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( empty( $subscriptions ) ) {
				return false;
			}

			foreach ( $subscriptions as $subscription_id ) {
				$subscription       = ywsbs_get_subscription( $subscription_id );
				$stripe_customer_id = $subscription->get( '_stripe_customer_id' );

				if ( $stripe_customer_id ) {
					$stripe_customer->set_id( $stripe_customer_id );
				}

				$source_id = $this->get_source_id( $subscription );

				if ( $source_id ) {
					$stripe_source = $source_id;
					$source_object = WC_Stripe_API::retrieve( 'sources/' . $source_id );
				} elseif ( apply_filters( 'wc_stripe_use_default_customer_source', true ) ) {
					/*
					* We can attempt to charge the customer's default source
					* by sending empty source id.
					*/
					$stripe_source = '';
				}
			}

			return (object) array(
				'token_id'      => $token_id,
				'customer'      => $stripe_customer ? $stripe_customer->get_id() : false,
				'source'        => $stripe_source,
				'source_object' => $source_object,
			);
		}

		return false;

	}

	/**
	 * Get the source id.
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 * @return string;
	 */
	protected function get_source_id( $subscription ) {
		$source_id = $subscription->get( '_stripe_source_id' );
		$wc_token  = WC_Payment_Tokens::get( $source_id );
		$user_id   = $subscription->get_user_id();

		if ( empty( $wc_token ) ) {

			$default = WC_Payment_Tokens::get_customer_default_token( $user_id );

			if ( $default && self::$gateway_id === $default->gateway_id ) {
				$source_id = $default->get_token();
			} else {
				$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, self::$gateway_id );
				if ( $tokens ) {
					foreach ( $tokens as $token ) {
						$source_id = $token->get_token();
						break;
					}
				}
			}

			if( $source_id = $subscription->get( '_stripe_source_id' ) ){
				$source_id = '';
			}else{
				$subscription->set( '_stripe_source_id', $source_id );
			}
		}

		return $source_id;
	}
}
