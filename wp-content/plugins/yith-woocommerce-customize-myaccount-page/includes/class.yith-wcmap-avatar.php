<?php
/**
 * Avatar handler class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMAP_Avatar' ) ) {
	/**
	 * YITH WooCommerce Customize My Account Page Avatar class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Avatar {

		/**
		 * Action upload avatar
		 *
		 * @since 3.0.0
		 * @const string
		 */
		const AVATAR_ACTION = 'ywcmap_avatar_action';

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function __construct() {

			if( self::can_upload_avatar() ) {
				// AJAX actions
				add_action( 'wc_ajax_' . self::AVATAR_ACTION, array( $this, 'avatar_ajax_action' ) );
				// add frontend modal
				add_action( 'wp_footer', array( $this, 'add_avatar_modal' ) );
				// enqueue scripts and styles
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

				add_filter( 'pre_get_avatar', array( $this, 'get_avatar' ), 100, 3 );
			}

			// filter user avatar
			add_filter( 'pre_get_avatar_data', array( $this, 'default_avatar_url' ), 10, 3 );

			// gdpr compliance
			add_filter( 'woocommerce_privacy_export_customer_personal_data', array( $this, 'export_avatar' ), 99, 2 );
			add_filter( 'woocommerce_privacy_erase_personal_data_customer', array( $this, 'erase_avatar' ), 99, 2 );
		}

		/**
		 * Check if users are able to upload their own avatar image
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public static function can_upload_avatar() {
			$opts       = get_option( 'yith_wcmap_avatar', array() );
			$enabled    = ! isset( $opts['custom'] ) || 'yes' === $opts['custom'];

			return apply_filters( 'yith_wcmap_users_can_upload_avatar', $enabled );
		}

		/**
		 * Check if users are able to upload their own avatar image
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public static function get_avatar_default_size() {
			$opts = get_option( 'yith_wcmap_avatar', array() );
			$size = ! empty( $opts['avatar_size'] ) ? intval( $opts['avatar_size'] ) : 120;

			return apply_filters( 'yith_wcmap_filter_avatar_size', $size );
		}

		/**
		 * Add localized script for avatar
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_localize_script( 'ywcmap-frontend', 'yith_wcmap', array(
				'ajaxUrl'     => WC_AJAX::get_endpoint( self::AVATAR_ACTION ),
				'actionNonce' => wp_create_nonce( self::AVATAR_ACTION ),
			) );
		}

		/**
		 * Handle avatar AJAX actions
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function avatar_ajax_action() {
			check_ajax_referer( self::AVATAR_ACTION, 'security' );

			$action = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : false;

			if ( empty( $action ) || ! is_callable( array( $this, $action ) ) ) {
				wp_send_json_error();
			}

			do_action( 'yith_wcmap_before_avatar_ajax_action', $action );

			$this->$action();
			die();
		}

		/**
		 * Get user custom avatar ID
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @param integer $user
		 * @return integer
		 */
		public function get_user_avatar_id( $user = 0 ) {
			! $user && $user = get_current_user_id();
			return intval( get_user_meta( $user, 'yith-wcmap-avatar', true ) );
		}

		/**
		 * Upload custom avatar
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function upload_avatar() {

			if ( empty( $_FILES['ywcmap_custom_avatar'] ) ) {
				wp_send_json_error();
			}

			// Required file.
			if ( ! function_exists( 'media_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
			}
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			}

			$media_id = media_handle_upload( 'ywcmap_custom_avatar', 0 );
			if ( is_wp_error( $media_id ) ) {
				wp_send_json_error();
			}

			// Save media id for filter query in media library.
			$medias   = get_option( 'yith_wcmap_users_avatar_ids', array() );
			$medias[] = $media_id;
			// Then save.
			update_option( 'yith_wcmap_users_avatar_ids', $medias );

			// Save user meta.
			$user = get_current_user_id();
			update_user_meta( $user, 'yith_wcmap_avatar_temp', $media_id );

			// maybe resize img
			yith_wcmap_resize_avatar_url( $media_id, '150' );
			$src = yith_wcmap_generate_avatar_url( $media_id, '150' );

			wp_send_json_success( array(
				'html' => sprintf( "<img src='%s' height='150' width='150' />", esc_url( $src ) )
			) );
		}

		/**
		 * Set custom avatar from temp value
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function set_avatar() {
			// Get temp user avatar
			$user       = get_current_user_id();
			$avatar_id  = get_user_meta( $user, 'yith_wcmap_avatar_temp', true );

			if( empty( $avatar_id ) ) {
				wp_send_json_error();
			}

			// Clear old avatar if any
			$this->delete_avatar( 'yith-wcmap-avatar' );
			// Set the new one
			delete_user_meta( $user, 'yith_wcmap_avatar_temp' );
			update_user_meta( $user, 'yith-wcmap-avatar', $avatar_id );

			wp_send_json_success();
		}

		/**
		 * Clear temp avatar
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function clear_temp_avatar() {
			$this->delete_avatar( 'yith_wcmap_avatar_temp' );
			wp_send_json_success();
		}

		/**
		 * Reset avatar to default
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function reset_avatar() {
			$this->delete_avatar( 'yith-wcmap-avatar' );
			wp_send_json_success();
		}

		/**
		 * Delete given customer avatar
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @param string $avatar_key The avatar key to delete
		 * @return boolean
		 */
		protected function delete_avatar( $avatar_key ) {

			$user   = get_current_user_id();
			$avatar = get_user_meta( $user, $avatar_key, true );

			if ( empty( $avatar ) ) {
				return true;
			}

			$avatar_ids = get_option( 'yith_wcmap_users_avatar_ids', array() );
			if ( false !== ( $key = array_search( $avatar, $avatar_ids ) ) ) {
				unset( $avatar_ids[ $key ] );
				// then save
				update_option( 'yith_wcmap_users_avatar_ids', $avatar_ids );
			}

			// then delete user meta
			delete_user_meta( $user, $avatar_key );
			wp_delete_attachment( $avatar, true );
			return true;
		}

		/**
		 * Get avatar upload form
		 *
		 * @since  2.2.0
		 * @author Francesco Licandro
		 * @access public
		 * @return void
		 */
		public function add_avatar_modal() {

			if ( is_null( YITH_WCMAP()->frontend ) || ! YITH_WCMAP()->frontend->is_my_account() ) {
				return;
			}

			wc_get_template( 'ywcmap-myaccount-avatar-modal.php', array(
				'has_custom_avatar' => ! empty( $this->get_user_avatar_id() )
			), '', YITH_WCMAP_DIR . 'templates/' );
		}

		/**
		 * Get customer avatar for user
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $avatar
		 * @param mixed  $id_or_email
		 * @param array  $args
		 * @return string
		 */
		public function get_avatar( $avatar, $id_or_email, $args ) {

			// prevent filter
			remove_all_filters( 'get_avatar' );

			$user = false;

			if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
				$user = get_user_by( 'email', $id_or_email );
			} elseif ( $id_or_email instanceof WP_User ) {
				// User Object
				$user = $id_or_email;
			} elseif ( $id_or_email instanceof WP_Post ) {
				// Post Object
				$user = get_user_by( 'id', (int) $id_or_email->post_author );
			} elseif ( $id_or_email instanceof WP_Comment ) {

				if ( ! empty( $id_or_email->user_id ) ) {
					$user = get_user_by( 'id', (int) $id_or_email->user_id );
				}
				if ( ( ! $user || is_wp_error( $user ) ) && ! empty( $id_or_email->comment_author_email ) ) {
					$email = $id_or_email->comment_author_email;
					$user  = get_user_by( 'email', $email );
				}
			}

			// get the user ID
			$user_id = ! $user ? $id_or_email : $user->ID;
			// get custom avatar and make sure file exists
			$custom_avatar = $this->get_user_avatar_id( $user_id );
			if ( ! $custom_avatar || ! get_attached_file( $custom_avatar ) ) {
				return $avatar;
			}

			// maybe resize img
			$resized = yith_wcmap_resize_avatar_url( $custom_avatar, $args['size'] );
			// if error occurred return
			if ( ! $resized ) {
				return $avatar;
			}

			$src   = yith_wcmap_generate_avatar_url( $custom_avatar, $args['size'] );
			$class = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );

			$avatar = sprintf(
				"<img alt='%s' src='%s' class='%s' height='%d' width='%d' %s/>",
				esc_attr( $args['alt'] ),
				esc_url( $src ),
				esc_attr( join( ' ', $class ) ),
				(int) $args['height'],
				(int) $args['width'],
				$args['extra_attr']
			);

			return $avatar;
		}

		/**
		 * Filter default avatar url to get the custom one
		 *
		 * @since  3.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $args Arguments passed to get_avatar_data(), after processing.
		 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user ID, Gravatar MD5 hash,
		 *                           user email, WP_User object, WP_Post object, or WP_Comment object.
		 * @return array
		 */
		public function default_avatar_url( $args, $id_or_email ) {
			$opts = get_option( 'yith_wcmap_avatar', array() );
			if ( ! empty( $opts['default'] ) && ! empty( $opts['custom_default'] ) && 'custom' === $opts['default'] ) {
				$args['default'] = $opts['custom_default'];
			}

			return $args;
		}

		/**
		 * Add avatar to customer data for GDPR exporter
		 *
		 * @since  2.2.9
		 * @author Francesco Licandro
		 * @param array       $data
		 * @param WC_Customer $customer
		 * @return array
		 */
		public function export_avatar( $data, $customer ) {

			$avatar = $this->get_user_avatar_id( $customer->get_id() );
			if ( ! $avatar ) {
				return $data;
			}

			$src = wp_get_attachment_image_src( $avatar, 'full' );
			if ( $src ) {
				$data[] = array(
					'name'  => __( 'Custom Avatar', 'yith-woocommerce-customize-myaccount-page' ),
					'value' => '<a href="' . $src[0] . '">' . $src[0] . '</a>',
				);
			}

			return $data;
		}

		/**
		 * Erase custom avatar on GDPR request
		 *
		 * @since  2.2.9
		 * @author Francesco Licandro
		 * @param array       $response
		 * @param WC_Customer $customer
		 * @return array
		 */
		public function erase_avatar( $response, $customer ) {

			$avatar = $this->get_user_avatar_id( $customer->get_id() );
			if ( ! $avatar ) {
				return $response;
			}

			// remove id from global list
			$medias = get_option( 'yith_wcmap_users_avatar_ids', array() );
			foreach ( $medias as $key => $media ) {
				if ( $media == $avatar ) {
					unset( $medias[ $key ] );
					continue;
				}
			}

			// then save
			update_option( 'yith_wcmap_users_avatar_ids', $medias );
			// then delete user meta
			delete_user_meta( $customer->get_id(), 'yith-wcmap-avatar' );
			// then delete media attachment
			wp_delete_attachment( $avatar );

			$response['messages'][]    = __( 'Removed customer avatar', 'yith-woocommerce-customize-myaccount-page' );
			$response['items_removed'] = true;

			return $response;
		}
	}
}

new YITH_WCMAP_Avatar();