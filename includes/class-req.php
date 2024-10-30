<?php

class CDEK24_req {
	public static function token() {
		$array = array(
	        'grant_type' => 'client_credentials',
	        'client_id'    => get_option('cdek24_sdek_api_account'),
	        'client_secret' => get_option('cdek24_sdek_api_key')
	    );  


		$url = 'https://api.cdek.ru/v2/oauth/token?parameters';
		$args = array(
			'timeout'     => 45,
			'httpversion' => '1.0',
			'blocking'    => true,
			'body'    => $array,
			'cookies' => array()
		);
		$response = wp_remote_post( $url, $args );
		$html = json_decode($response['body'], true);
		$access_token = $html['access_token'];

		return $access_token;
	}

	public static function calc($instance_settings, $total_wwlh, $destination) {

		$access_token = self::token();

		// $ch_c = curl_init('https://api.cdek.ru/v2/calculator/tariff');
		$field_city = explode(' ( Регион: ', $destination['city']);
		$field_city = str_replace(' ', '%20', $field_city[0]);
        $get_city = file_get_contents(get_site_url().'/wp-content/plugins/cdek-delivery-for-woocommerce/assets/js/city-code.php?term[term]='.$field_city);
        $get_city = json_decode($get_city, true);
        
        if(count($get_city) > 0 && array_key_exists('id', $get_city[0])){
        	$get_city = $get_city[0]['id'];
        }else{
        	$get_city = '0';
        }
		$data = array(
			'tariff_code'  => $instance_settings['tariffes'],
			'from_location' =>  array(
				'code' => get_option('cdek24_city_code'), 
			),
			'to_location' => array(
				'code' => $get_city,
			),
			'packages' => array(
				'weight' => $total_wwlh['weight'], 
				'length' => $total_wwlh['length'],
				'width' => $total_wwlh['width'],
				'height' => $total_wwlh['height']
			)
		);	 


		$url = 'https://api.cdek.ru/v2/calculator/tariff';

		$args = array(
			
			'timeout'     => 45,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers' => array(
				'Authorization' => 'Bearer '.$access_token, 
				'Content-Type' => 'application/json'
			),
			'body'    => json_encode($data, JSON_UNESCAPED_UNICODE),
			'cookies' => array()
		);
		$response = wp_remote_post( $url, $args );


		return json_decode($response['body'], true);
	}


		



		

}
	
