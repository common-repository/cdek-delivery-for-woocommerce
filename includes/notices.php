<?php

$errors_notices_cdek24 = '';
if(get_option('cdek24_sdek_api_account') == ''){
	$errors_notices_cdek24 .= '«API аккаунт», ';
}


if(get_option('cdek24_sdek_api_key') == ''){
	$errors_notices_cdek24 .= '«API cекретный ключ», ';
}

if(get_option('cdek24_city_code') == ''){
	$errors_notices_cdek24 .= '«Код города СДЭК», ';
}

if(get_option('cdek24_weight') == ''){
	$errors_notices_cdek24 .= '«Вес товара по умолчанию», ';
}

if(get_option('cdek24_length') == ''){
	$errors_notices_cdek24 .= '«Длина товара по умолчанию», ';
}

if(get_option('cdek24_width') == ''){
	$errors_notices_cdek24 .= '«Ширина товара по умолчанию», ';
}

if(get_option('cdek24_height') == ''){
	$errors_notices_cdek24 .= '«Высота товара по умолчанию», ';
}

if ($errors_notices_cdek24 != ''){
	add_action('admin_notices', function(){
		GLOBAL $errors_notices_cdek24;
		$errors_notices_cdek24= mb_substr($errors_notices_cdek24, 0, -2);
		$message = 'Не заданы поля: '.$errors_notices_cdek24;
		echo '<div class="notice notice-error is-dismissible"> <p><b><span style="color:red">ВНИМАНИЕ!</span> Настройка доставки СДЭК:</b> '. $message .'. Перейдите в <a href="/wp-admin/admin.php?page=wc-settings&tab=shipping&section=sdek24_global">настройки модуля</a></p></div>';
	});
}











