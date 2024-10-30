<?php
/**
 * Plugin Name: CDEK Delivery for WooCommerce
 * Description: Калькулятор стоимости доставки товара СДЭК.
 * Version: 2.0.7
 * Author: bikowskiy.com
 * Plugin URI: http://bikowskiy.com
 * Author URI: http://bikowskiy.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: 
 * Domain Path: 
 * Requires at least: 5.5
 * Requires PHP: 7.0
 * WC requires at least: 5.0
 * WC tested up to: 5.9
 */

if ( ! defined( 'WPINC' ) ) { die; }
 
// Check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	define( 'CDEK24_PL_FILE', __FILE__ );
 	

 	include 'includes/cdek_constants.php';
 	include 'includes/class-cdek24.php';
 	include 'includes/settings-admin.php';
 	include 'includes/class-req.php';
 	include 'includes/class-cdek24-shipping-method.php';
 	include 'includes/notices.php';
 	
 	add_action('wp_enqueue_scripts', 'tutsplus_enqueue_custom_js');
	function tutsplus_enqueue_custom_js() {
		wp_enqueue_script( 'jquery' );
	    wp_enqueue_script('cdek24', plugins_url('assets/js/cdek24.js', CDEK24_PL_FILE), 
	    array('jquery', 'select2'), false, true);

		// plugins_url( 'myscript.js', );
	    wp_enqueue_script('cdek24-select2', plugins_url('assets/js/select2.min.js', CDEK24_PL_FILE), 
	    array('jquery','cdek24'), false, true);
	    wp_enqueue_style( 'cdek24-billing_city_field', plugins_url('assets/css/billing_city_field.css', CDEK24_PL_FILE) );
	    
	}


	add_filter( 'plugin_action_links_' . plugin_basename( CDEK24_PL_FILE ), 'plugin_action_links');

	function plugin_action_links($links_settings) {
		$settings = array( 'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=sdek24_global' ) . '">Настройки</a>' );

		$links_settings = $settings + $links_settings;

		$links_settings['buy'] = '<a href="http://bikowskiy.com/documentation/modul-sdek-dlya-woocommerce/" target="_blank">Документация</a> <b>Купить версию <a href="http://bikowskiy.com/product/wordpress-woocommerce-cdek/" target="_blank" style="color: #3db634">CDEK Калькулятор</a>  или <a href="http://bikowskiy.com/product/cdek-kalkulyator-integraciya/" target="_blank" style="color: #3db634">CDEK Калькулятор + Интеграция</a></b>';

		return $links_settings;
	}

	function plugin_row_meta( $links, $file ) {    
	    if ( plugin_basename( CDEK24_PL_FILE ) == $file ) {
	        $row_meta = array(
	          // 'docs'    => '<a href="https://alrico.ru/docs/" target="_blank" style="color:green;">Документация</a>',
	          'support'    => '<a href="https://t.me/bikowskiy" target="_blank" style="color:green;">Поддержка</a>',
	        );

	 
	        return array_merge( $links, $row_meta );
	    }
	    return (array) $links;
	}

	add_filter( 'plugin_row_meta', 'plugin_row_meta', 10, 2 );



add_filter( 'woocommerce_billing_fields', 'cdek24_add_city_field', 25 );
 
function cdek24_add_city_field( $fields ) {
 
	// массив нового поля
	$new_field = array(
		'city_cdek24_select' => array(
			'type'          => 'select', // text, textarea, select, radio, checkbox, password
			'label' => 'Населённый пункт',
			'options'	=> array( // options for  or
				''		=> WC()->customer->get_shipping_city(), // пустое значение
			)
		)
	);


    
 	
 	$city_pos = array_search('billing_city', array_keys($fields))+1;


	$fields = array_slice( $fields, 0, $city_pos, true ) + $new_field + array_slice( $fields, $city_pos, NULL, true );
 
	return $fields;
 
}




}