<?php
/**
 * Plugin Name: Display Woocommerce Orders in Front-end
 * Plugin URI:  https://github.com/naimurhasan/woocommerce_display_orders_frontend
 * Description: One click Link folder with category of uploaded media
 * Version: 1.0
 * Author: WeblyWork
 * Author URI: https://weblywork.com/
 * Display Woocommerce Orders in Front-end is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Display Woocommerce Orders in Front-end is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function nhn_output_table_start($orders_type){
	?>
	<style type="text/css">
		.nhn_wc_display_div table,
		.nhn_wc_display_div table th,
		.nhn_wc_display_div table td,
		.nhn_wc_display_div table tr {
		    border: 2px solid #dee2e6;    
		}
	</style>
	<div class="nhn_wc_<?php echo $orders_type; ?>_orders_div nhn_wc_display_div" style="overflow-x:auto;">
	<table class="nhn_wc_<?php echo $orders_type; ?>_orders_table" style="min-width:600px">
		<thead>
			<th>Product</th>
			<th>Placed by</th>
			<th>Payment method</th>
			<th><?php echo $orders_type == 'completed' ? 'Completed' : 'Placed'; ?> on</th>
			<th>Amount</th>
		</thead>
		<tbody>
	<?php
}

function nhn_output_table_end(){
	?>
	</tbody>
	</table>
	</div>
	<?php
}

function show_orders_table($orders, $orders_type){

	foreach ($orders as $order) {
		$order_id =  $order->data['id'];
		$first_product_name = current($order->get_items())->get_name();
		$customer_name = $order->data['billing']['first_name'].' '.$order->data['billing']['last_name'];
		$pay_method = $order->data['payment_method_title'];

		$ordered_date = date_format($orders_type == 'completed' ? $order->get_date_completed() : $order->get_date_created(), 'd M, Y');

		$total_amount = $order->data['total'];
		?>

		<tr id="nhn_wc_<?php echo esc_html_e($orders_type); ?>_order_row_<?php echo esc_html_e($order_id); ?>" class="nhn_wc_<?php echo esc_html_e($orders_type); ?>_order_row">
			<td class="nhn_wc_<?php echo esc_html_e($orders_type); ?>_fpn"><?php echo esc_html_e($first_product_name); ?></td>
			<td class="nhn_wc_<?php echo esc_html_e($orders_type); ?>_cn"><?php echo esc_html_e($customer_name); ?></td>
			<td class="nhn_wc_<?php echo esc_html_e($orders_type); ?>_pm"><?php echo esc_html_e($pay_method); ?></td>
			<td class="nhn_wc_<?php echo esc_html_e($orders_type); ?>_od"><?php echo esc_html_e($ordered_date); ?></td>
			<td class="nhn_wc_<?php echo esc_html_e($orders_type); ?>_ta"><?php echo esc_html_e($total_amount); ?>/=</td>
		</tr>

		<?php
	}

}

// display a table of pending, hold, processing orders
function nhn_wc_pending_orders_func(){

	$orders = wc_get_orders( array(
    'limit'    => 5,
    'status'   => array('pending', 'on-hold', 'processing'),
	) );

	if ( count($orders) < 1 ) {
		return '<p class="nhn_wc_nothing_found">Nothing is pending! Every orders has been delivered.</p>';
	}


	nhn_output_table_start('pending');
	
	show_orders_table($orders, 'pending');

	nhn_output_table_end();
}

add_shortcode('nhn_wc_pending_orders', 'nhn_wc_pending_orders_func');

function nhn_wc_complete_orders_func(){
	$orders = wc_get_orders( array(
    'limit'    => 5,
    'status'   => array('completed'),
	) );

	if ( count($orders) < 1 ) {
		return '<p class="nhn_wc_nothing_found">No completed orders found.</p>';
	}

	nhn_output_table_start('completed');
	
	show_orders_table($orders, 'completed');

	nhn_output_table_end();
}

add_shortcode('nhn_wc_complete_orders', 'nhn_wc_complete_orders_func');
