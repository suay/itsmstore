<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YITH WooCommerce Subscription Database Class
 *
 * @class   YITH_WC_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Subscription_DB' ) ) {
	/**
	 * Class YITH_WC_Subscription_DB
	 * handle DB custom tables
	 *
	 * @abstract
	 */
	abstract class YITH_WC_Subscription_DB {


		/**
		 * Database version.
		 *
		 * @var string DB version
		 */
		public static $version = '1.1.1';

		/**
		 * Activity Log table name
		 *
		 * @var string
		 */
		public static $activities_log = 'yith_ywsbs_activities_log';

		/**
		 * Delivery schedules table name
		 *
		 * @var string
		 */
		public static $delivery_schedules = 'yith_ywsbs_delivery_schedules';

		/**
		 * Install the database
		 */
		public static function install() {
			self::create_db_tables();
		}

		/**
		 * Create table
		 *
		 * @param bool $force Force the creation.
		 */
		public static function create_db_tables( $force = false ) {
			global $wpdb;

			$current_version = get_option( 'yith_ywsbs_db_version' );

			if ( $force || self::$version !== $current_version ) {
				$wpdb->hide_errors();

				$table_name      = $wpdb->prefix . self::$activities_log;
				$delivery_schedules = $wpdb->prefix . self::$delivery_schedules;
				$charset_collate = $wpdb->get_charset_collate();

				$sql = "CREATE TABLE $table_name (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`activity` varchar(255) NOT NULL,
							`status` varchar(255) NOT NULL,
							`subscription` int(11) NOT NULL,
							`order` int(11) NOT NULL,
							`description` varchar(255) NOT NULL,
							`timestamp_date` datetime NOT NULL,
							PRIMARY KEY (id)
						) $charset_collate;";


				$sql .= "CREATE TABLE $delivery_schedules (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `subscription_id` bigint(20),
                    `status` varchar(20),
                    `entry_date` datetime NOT NULL,
                    `scheduled_date` datetime NOT NULL,
                    `sent_on` datetime,
                    PRIMARY KEY (id)
                    ) $charset_collate;";

				if ( ! function_exists( 'dbDelta' ) ) {
					include_once ABSPATH . 'wp-admin/includes/upgrade.php';
				}
				dbDelta( $sql );
				update_option( 'yith_ywsbs_db_version', self::$version );
			}
		}
	}
}
