<?php
GLOBAL $delivery_tariffes;

add_filter( 'woocommerce_get_sections_shipping', 'sdek24_add_global_settings' );
function sdek24_add_global_settings( $sections ) {
 
	$sections[ 'sdek24_global' ] = 'Настройка доставки СДЭК';
	return $sections;
 
}

add_filter( 'woocommerce_get_settings_shipping', 'sdek24_global_settings', 25, 2 );
 
function sdek24_global_settings( $settings, $current_section ) {
 
	// тут у нас проверка секции, в которой находимся
	if ( 'sdek24_global' == $current_section ) {
 
		$new_settings = array();
 		
 		$new_settings[] = array(
			'name' => 'Настройки',
			'type' => 'title',
			'id' => 'cdek24_settings_main'
		);

		$new_settings[] = array(
			'name' => 'API аккаунт',
			'id' => 'cdek24_sdek_api_account',
			'type' => 'text',
			'desc' => 'Если у вас нет учетных данных API, вы можете получить их, отправив запрос по адресу integrator@cdek.ru. В запросе вы должны указать свой номер договора с компанией СДЭК и электронную почту для получения ключей и оповещений от API интеграции.',
		);
	

		$new_settings[] = array(
			'id' => 'cdek24_sdek_api_key',
			'name' => 'API cекретный ключ',
			'type' => 'text',

			'desc' => 'Если у вас нет учетных данных API, вы можете получить их, отправив запрос по адресу integrator@cdek.ru. В запросе вы должны указать свой номер договора с компанией СДЭК и электронную почту для получения ключей и оповещений от API интеграции.',
		);

		$new_settings[] = array(
			'id' => 'cdek24_type_dogovor',
			'name' => 'Тип договора',
			'type' => 'select',
			'options'       =>  array(
				'1' => 'Интернет-магазин'
			),
			'desc'    => '«Интернет-магазин» - только для клиента с типом договора «Интернет-магазин». «Доставка» может быть создана любым клиентом с договором (но тарифы доступны только для обычной доставки).<span style="color:#7a0000"> «Доставка» доступна начиная с версии <a href="https://alrico.ru/product/plugin-cdek-for-woocommerce/" target="_blank" style="color: #7a0000">CDEK Калькулятор</a>.</span>',
			'required' => true,
		);

		$new_settings[] = array(
			'id' => 'cdek24_city_code',
			'name' => 'Код города СДЭК',
			'type' => 'text',
			'desc'    => 'Введите здесь код вашего города. Узнать код Вы можете на странице нашей <a href="http://bikowskiy.com/doc/kody-gorodov/" target="_blank">документации</a>.<br><br>
				Несколько кодов для популярных городов:
				<table>
					<thead>
						<th style="padding: 0px 10px; width: auto;">Код</th>
						<th style="padding: 0px 10px; border-right: 1px solid; width: auto;">Город</th>
						<th style="padding: 0px 10px; width: auto;">Код</th>
						<th style="padding: 0px 10px; width: auto;">Город</th>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px 10px;">44</td>
							<td style="padding: 0px 10px; border-right: 1px solid">Москва</td>
							<td style="padding: 0px 10px;">137</td>
							<td style="padding: 0px 10px;">Санкт-Петербург</td>
						</tr>
						<tr>
							<td style="padding: 0px 10px;">270</td>
							<td style="padding: 0px 10px; border-right: 1px solid">Новосибирск</td>
							<td style="padding: 0px 10px;">250</td>
							<td style="padding: 0px 10px;">Екатеринбург</td>
						</tr>
						<tr>
							<td style="padding: 0px 10px;">437</td>
							<td style="padding: 0px 10px; border-right: 1px solid">Сочи</td>
							<td style="padding: 0px 10px;">435</td>
							<td style="padding: 0px 10px;">Краснодар</td>
						</tr>
					</tbody>
				</table>',
			'required' => true,
		);

		$new_settings[] = array(
			'id' => 'cdek24_shipment_point',
			'name' => 'Код пункта отгрузки СДЭК',
			'type' => 'text',
			'desc'    => CDEK24_sdek24::beginning_light_pro_text().'Требуется для тарифов "от склада". Пример кода отгрузки: "SPB290". Вы можете найти код для своего города на <a href="https://www.cdek.ru/ru/offices" target="_blank">официальном сайте</a>.',
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_from_location_address',
			'name' => 'Адрес офиса/склада',
			'type' => 'text',
			'desc'    => CDEK24_sdek24::beginning_light_pro_text().'Для создания заказа в СДЭК. Обязательно, если заказ с тарифом "от двери"',
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_api_yandex_key',
			'name' => 'Ключ api яндекс карт',
			'type' => 'text',
			'desc' => CDEK24_sdek24::beginning_light_pro_text().'Для оторбражения пунктов выдачи. Ключ api яндекс карт нужен для отображения карты с пунктами СДЭК. <br>Узнать как получить ключ по <a href="https://yandex.ru/dev/maps/jsapi/doc/2.1/quick-start/index.html#get-api-key" target="_blank">ссылке</a>',
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_select2',
			'name' => 'Отключить выпадающий список городов',
			'type' => 'select',
			'options'       =>  array(
				'0' => 'Нет', 
				'1' => 'Да',
			),
			'desc' => CDEK24_sdek24::beginning_pro_text().'<br>Может потребоваться в случае интеграции с другими способами доставки',
			'required' => false,
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		$new_settings[] = array(
			'type' => 'sectionend',
		);

		$new_settings[] = array(
			'id' => 'cdek24_box_title',
		 	'name'       => "Параметры упаковки и габаритов товара",
		 	'type'        => 'title',
		 	'desc' => 'Габариты товара влияют на стоимость доставки во время расчета и необходимы для создания заказа в личном кабинете. Если фактический заказ покупателя не будет подходить под заданные размеры упаковки, вы всегда сможете изменить их перед отправкой заказа на личный кабинет СДЭК.'
		);

		$new_settings[] = array(
			'id' => 'cdek24_weight',
			'name' => 'Вес товара по умолчанию (в граммах)',
			'type' => 'number',
			'desc' => 'Для товаров, у которых не задан вес, будет учитываться значение из этой  строки.  (Если иное не задано в методе доставки)',
			'custom_attributes' => array('min' => '0'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_length',
			'name' => 'Длина товара по умолчанию (в сантиметрах) ',
			'type' => 'number',
			'desc' => 'Для товаров, у которых не задана длина, будет учитываться значение из этой  строки. (Если иное не задано в методе доставки)',
			'custom_attributes' => array('min' => '0'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_width',
			'name' => 'Ширина товара по умолчанию. (в сантиметрах) ',
			'type' => 'number',
			'desc' => 'Для товаров, у которых не задана ширина, будет учитываться значение из этой  строки. (Если иное не задано в методе доставки)',
			'custom_attributes' => array('min' => '0'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_height',
			'name' => 'Высота товара по умолчанию (в сантиметрах)',
			'type' => 'number',
			'desc' => 'Для товаров, у которых не задана высота, будет учитываться значение из этой  строки. (Если иное не задано в методе доставки)',
			'custom_attributes' => array('min' => '0'),
		);

		$new_settings[] = array(
			'type' => 'sectionend',
		);


		$new_settings[] = array(
			'id' => 'cdek24_sender_title',
		 	'name'       => 'Отправитель',
		 	'type'        => 'title',
		 	'desc' => 'Обязательно, если заказ типа "доставка"'
		);

		$new_settings[] = array(
			'id' => 'cdek24_sender_company',
			'name' => 'Название компании',
			'type' => 'text',
			'desc' => CDEK24_sdek24::beginning_pro_text().'',
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_sender_name',
			'name' => 'ФИО контактного лица',
			'type' => 'text',
			'desc' => CDEK24_sdek24::beginning_pro_text().'',
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_sender_email',
			'name' => 'Эл. адрес',
			'type' => 'text',
			'desc' => CDEK24_sdek24::beginning_pro_text().'',
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		$new_settings[] = array(
			'id' => 'cdek24_phones_number',
			'name' => 'Номер телефона',
			'type' => 'text',
			'desc' => CDEK24_sdek24::beginning_pro_text().'Должен передаваться в международном формате: код страны (для России +7) и сам номер (10 и более цифр)<br>Можно  указать до 10 номеров, разделив запятой без пробелов',
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		$new_settings[] = array(
			'type' => 'sectionend',
		);

		$new_settings[] = array(
			'id' => 'cdek24_order_title',
		 	'name'       => 'Заказ',
		 	'type'        => 'title',
		 	'desc' => ''
		);

		$new_settings[] = array(
			'id' => 'cdek24_additional_tariff_3',
			'name' => 'Создавать заказ в СДЭК',
			'type' => 'select',
			'options'       =>  array(
				'0' => 'Нет', 
				'1' => 'Да',
			),
			'desc' => CDEK24_sdek24::beginning_pro_text().'',
			'required' => false,
			'custom_attributes' => array('disabled' => 'disabled'),
		);

		

		$new_settings[] = array(
			'type' => 'sectionend',
		);
		return $new_settings;
 
	// какая-то другая вкладка? Окей, возвращаем те опции, которые уже есть
	} else {
		return $settings;
	}
}

    


			 
			    