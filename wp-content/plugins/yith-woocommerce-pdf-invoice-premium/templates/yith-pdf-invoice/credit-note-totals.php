<?php
/**
 * Override this template by copying it to [your theme folder]/woocommerce/yith-pdf-invoice
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var WC_Order $current_order */
/** @var YITH_Document $document */

$current_order   = $document->order;
$parent_order   = wc_get_order( $current_order->get_parent_id() );

$invoice_details = new YITH_Invoice_Details( $document );

$total_section_color = get_option('ywpi_total_section_color');

$negative_value = get_option( 'ywpi_credit_note_positive_values', 'no' )  == 'yes' ? '1' : '-1'  ;

if ( $current_order->get_total() == ($parent_order->get_total() * -1) ){
	$order_subtotal =  $parent_order->get_subtotal() * $negative_value;
	$order_tax_total = $parent_order->get_total_tax() * $negative_value;
	$order_total = $parent_order->get_total() * $negative_value;
}
else{
	$order_subtotal =  $current_order->get_subtotal();
	$order_tax_total = $current_order->get_total_tax();
	$order_total = $current_order->get_total();
}




?>

<?php if ( ywpi_is_visible_order_totals( $document ) ) : ?>
	<div class="document-totals">
		<table class="invoice-totals">
			<tr class="invoice-details-subtotal">
				<td class="left-content column-product"><?php esc_html_e( "Subtotal", 'yith-woocommerce-pdf-invoice' ); ?></td>
				<td class="right-content column-total"><?php echo $invoice_details->get_order_currency_new( $order_subtotal ); ?>
                </td>
			</tr>

            <?php if ( wc_tax_enabled() ) : ?>
                <tr class="invoice-details-vat">
                    <td class="left-content column-product"><?php esc_html_e( "Tax total", 'yith-woocommerce-pdf-invoice' ); ?></td>
                    <td class="right-content column-total"><?php echo $invoice_details->get_order_currency_new( $order_tax_total ); ?></td>
                </tr>
            <?php endif; ?>

			<?php do_action( 'yith_pdf_invoice_before_total', $current_order ); ?>

			<tr class="invoice-details-total">
				<td class="left-content column-product" style="background-color: <?php echo $total_section_color;?>"><?php esc_html_e( "Total", 'yith-woocommerce-pdf-invoice' ); ?></td>
				<td class="right-content column-total" style="background-color: <?php echo $total_section_color;?>"><?php echo $invoice_details->get_order_currency_new( $order_total ); ?></td>
			</tr>
		</table>
	</div>
<?php endif; ?>
