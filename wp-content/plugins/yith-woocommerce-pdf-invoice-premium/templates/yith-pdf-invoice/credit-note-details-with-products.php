<?php
/**
 * The Template for invoice details
 *
 * Override this template by copying it to [your theme]/woocommerce/invoice/ywpi-invoice-details.php
 *
 * @author        Yithemes
 * @package       yith-woocommerce-pdf-invoice-premium/Templates
 * @version       1.0.0
 */

/** @var YITH_Document $document */

$invoice_details = new YITH_Invoice_Details( $document );
/** @var WC_Order_Refund $order */

$order   = $document->order;

$parent_order   = wc_get_order( $order->get_parent_id() );
$invoice_details = new YITH_Invoice_Details( $document );

$table_header_color = get_option('ywpi_table_header_color');
$table_header_font_color = get_option('ywpi_table_header_font_color');

$has_suborder = $order->get_meta( 'has_sub_order' );

$order_items = $order->get_items();

$negative_symbol = get_option( 'ywpi_credit_note_positive_values', 'no' )  == 'yes' ? '' : '-'  ;

?>

<table class="invoice-details credit-note-product-table">
	<thead style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>">
	<tr style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>">


		<?php if ( ywpi_is_enabled_column_picture ( $document ) ) : ?>
			<th class="column-picture" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"></th>
		<?php endif;?>

		<th class="column-product" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Product', 'yith-woocommerce-pdf-invoice' ); ?></th>

		<th class="column-refund-text" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Description', 'yith-woocommerce-pdf-invoice' ); ?></th>

		<?php if ( ywpi_is_enabled_column_quantity ( $document ) ) : ?>
			<th class="column-quantity" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Qty', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_regular_price ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Regular price', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_sale_price ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Sale price', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_product_price ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Product price', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_percentage ( $document ) ) : ?>
			<th class="column-discount-percentage" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Discount percentage', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_line_total ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Line total', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_tax ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Tax', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_percentage_tax ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Percentage tax', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>

		<?php if ( ywpi_is_enabled_column_total_taxed ( $document ) ) : ?>
			<th class="column-price" style="background-color: <?php echo $table_header_color; ?>; color:<?php echo $table_header_font_color; ?>"><?php esc_html_e( 'Total (inc. tax)', 'yith-woocommerce-pdf-invoice' ); ?></th>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<?php



	//FULL REFUND:
	if ( $order->get_total() == ($parent_order->get_total() * -1) ){

		foreach ( $parent_order->get_items() as $item_id => $item ) {

			$_product = $invoice_details->get_item_product ( $item );

			?>
			<tr>
				<!-- Show picture if related option is enabled -->
				<?php if ( ywpi_is_enabled_column_picture( $document ) ): ?>
					<td class="column-picture">
						<?php $image_path = apply_filters( 'yith_ywpi_image_path', $invoice_details->get_product_image( $item ), $_product );
						if ( $image_path ): ?>
							<img class="product-image" src="<?php echo $image_path; ?>"/>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<td class="column-product">
					<!-- Show product title -->
					<?php echo apply_filters( 'woocommerce_order_product_title', $item['name'], $_product ); ?>
					<br>

					<?php if ( ywpi_is_enabled_column_variation( $document ) ) : ?>
						<?php echo urldecode( $invoice_details->get_variation_text( $item_id, $_product ) ); ?>
						<br>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_sku( $document ) ) : ?>
						<?php echo $invoice_details->get_sku_text( $item, $item_id ); ?>
					<?php endif; ?>


					<?php if ( ywpi_is_enabled_column_short_description( $document ) && ( $invoice_details->get_short_description( $item, $item_id ) != '' ) ) : ?>
						<div
							class="product-short-description"><?php echo $invoice_details->get_short_description( $item, $item_id ); ?></div>
					<?php endif; ?>

					<?php do_action( 'yith_ywpi_column_product_after_content', $document, $_product, $item_id ); ?>
				</td>

				<td class="column-refund-text">

					<?php
					$refund_text = get_option( 'ywpi_credit_note_refund_text', '' );
					echo $refund_text ? $refund_text : esc_html__( 'Your refund', 'yith-woocommerce-pdf-invoice' );

					if ( ywpi_is_enabled_credit_note_reason_column( $document ) ) : ?>
						<br>
						<i><?php echo $order->get_reason(); ?></i>
					<?php endif; ?>
				</td>

				<?php if ( ywpi_is_enabled_column_quantity( $document ) ) : ?>
					<td class="column-quantity">
						<?php echo ( isset( $item['qty'] ) ) ? esc_html( $negative_symbol . $item['qty'] ) : ''; ?>
					</td>
				<?php endif; ?>


				<?php if ( ywpi_is_enabled_column_regular_price( $document ) ) : ?>
					<td class="column-price">
						<?php echo $negative_symbol . wc_price( $invoice_details->get_item_product_regular_price( $item, $item_id ) ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_sale_price( $document ) ) : ?>
					<td class="column-price">
						<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit_sale( $item ) ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_product_price( $document ) ) : ?>
					<td class="column-price">
						<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit( $item ) ); ?>
					</td>
				<?php endif; ?>


				<?php if ( ywpi_is_enabled_column_percentage( $document ) ) : ?>
					<td class="column-discount-percentage">
						<?php echo $invoice_details->get_item_percentage_discount( $item ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_line_total( $document ) ) : ?>
					<td class="column-price">
						<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $item["line_total"] ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_tax( $document ) ) : ?>
					<td class="column-price">
						<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $item["line_tax"] ); ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_percentage_tax( $document ) && isset( $item['line_tax'] ) && isset( $item['line_total'] ) ) : ?>
					<td class="column-price">
						<?php if ( $item['line_total'] != 0 && $item['line_total'] != '' ):

							$tax_percentage = $item['line_tax'] * 100 / $item['line_total'];

							$precision = '0';

							echo round( $tax_percentage, $precision ) . '%'; ?>

						<?php else: ?>
							<?php echo '0%'; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<?php if ( ywpi_is_enabled_column_total_taxed( $document ) ) : ?>
					<td class="column-price">
						<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $item["line_tax"] + $item["line_total"] ); ?>
					</td>
				<?php endif; ?>
			</tr>

			<?php
		}
	}

	//PARTIAL REFUNDS:

	else{
		/** @var WC_Product $_product */
		foreach ( $order_items as $item_id => $item ) {

			$_product = $invoice_details->get_item_product ( $item );
			$item_qty_refunded = $item->get_quantity();
			$item_value_refunded = $item->get_total();

			//Partial Refunds:
			?>
			<tr>
				<!-- Show picture if related option is enabled -->
				<?php if ( ywpi_is_enabled_column_picture ( $document ) ): ?>
					<td class="column-picture">
						<?php $image_path = apply_filters ( 'yith_ywpi_image_path', $invoice_details->get_product_image ( $item ), $_product );
						if ( $image_path ): ?>
							<img class="product-image" src="<?php echo $image_path; ?>" />
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<td class="column-product">
					<!-- Show product title -->
					<?php
					$item_name = apply_filters ( 'woocommerce_order_item_name', $item['name'], $_product );
					$item_name = apply_filters ( 'woocommerce_order_product_title', $item_name, $_product );

					echo $item_name;
					?>
					<br>

					<?php if ( ywpi_is_enabled_column_variation ( $document ) ) : ?>
						<?php echo urldecode($invoice_details->get_variation_text ( $item_id, $_product )); ?>
						<br>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_sku ( $document ) ) : ?>
						<?php echo $invoice_details->get_sku_text ( $item, $item_id ); ?>
					<?php endif; ?>


					<?php if ( ywpi_is_enabled_column_short_description ( $document ) && ( $invoice_details->get_short_description( $item, $item_id) != '' ) ) : ?>
						<div class="product-short-description"><?php echo $invoice_details->get_short_description( $item, $item_id); ?></div>
					<?php endif; ?>

					<?php do_action ( 'yith_ywpi_column_product_after_content', $document, $_product, $item_id ); ?>
				</td>

				<td class="column-refund-text">

					<?php
					$refund_text = get_option( 'ywpi_credit_note_refund_text', '' );
					echo $refund_text ? $refund_text : esc_html__( 'Your refund', 'yith-woocommerce-pdf-invoice' );

					if ( ywpi_is_enabled_credit_note_reason_column( $document ) ) : ?>
						<br>
						<i><?php echo $order->get_reason(); ?></i>
					<?php endif; ?>
				</td>

				<?php if ( $item_qty_refunded != 0 ) : ?>

					<?php if ( ywpi_is_enabled_column_quantity ( $document ) ) : ?>
						<td class="column-quantity">
							<?php echo $item_qty_refunded; ?>
						</td>
					<?php endif; ?>


					<?php if ( ywpi_is_enabled_column_regular_price ( $document ) ) : ?>
						<td class="column-price">
							<?php echo wc_price( $invoice_details->get_item_product_regular_price ( $item, $item_id ) ); ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_sale_price ( $document ) ) : ?>
						<td class="column-price">
							<?php echo $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit_sale ( $item ) ); ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_product_price ( $document ) ) : ?>
						<td class="column-price">
							<?php echo $invoice_details->get_order_currency_new( $invoice_details->get_item_price_per_unit ( $item ) ); ?>
						</td>
					<?php endif; ?>


					<?php if ( ywpi_is_enabled_column_percentage ( $document ) ) : ?>
						<td class="column-discount-percentage">
							<?php echo $invoice_details->get_item_percentage_discount ( $item ); ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_line_total ( $document ) ) : ?>
						<td class="column-price">
							<?php

							echo wc_price( $item->get_total() ); ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_tax ( $document ) ) : ?>
						<td class="column-price">
							<?php echo wc_price( $item->get_total_tax() ); ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_percentage_tax ( $document ) && isset($item['line_tax']) && isset($item['line_total']) ) : ?>
						<td class="column-price">
							<?php if( $item->get_total() != 0 && $item->get_total() != '' ):

								$tax_percentage = $item->get_total_tax() * 100 / $item->get_total();

								$precision = '0';

								echo round( $tax_percentage, $precision ) . '%'; ?>

							<?php else: ?>
								<?php echo '0%'; ?>
							<?php endif; ?>
						</td>
					<?php endif; ?>



					<?php if ( ywpi_is_enabled_column_total_taxed ( $document ) ) : ?>
						<td class="column-price">
							<?php echo  wc_price( $item->get_total() + $item->get_total_tax() ); ?>
						</td>
					<?php endif; ?>
				<?php endif; ?>
			</tr>

			<?php


		} // foreach;
	}


	if ( $order->get_total() == ($parent_order->get_total() * -1) ){
		if ( apply_filters ( 'ywpi_is_visible_fee_details_section', true, $document ) ) :

			foreach ( $invoice_details->get_order_fees () as $item_id => $item ) {
				?>

				<tr class="border-top">
					<?php if ( ywpi_is_enabled_column_picture ( $document ) ) : ?>
						<td class="column-picture">
						</td>
					<?php endif; ?>

					<td class="column-product">
						<?php echo ! empty( $item['name'] ) ? esc_html ( $item['name'] ) : esc_html__( 'Fee', 'yith-woocommerce-pdf-invoice' ); ?>
					</td>

					<?php if ( ywpi_is_enabled_column_quantity ( $document ) ) : ?>
						<td class="column-quantity">
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_product_price ( $document ) ) : ?>
						<td class="column-price">
							<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $item['line_total'] ); ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_regular_price ( $document ) ) : ?>
						<td class="column-price">
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_sale_price ( $document ) ) : ?>
						<td class="column-price">
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_percentage ( $document ) ) : ?>
						<td class="column-discount-percentage"></td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_line_total ( $document ) ) : ?>
						<td class="column-price">
							<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $item['line_total'] ); ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_tax ( $document ) ) : ?>
						<td class="column-price">
							<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $item['line_tax'] ); ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_percentage_tax ( $document ) && isset($item['line_tax']) && isset($item['line_total']) ) : ?>
						<td class="column-price">
							<?php if( $item['line_total'] != 0 && $item['line_total'] != '' ):

								$tax_percentage = $item['line_tax'] * 100 / $item['line_total'];

								echo round( $tax_percentage, 0 ) . '%'; ?>


							<?php else: ?>
								<?php echo '0%'; ?>
							<?php endif; ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_total_taxed ( $document ) ) : ?>
						<td class="column-price">
							<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $item["line_tax"] + $item["line_total"] ); ?>
						</td>
					<?php endif; ?>

				</tr>

				<?php
			}   // foreach
		endif;

		if ( apply_filters ( 'ywpi_is_visible_shipping_details_section', true, $document ) ) :

			foreach ( $invoice_details->get_order_shipping () as $item_id => $item ) {

				?>

				<tr>
					<?php if ( ywpi_is_enabled_column_picture ( $document ) ) : ?>
						<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
						<td class="column-picture">
						</td>
					<?php endif; ?>

					<td class="column-product">
						<?php echo ! empty( $item['name'] ) ? esc_html ( $item['name'] ) : esc_html__( 'Shipping', 'yith-woocommerce-pdf-invoice' ); ?>
					</td>

					<td class="column-refund-text">
					</td>

					<?php if ( ywpi_is_enabled_column_quantity ( $document ) ) : ?>
						<td class="column-quantity">
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_product_price ( $document ) ) : ?>

						<td class="column-price">
							<?php echo $negative_symbol . ( isset( $item['cost'] ) ) ? $invoice_details->get_order_currency_new( wc_round_tax_total ( $item['cost']) ) : ''; ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_regular_price ( $document ) ) : ?>
						<td class="column-price">
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_sale_price ( $document ) ) : ?>
						<td class="column-price">
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_percentage ( $document ) ) : ?>
						<td class="column-discount-percentage"></td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_line_total ( $document ) ) : ?>
						<td class="column-price">
							<?php echo ( isset( $item['cost'] ) ) ? $negative_symbol . $invoice_details->get_order_currency_new( $item['cost'] ) : ''; ?>
						</td>
					<?php endif; ?>

					<?php

					if ( ywpi_is_enabled_column_tax ( $document ) ) : ?>
						<td class="column-price">
							<?php
							echo $negative_symbol . ( $invoice_details->get_order_currency_new( wc_round_tax_total ( $invoice_details->get_item_shipping_taxes ( $item ) ) ) );
							?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_percentage_tax ( $document ) && isset($item['cost']) ) : ?>
						<td class="column-price">
							<?php if( $item['cost'] != 0 && $item['cost'] != '' ):

								$tax_percentage = ( ( $invoice_details->get_item_shipping_taxes ( $item ) ) * 100 ) / $item["cost"];

								$precision = '0';

								echo round( $tax_percentage, $precision ) . '%'; ?>

							<?php else: ?>
								<?php echo '0%'; ?>
							<?php endif; ?>
						</td>
					<?php endif; ?>

					<?php if ( ywpi_is_enabled_column_total_taxed ( $document ) ) : ?>
						<td class="column-price">
							<?php echo $negative_symbol . $invoice_details->get_order_currency_new( $item["cost"] + $invoice_details->get_item_shipping_taxes ( $item ) ); ?>
						</td>
					<?php endif; ?>
				</tr>
				<?php
			};
		endif;
	}

	?>
	</tbody>
</table>
