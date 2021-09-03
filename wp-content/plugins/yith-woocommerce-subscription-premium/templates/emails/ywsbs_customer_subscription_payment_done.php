<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * This is the email sent to the customer when his subscription is in overdue
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @var $email_heading
 * @var $email
 * @var YWSBS_Subscription $subscription
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<?php /* translators: %s: Customer first name */ ?>
	<p><?php printf( esc_html__( 'Hi %s,', 'yith-woocommerce-subscription' ), esc_html( $subscription->get_billing_first_name() ) ); ?></p>
<?php
$order_ids = $subscription->get_order_ids();
if ( count( $order_ids ) > 1 ) :
	?>
<p><?php printf( esc_html( __( 'Your subscription renewal order is now being processed. Your order details are shown below for your reference:', 'yith-woocommerce-subscription' ) ), esc_html( get_option( 'blogname' ) ) ); ?></p>
<?php else : ?>
	<p><?php printf( esc_html( __( 'Your subscription order is now being processed. Your order details are shown below for your reference:', 'yith-woocommerce-subscription' ) ), esc_html( get_option( 'blogname' ) ) ); ?></p>
<?php endif; ?>


<h2><a class="link" href="<?php echo esc_url( ywsbs_get_view_subscription_url( $subscription->get_id() ) ); ?>">
		<?php
		// translators: placeholder subscription number.
		printf( esc_html_x( 'Subscription %s', 'the placeholder is the subscription number', 'yith-woocommerce-subscription' ), esc_html( $subscription->get_number() ) );
		?>
	</a> (<?php printf( '<time datetime="%s">%s</time>', esc_html( date_i18n( 'c', time() ) ), esc_html( date_i18n( wc_date_format(), time() ) ) ); ?>)</h2>

<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
?>

<?php
do_action( 'woocommerce_email_footer', $email );
