<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WOOMULTI_CURRENCY_F_Frontend_Shipping
 */
class WOOMULTI_CURRENCY_F_Frontend_Shipping {
	protected $settings;
	protected $cache = array();

	function __construct() {
		$this->settings = WOOMULTI_CURRENCY_F_Data::get_ins();
		if ( $this->settings->get_enable() ) {
			global $wpdb;
			$raw_methods_sql = "SELECT method_id, method_order, instance_id, is_enabled FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'betrs_shipping' AND is_enabled = 1 order by instance_id ASC;";
			$raw_methods     = $wpdb->get_results( $raw_methods_sql );
			if ( count( $raw_methods ) ) {
				foreach ( $raw_methods as $method ) {
					add_filter( 'option_betrs_shipping_options-' . intval( $method->instance_id ), array(
						$this,
						'table_rate_shipping'
					) );
				}
			}
			add_filter( 'woocommerce_package_rates', array( $this, 'woocommerce_package_rates' ) );
			add_filter( 'woocommerce_shipping_free_shipping_instance_option', array(
				$this,
				'woocommerce_shipping_free_shipping_instance_option'
			), 10, 3 );
			add_filter( 'woocommerce_shipping_flat_rate_instance_option', array(
				$this,
				'woocommerce_shipping_flat_rate_instance_option'
			), 10, 3 );
		}
	}

	/**
	 * Handle min_amount of Free shipping method
	 *
	 * If enable fixed price and the value is not empty, use it. Otherwise, convert the min_amount of the default currency
	 *
	 * @param $value
	 * @param $key
	 * @param $instance
	 *
	 * @return float|int|mixed
	 */
	public function woocommerce_shipping_free_shipping_instance_option( $value, $key, $instance ) {
		if ( ! is_admin() || wp_doing_ajax() ) {
			if ( $key === 'min_amount' ) {
				$default_currency = $this->settings->get_default_currency();
				$currency         = $this->settings->get_current_currency();
				if ( $currency !== $default_currency ) {
					$value = wmc_get_price( $instance->instance_settings["min_amount"] );
				}
			}
		}

		return $value;
	}

	/**
	 * Handle cost of Flat rate shipping
	 *
	 * If enable fixed price and the value is not empty, use it. Otherwise, convert the cost of the default currency
	 *
	 * @param $value
	 * @param $key
	 * @param $instance
	 *
	 * @return float|int|mixed
	 */
	public function woocommerce_shipping_flat_rate_instance_option( $value, $key, $instance ) {
		if ( ! is_admin() || wp_doing_ajax() ) {
			$default_currency = $this->settings->get_default_currency();
			$currency         = $this->settings->get_current_currency();
			switch ( $key ) {
				case 'no_class_cost':
				case 'cost':
					if ( $currency !== $default_currency ) {
						$value = $this->get_cost_by_currency( $value, $key, $instance->instance_settings );
					}
					break;
				default:
					if ( substr( $key, 0, 11 ) === 'class_cost_' && $currency !== $default_currency ) {
						$wc_shipping      = WC_Shipping::instance();
						$shipping_classes = $wc_shipping->get_shipping_classes();
						if ( count( $shipping_classes ) ) {
							foreach ( $shipping_classes as $shipping_class ) {
								$class_cost = "class_cost_{$shipping_class->term_id}";
								if ( $class_cost === $key ) {
									$value = $this->get_cost_by_currency( $value, $key, $instance->instance_settings );
									break;
								}
							}
						}
					}
			}
		}

		return $value;
	}

	private function get_cost_by_currency( $value, $key, $instance_settings ) {
		add_shortcode( 'fee', array( 'WC_Shipping_Flat_Rate', 'fee' ) );
		if ( ! has_shortcode( $value, 'fee' ) && strpos( $value, '[qty]' ) === false && strpos( $value, '[cost]' ) === false ) {
			$value = wmc_get_price( $instance_settings[ $key ] );
		}
		remove_shortcode( 'fee', array( 'WC_Shipping_Flat_Rate', 'fee' ) );

		return $value;
	}

	/**
	 * Table rate shipping
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public function table_rate_shipping( $options ) {
		$new_options = $options;
		if ( ! empty( $new_options ) ) {
			// step through each table rate row
			foreach ( $new_options['settings'] as $o_key => $option ) {
				foreach ( $option['rows'] as $r_key => $row ) {
					$costs = $row['costs'];
					if ( is_array( $costs ) ) {
						foreach ( $costs as $k => $cost ) {
							switch ( $cost['cost_type'] ) {
								case '%':
									break;
								default:
									$options['settings'][ $o_key ]['rows'][ $r_key ]['costs'][ $k ]['cost_value'] = wmc_get_price( $cost['cost_value'] );
							}
						}
					}
				}
			}
		}

		return $options;
	}

	/**
	 * Convert shipping cost
	 *
	 * @param $methods
	 *
	 * @return mixed
	 */
	public function woocommerce_package_rates( $methods ) {
		if ( $this->settings->get_current_currency() == $this->settings->get_default_currency() ) {
			return $methods;
		}

		if ( count( array_filter( $methods ) ) ) {
			foreach ( $methods as $k => $method ) {
				if ( ! in_array( $method->method_id, apply_filters( 'wmc_excluded_shipping_methods_from_converting',
					array(
						'free_shipping',
						'flat_rate',
						'wf_shipping_ups',
						'betrs_shipping',
						'printful_shipping',
						'easyship',
						'printful_shipping_PRINTFUL_SLOW',
						'printful_shipping_STANDARD',
						'printful_shipping_PRINTFUL_MEDIUM'
					) ) ) ) {
					if ( $method->cost ) {
						$new_cost = wmc_get_price( $method->cost );
						$methods[ $k ]->set_cost( $new_cost );
//					$taxes = WC_Tax::calc_shipping_tax( $new_cost, WC_Tax::get_shipping_tax_rates() );
//					$methods[ $k ]->set_taxes( $taxes );
						if ( count( $method->get_taxes() ) ) {
							$new_tax = array();
							foreach ( $method->get_taxes() as $tax_k => $tax ) {
								$new_tax[ $tax_k ] = wmc_get_price( $tax );
							}
							$methods[ $k ]->set_taxes( $new_tax );
						}
					}
				}

			}
		}

		return $methods;
	}
}