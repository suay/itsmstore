<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Coupons Class.
 *
 * @class   YWSBS_Subscription_Coupons
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Subscription_Coupons' ) ) {

	/**
	 * Class YWSBS_Subscription_Coupons
	 */
	class YWSBS_Subscription_Coupons {

		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Coupons
		 */
		protected static $instance;

		/**
		 * List of coupon types
		 *
		 * @var array
		 */
		protected $coupon_types = array();

		/**
		 * Coupon error message
		 *
		 * @var string
		 */
		protected $coupon_error = '';

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Coupons
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			$this->coupon_types = array( 'signup_percent', 'signup_fixed', 'recurring_percent', 'recurring_fixed' );

			// Add new coupons type to administrator.
			add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_coupon_discount_types' ) );
			add_filter( 'woocommerce_product_coupon_types', array( $this, 'add_coupon_discount_types_list' ) );

			// Apply discounts to a product and get the discounted price (before tax is applied).
			add_filter( 'woocommerce_get_discounted_price', array( $this, 'get_discounted_price' ), 10, 3 );
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'coupon_get_discount_amount' ), 10, 5 );
			add_filter( 'woocommerce_coupon_sort', array( $this, 'sort_coupons' ), 10, 2 );

			// Validate coupons.
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate_coupon' ), 10, 2 );

		}

		/**
		 * Override sort coupons during the discount calculation.
		 *
		 * @param int       $sort Sort priority.
		 * @param WC_Coupon $coupon Coupon.
		 *
		 * @since 2.1
		 */
		public function sort_coupons( $sort, $coupon ) {
			$coupon_type = $coupon->get_discount_type();
			if ( ! in_array( $coupon_type, $this->coupon_types, true ) ) {
				return $sort;
			}

			if ( in_array( $coupon_type, array( 'signup_percent', 'recurring_percent' ) ) ) {
				$sort = 2;
			}

			if ( in_array( $coupon_type, array( 'signup_fixed', 'signup_fixed' ) ) ) {
				$sort = 1;
			}

			return $sort;
		}

		/**
		 * Add discount types on coupon system
		 *
		 * @param array $coupons_type List of coupon types.
		 * @return mixed
		 *
		 * @since 1.0.0
		 */
		public function add_coupon_discount_types( $coupons_type ) {

			$coupons_type['signup_percent']    = esc_html__( 'Subscription Signup % Discount', 'yith-woocommerce-subscription' );
			$coupons_type['signup_fixed']      = esc_html__( 'Subscription Signup Discount', 'yith-woocommerce-subscription' );
			$coupons_type['recurring_percent'] = esc_html__( 'Subscription Recurring % Discount', 'yith-woocommerce-subscription' );
			$coupons_type['recurring_fixed']   = esc_html__( 'Subscription Recurring Discount', 'yith-woocommerce-subscription' );

			return $coupons_type;
		}

		/**
		 * Add subscription coupons to WooCommerce List.
		 *
		 * @param array $coupons_type Coupon type list.
		 *
		 * @return array
		 */
		public function add_coupon_discount_types_list( $coupons_type ) {
			return array_merge( $coupons_type, $this->coupon_types );
		}

		/**
		 * Return the discounted price.
		 *
		 * @param float   $price Price of cart item.
		 * @param array   $cart_item Cart item.
		 * @param WC_Cart $cart Cart Object.
		 *
		 * @return mixed
		 * @throws Exception Return an error.
		 */
		public function get_discounted_price( $price, $cart_item, $cart ) {

			$id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

			if ( ! $price || ! ywsbs_is_subscription_product( $id ) ) {
				return $price;
			}

			$regular_price   = $price;
			$applied_coupons = ywsbs_get_applied_coupons( $cart );

			if ( ! empty( $applied_coupons ) ) {

				$product = $cart_item['data'];
				foreach ( $applied_coupons as $code => $coupon ) {
					$valid = ywsbs_coupon_is_valid( $coupon, WC()->cart );

					if ( $valid ) {
						$discount_amount = (float) $coupon->get_discount_amount( 'yes' === get_option( 'woocommerce_calc_discounts_sequentially', 'no' ) ? $price : $regular_price, $cart_item, true );

						// Store the totals for DISPLAY in the cart.
						$total_discount     = $discount_amount * $cart_item['quantity'];
						$total_discount_tax = 0;

						if ( wc_tax_enabled() && $product->is_taxable() ) {
							$tax_rates          = WC_Tax::get_rates( $product->get_tax_class() );
							$taxes              = WC_Tax::calc_tax( $discount_amount, $tax_rates, $cart->prices_include_tax );
							$total_discount_tax = WC_Tax::get_tax_total( $taxes ) * $cart_item['quantity'];
							$total_discount     = $cart->prices_include_tax ? $total_discount - $total_discount_tax : $total_discount;

							$cart->discount_cart_tax += $total_discount_tax;

						}

						$cart->discount_cart += $total_discount;

						$this->increase_coupon_discount_amount( $code, $total_discount, $total_discount_tax, $cart );
						$this->increase_coupon_applied_count( $code, $cart_item['quantity'], $cart );

					}

					// If the price is 0, we can stop going through coupons because there is nothing more to discount for this product.
					if ( 0 >= $price ) {
						break;
					}
				}
			}

			return $price;
		}


		/**
		 * Total of coupon discounts
		 *
		 * @param string  $coupon_code Coupon Code.
		 * @param float   $amount Amount.
		 * @param float   $total_discount_tax Total discount tax.
		 * @param WC_Cart $cart Cart.
		 *
		 * @return void
		 */
		public function increase_coupon_discount_amount( $coupon_code, $amount, $total_discount_tax, $cart ) {
			$cart->coupon_discount_amounts[ $coupon_code ]     = isset( $cart->coupon_discount_amounts[ $coupon_code ] ) ? $cart->coupon_discount_amounts[ $coupon_code ] + $amount : $amount;
			$cart->coupon_discount_tax_amounts[ $coupon_code ] = isset( $cart->coupon_discount_tax_amounts[ $coupon_code ] ) ? $cart->coupon_discount_tax_amounts[ $coupon_code ] + $total_discount_tax : $total_discount_tax;
		}

		/**
		 * Increase coupon applied count.
		 *
		 * @param string  $coupon_code Coupon Code.
		 * @param int     $count Counter.
		 * @param WC_Cart $cart Cart.
		 */
		public function increase_coupon_applied_count( $coupon_code, $count = 1, $cart ) {
			if ( empty( $cart->coupon_applied_count[ $coupon_code ] ) ) {
				$cart->coupon_applied_count[ $coupon_code ] = 0;
			}
			$cart->coupon_applied_count[ $coupon_code ] += $count;
		}

		/**
		 * Check if coupon is valid.
		 *
		 * @param bool      $is_valid Is valid.
		 * @param WC_Coupon $coupon WC_Coupon.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function validate_coupon( $is_valid, $coupon ) {

			$this->coupon_error   = '';
			$coupon_type          = $coupon->get_discount_type();
			$subscription_in_cart = YWSBS_Subscription_Cart::cart_has_subscriptions();

			if ( ! in_array( $coupon_type, $this->coupon_types, true ) && ! $subscription_in_cart ) {
				return $is_valid;
			}

			// ignore non-subscription coupons.
			if ( ! $subscription_in_cart ) {
				$this->coupon_error = esc_html__( 'Sorry, this coupon can be used only if there is a subscription in the cart', 'yith-woocommerce-subscription' );
			} else {
				if ( in_array( $coupon_type, array( 'signup_percent', 'signup_fixed' ), true ) && ! YWSBS_Subscription_Cart::cart_has_subscription_with_signup() ) {
					$this->coupon_error = __( 'Sorry, this coupon can be used only if there is a subscription with signup fees', 'yith-woocommerce-subscription' );
				}
			}

			if ( ! empty( $this->coupon_error ) ) {
				$is_valid = false;
				add_filter( 'woocommerce_coupon_error', array( $this, 'add_coupon_error' ), 10 );
			}

			return $is_valid;
		}

		/**
		 * Get discount amount.
		 *
		 * @param float      $discount Amount this coupon has discounted.
		 * @param float      $discounting_amount Amount the coupon is being applied to.
		 * @param array|null $cart_item Cart item being discounted if applicable.
		 * @param boolean    $single True if discounting a single qty item, false if its the line.
		 * @param WC_Coupon  $coupon Coupon Object.
		 *
		 * @return float|int|mixed
		 * @throws Exception Return error.
		 */
		public function coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

			$product = $cart_item['data'];
			$id      = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

			if ( ! ywsbs_is_subscription_product( $id ) ) {
				return $discount;
			}

			$fee             = ywsbs_get_product_fee( $product );
			$trial_per       = ywsbs_get_product_trial( $product );
			$recurring_price = $discounting_amount; // $product->get_price();

			$valid = ywsbs_coupon_is_valid( $coupon, WC()->cart );

			if ( ! empty( $coupon ) && $valid ) {

				$coupon_type   = $coupon->get_discount_type();
				$coupon_amount = $coupon->get_amount();

				switch ( $coupon_type ) {
					case 'signup_percent':
						if ( ! empty( $fee ) && 0 !== $fee ) {
							$discount = round( ( $fee / 100 ) * $coupon_amount, WC()->cart->dp );
						}
						break;
					case 'recurring_percent':
						if ( empty( $trial_per ) || isset( WC()->cart->subscription_coupon ) ) {
							$discount = round( ( $recurring_price / 100 ) * $coupon_amount, WC()->cart->dp );
						}
						break;
					case 'signup_fixed':
						if ( ! empty( $fee ) && 0 !== $fee ) {
							$discount = ( $fee < $coupon_amount ) ? $fee : $coupon_amount;
						}
						break;
					case 'recurring_fixed':
						if ( empty( $trial_per ) || isset( WC()->cart->subscription_coupon ) ) {
							$discount = ( $recurring_price < $coupon_amount ) ? $recurring_price : $coupon_amount;
						}
						break;
					default:
				}
			}

			return $discount;

		}

		/**
		 * Add coupon error if the coupon is not valid
		 *
		 * @param string $errors Error.
		 * @return string
		 * @since  1.0.0
		 */
		public function add_coupon_error( $errors ) {
			if ( ! empty( $this->coupon_error ) ) {
				$errors = $this->coupon_error;
			}

			return $errors;
		}
	}

}

/**
 * Unique access to instance of YWSBS_Subscription_Coupons class
 *
 * @return YWSBS_Subscription_Coupons
 */
function YWSBS_Subscription_Coupons() { // phpcs:ignore
	return YWSBS_Subscription_Coupons::get_instance();
}
