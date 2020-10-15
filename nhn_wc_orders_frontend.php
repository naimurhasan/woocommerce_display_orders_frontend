<?php
/**
 * Plugin Name: Display WC Orders in Front-end
 * Plugin URI:  https://github.com/naimurhasan/woocommerce_display_orders_frontend
 * Description: Display Woocommerce Orders in Front-End by weblywork.com
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
	$th = $orders_type == 'completed' ? 'Completed' : 'Placed';
	$output = <<<DATA
	<style type="text/css">
		.nhn_wc_display_div table,
		.nhn_wc_display_div table th,
		.nhn_wc_display_div table td,
		.nhn_wc_display_div table tr \{
		    border: 2px solid #dee2e6;    
		}
	</style>
	<div class="nhn_wc_{$orders_type}_orders_div nhn_wc_display_div" style="overflow-x:auto;">
	<table class="nhn_wc_{$orders_type}_orders_table" style="min-width:600px">
		<thead>
			<th>Product</th>
			<th>Placed by</th>
			<th>Payment method</th>
			<th>{$th} on</th>
			<th>Amount</th>
		</thead>
		<tbody>
DATA;
return $output;
}

function nhn_output_table_end(){
	$output = <<<DATA
	</tbody>
	</table>
	</div>
DATA;
return $output;
}

function show_orders_table($orders, $orders_type){

	$output = '';
	foreach ($orders as $order) {

			// run your code here
			try{
				$order_id =  $order->data['id'];
				$first_product_name = current($order->get_items())->get_name();
				$customer_name = $order->data['billing']['first_name'].' '.$order->data['billing']['last_name'];
				$pay_method = $order->data['payment_method_title'];

				$ordered_date = date_format($orders_type == 'completed' ? $order->get_date_completed() : $order->get_date_created(), 'd M, Y');

				$total_amount = $order->data['total'].'/=';

				$order_id =  $order->data['id'];
				$first_product_name = current($order->get_items())->get_name();
				$customer_name = $order->data['billing']['first_name'].' '.$order->data['billing']['last_name'];
				$pay_method = $order->data['payment_method_title'];

				$ordered_date = date_format($orders_type == 'completed' ? $order->get_date_completed() : $order->get_date_created(), 'd M, Y');

				$total_amount = $order->data['total'].'/=';

			} catch(Error $e){
				$order_id =  rand(4651, 9653);
				$first_product_name = '(Hidden)';
				$customer_name = 'GUEST';
				$pay_method = 'bKash';
				$ordered_date = date('d M, Y');
				$total_amount = '(Hidden)';

			}
				
		$output .= <<<DATA

		<tr id="nhn_wc_{$orders_type}_order_row_{$order_id}; ?>" class="nhn_wc_{$orders_type}_order_row">
			<td class="nhn_wc_{$orders_type}_fpn">{$first_product_name}</td>
			<td class="nhn_wc_{$orders_type}_cn">{$customer_name}</td>
			<td class="nhn_wc_{$orders_type}_pm">{$pay_method}</td>
			<td class="nhn_wc_{$orders_type}_od">{$ordered_date}</td>
			<td class="nhn_wc_{$orders_type}_ta">{$total_amount}</td>
		</tr>
DATA;

		
	}
	return $output;
}


// display a table of pending, hold, processing orders
function nhn_wc_pending_orders_func($atts){

	extract(shortcode_atts(array(
		'limit' => -1,
	), $atts));
  
	  $orders = wc_get_orders( array(
		'limit'    => $limit,
		'status'   => array('on-hold', 'processing'),
	) );
	
	if ( count($orders) < 1 ) {
		return '<p class="nhn_wc_nothing_found">Nothing is pending! Every orders has been delivered.</p>';
	}

	$output = '';
	$output .= nhn_output_table_start('pending');
	$output .= show_orders_table($orders, 'pending');
	$output .= nhn_output_table_end();
	return $output;
}

add_shortcode('nhn_wc_pending_orders', 'nhn_wc_pending_orders_func');


// display a table of complete
function nhn_wc_complete_orders_func($atts){

	extract(shortcode_atts(array(
		'limit' => -1,
	), $atts));
  
	  $orders = wc_get_orders( array(
		'limit'    => $limit,
		'status'   => array('completed'),
	) );
	
	if ( count($orders) < 1 ) {
		return '<p class="nhn_wc_nothing_found">Nothing is pending! Every orders has been delivered.</p>';
	}

	
	$output = '';
	$output .=  nhn_output_table_start('complete');
	$output .=  show_orders_table($orders, 'completed');
	$output .=  nhn_output_table_end();
	return $output;
}

add_shortcode('nhn_wc_complete_orders', 'nhn_wc_complete_orders_func');
