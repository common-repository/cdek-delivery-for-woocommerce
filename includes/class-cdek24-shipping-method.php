<?php
function is_pro_active()
{
    return true;
}

add_action('woocommerce_shipping_init', 'cdek24_shipping_method');
function cdek24_shipping_method()
{
    if (!class_exists('WC_cdek24_Shipping_Method'))
    {
        class WC_cdek24_Shipping_Method extends WC_Shipping_Method
        {

            public function __construct($instance_id = 0)
            {
                $this->instance_id = absint($instance_id);
                $this->id = 'cdek24'; //this is the id of our shipping method
                $this->method_title = 'Доставка СДЭК';
                $this->method_description = 'Способ доставки через СДЭК';
                //add to shipping zones list
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                );
                //make it always enabled
                $this->title = 'Способ доставки через СДЭК';
                $this->enabled = 'yes';
                $this->init();
            }

            function init()
            {
                // Load the settings API
                $this->init_form_fields();
                $this->init_settings();

                foreach ($this->instance_form_fields as $key => $settings)
                {
                    $this->{$key} = $this->get_option($key);
                }

                // Save settings in admin if you have any defined
                add_action('woocommerce_update_options_shipping_' . $this->id, array(
                    $this,
                    'process_admin_options'
                ));
            }

            //Fields for the settings page
            function init_form_fields()
            {
                global $delivery_tariffes;
                global $additional_services;
                //fileds for the modal form from the Zones window
                $this->instance_form_fields = array(
                    // заголовк 1
                    'title_method' => array(
                        'title' => 'Название',
                        'type' => 'text',
                        'description' => 'Этот  текст покупатель видит при оформлении  заказа',
                        'default' => 'Доставка через СДЭК'
                    ) ,

                    'tariffes' => array(
                        'title' => 'Тариф',
                        'type' => 'select',
                        'class' => array(
                            'my-field-class form-row-wide'
                        ) ,
                        'options' => $delivery_tariffes,
                        'description' => (CDEK24_sdek24::is_pro_light() ? '' : '<span style="color:#7a0000">Другие тарифы доступны начиная с версиии <a href="http://bikowskiy.com/product/wordpress-woocommerce-cdek/" target="_blank" style="color: #7a0000">CDEK Калькулятор.</a>.</span><br><br>') . '<b>Обратите внимание!</b> Не все тарифы доступны для определенных направлений. Вы можете  проверить доступность тарифов на официальном калькулляторе СДЭК: https://cdek.ru/calculate ',
                        'required' => true,
                    ) ,

                    'send_to_sdek_title' => array(
                        'title' => "Настройка отправляемых данных в СДЭК",
                        'type' => 'title',
                    ) ,
                    'additional_services' => array(
                        'type' => 'multiselect',
                        'title' => 'Дополнительные услуги',
                        'description' => CDEK24_sdek24::beginning_pro_text() . 'Услуга "СТРАХОВАНИЕ" начисляется автоматически для всех заказов типа "интернет-магазин", не разрешена для самостоятельной передачи в поле.',
                        'options' => $additional_services,
                        'custom_attributes' => array(
                            'disabled' => 'disabled'
                        ) ,
                    ) ,

                    'box_title' => array(
                        'title' => "Параметры упаковки и габаритов товара",
                        'type' => 'title',
                        'description' => 'Габариты товара влияют на стоимость доставки во время расчета и необходимы для создания заказа в личном кабинете. Если фактический заказ покупателя не будет подходить под заданные размеры упаковки, вы всегда сможете изменить их перед отправкой заказа на личный кабинет СДЭК.'
                    ) ,

                    'weight' => array(
                        'title' => 'Вес товара по умолчанию <br> (в граммах)',
                        'type' => 'number',
                        'description' => 'Для товаров, у которых не задан вес, будет учитываться значение из этой  строки, или из глобальных настроек плагина.',
                        'custom_attributes' => array(
                            'min' => '0'
                        ) ,
                    ) ,

                    'length' => array(
                        'title' => 'Длина товара по умолчанию <br>(в сантиметрах) ',
                        'type' => 'number',
                        'description' => 'Для товаров, у которых не задана длина, будет учитываться значение из этой  строки, или из глобальных настроек плагина.',
                        'custom_attributes' => array(
                            'min' => '0'
                        ) ,
                    ) ,

                    'width' => array(
                        'title' => 'Ширина товара по умолчанию<br>(в сантиметрах) ',
                        'type' => 'number',
                        'description' => 'Для товаров, у которых не задана ширина, будет учитываться значение из этой  строки, или из глобальных настроек плагина.',
                        'custom_attributes' => array(
                            'min' => '0'
                        ) ,
                    ) ,

                    'height' => array(
                        'title' => 'Высота товара по умолчанию<br>(в сантиметрах) ',
                        'type' => 'number',
                        'description' => 'Для товаров, у которых не задана высота, будет учитываться значение из этой  строки, или из глобальных настроек плагина.',
                        'custom_attributes' => array(
                            'min' => '0'
                        ) ,
                    ) ,

                    'delivery_time_title' => array(
                        'title' => "Срок доставки",
                        'type' => 'title'
                    ) ,

                    'show_delivery_time' => array(
                        'title' => 'Показывать срок доставки?',
                        'label' => 'Да',
                        'type' => 'checkbox',
                        'description' => CDEK24_sdek24::beginning_light_pro_text() ,
                        'custom_attributes' => array(
                            CDEK24_sdek24::is_pro_light() ? '' : 'disabled' => '',
                        ) ,
                    ) ,

                    'add_delivery_time' => array(
                        'title' => 'Добавить дни к сроку доставки',
                        'type' => 'number',
                        'description' => CDEK24_sdek24::beginning_light_pro_text() . 'Функция может быть полезна, если вы хотите заложить срок сбора заказа.',
                        'custom_attributes' => array(
                            'min' => '0'
                        ) ,
                        'custom_attributes' => array(
                            CDEK24_sdek24::is_pro_light() ? '' : 'disabled' => '',
                        ) ,
                    ) ,

                    'delivery_price' => array(
                        'title' => "Стоимость доставки",
                        'type' => 'title'
                    ) ,

                    'additional_price' => array(
                        'title' => 'Добавить к стоимости доставки',
                        'type' => 'number',
                        'description' => CDEK24_sdek24::beginning_light_pro_text() . 'Значение со знаком "-" (минус) - вычесть из доставки указанную сумму<br>Значение "0" (минус) - Бесплатная доставка',
                        'custom_attributes' => array(
                            'min' => '0'
                        ) ,
                        'custom_attributes' => array(
                            CDEK24_sdek24::is_pro_light() ? '' : 'disabled' => '',
                        ) ,
                    ) ,

                    'additional_price_percent' => array(
                        'title' => 'Добавить к стоимости доставки в процентах (%)',
                        'type' => 'number',
                        'description' => CDEK24_sdek24::beginning_light_pro_text() . 'Значение со знаком "-" (минус) - вычесть указанный процент из суммы доставки<br>Если поле задано - Поле "Добавить к стоимости доставки" игнорируется',
                        'custom_attributes' => array(
                            'min' => '-100',
                            'max' => '100'
                        ) ,
                        'custom_attributes' => array(
                            CDEK24_sdek24::is_pro_light() ? '' : 'disabled' => '',
                        ) ,
                    ) ,

                    'free_of_the_amount' => array(
                        'title' => 'Бесплатная доставка от суммы',
                        'type' => 'number',
                        'description' => CDEK24_sdek24::beginning_light_pro_text() ,
                        'custom_attributes' => array(
                            'min' => '0'
                        ) ,
                        'custom_attributes' => array(
                            CDEK24_sdek24::is_pro_light() ? '' : 'disabled' => '',
                        ) ,
                    ) ,

                    'additional_tariff' => array(
                        'title' => "Дополнительные тарифы",
                        'type' => 'title'
                    ) ,

                    'additional_tariff_1' => array(
                        'title' => 'Дополнительный Тариф 1',
                        'type' => 'select',
                        'class' => array(
                            'my-field-class form-row-wide'
                        ) ,
                        'options' => $delivery_tariffes,
                        'description' => CDEK24_sdek24::beginning_light_pro_text() . 'Установите дополнительный тариф, если основной не может быть рассчитан для региона покупателя.',
                        'required' => false,
                        'custom_attributes' => array(
                            CDEK24_sdek24::is_pro_light() ? '' : 'disabled' => '',
                        ) ,
                    ) ,

                    'additional_tariff_2' => array(
                        'title' => 'Дополнительный Тариф 2',
                        'type' => 'select',
                        'class' => array(
                            'my-field-class form-row-wide'
                        ) ,
                        'options' => $delivery_tariffes,
                        'description' => CDEK24_sdek24::beginning_light_pro_text() . 'Установите дополнительный тариф, если Дополнительный Тариф 1 не может быть рассчитан для региона покупателя.',
                        'required' => false,
                        'custom_attributes' => array(
                            CDEK24_sdek24::is_pro_light() ? '' : 'disabled' => '',
                        ) ,
                    ) ,

                    'additional_tariff_3' => array(
                        'title' => 'Дополнительный Тариф 3',
                        'type' => 'select',
                        'class' => array(
                            'my-field-class form-row-wide'
                        ) ,
                        'options' => $delivery_tariffes,
                        'description' => CDEK24_sdek24::beginning_light_pro_text() . 'Установите дополнительный тариф, если Дополнительный Тариф 2 не может быть рассчитан для региона покупателя.',
                        'required' => false,
                        'custom_attributes' => array(
                            CDEK24_sdek24::is_pro_light() ? '' : 'disabled' => '',
                        ) ,
                    ) ,

                    // Дополнительная стоимость в процентах light
                    // Дополнительная стоимость
                    // от чего считать процент light
                    // Фиксированная стоимость доставки light
                    // доставка равна 0 light
                    // Условия бесплатной доставки
                    // Доополнительный тариф 1 light
                    // Дополнительный тариф 2 light
                    

                    
                );
                //$this->form_fields - use this with the same array as above for setting fields for separate settings page
                
            }

            public function calculate_shipping($package = array())
            {
                $instance_settings = $this->instance_settings;

                $total_wwlh = $this->get_wwlh($package['contents'], $instance_settings);

                $calc = CDEK24_req::calc($instance_settings, $total_wwlh, $package['destination']);



                $titile_delivery = $instance_settings['title_method'];

                $erorr_text = '';

                if (array_key_exists('errors', $calc))
                {
                    if ($calc['errors'][0]['code'] == 'ERR_INVALID_TARIFF_WITH_DIMENTIONS')
                    {
                        $erorr_text = $calc['errors'][0]['message'];
                    }
                    elseif ($calc['errors'][0]['code'] == 'v2_bad_request')
                    {
                        $erorr_text = $calc['errors'][0]['message'];
                    }
                    elseif ($calc['errors'][0]['code'] == 'VE_CALC_WEIGHT')
                    {
                        $erorr_text = $calc['errors'][0]['message'];
                    }
                    elseif ($calc['errors'][0]['message'] == '[to_location.code] is empty')
                    {
                        $erorr_text = '(Не выбран город)';
                    }elseif($calc['errors'][0]['code'] == 'ERR_RECCITYCODE'){
                        $erorr_text = '(Не удалось определить город)';
                    }
                    else
                    {
                        if (array_key_exists('text', $calc['errors'][0])){
                            $erorr_text = $calc['errors'][0]['text'];
                        }else{
                            $erorr_text = 'Не удалось расчитать доставку';
                        }
                        
                    }
                }

                if (array_key_exists('delivery_sum', $calc))
                {
                    $amount_delivery = $calc['delivery_sum'];
                }
                else
                {
                    $amount_delivery = '';
                }

                if ($amount_delivery)
                {
                    $time_delivery = ' (' . $calc['period_min'] . '-' . $calc['period_max'] . ' дн.)';
                }
                else
                {
                    $time_delivery = '';
                }

                $this->add_rate(array(
                    'id' => $this->get_rate_id() ,
                    'label' => $titile_delivery . $time_delivery . ' ' . $erorr_text,
                    'cost' => $amount_delivery,
                    'package' => $package,
                    'taxes' => false,
                    'meta_data' => array(
                        'cdek24_tariff' => '123'
                    ) ,
                ));
            }

            public function get_wwlh($package, $instance_settings)
            {
                // weight width length height
                $total_weight = 0;
                $total_length = 0;
                $total_width = 0;
                $total_height = 0;

                foreach ($package as $orderline)
                {

                    // weight
                    $product_weight = $orderline['data']->get_weight();
                    if ($product_weight == 0 || $product_weight == '')
                    {
                        if ($instance_settings['weight'] != '' && $instance_settings['weight'] != 0)
                        {
                            $total_weight += (int)$instance_settings['weight'];
                        }
                        else
                        {
                            $total_weight += (int)get_option('cdek24_weight');
                        }
                    }
                    else
                    {
                        $total_weight += $product_weight * 1000;
                    }

                    // length
                    $product_length = $orderline['data']->get_length();
                    if ($product_length == 0 || $product_length == '')
                    {
                        if ($instance_settings['length'] != '' && $instance_settings['length'] != 0)
                        {
                            $product_length = (int)$instance_settings['length'];
                        }
                        else
                        {
                            $product_length = (int)get_option('cdek24_length');
                        }
                    }
                    if ($product_length > $total_length)
                    {
                        $total_length = $product_length;
                    }

                    // width
                    $product_width = $orderline['data']->get_width();
                    if ($product_width == 0 || $product_width == '')
                    {
                        if ($instance_settings['width'] != '' && $instance_settings['width'] != 0)
                        {
                            $product_width = (int)$instance_settings['width'];
                        }
                        else
                        {
                            $product_width = (int)get_option('cdek24_width');
                        }
                    }
                    if ($product_width > $total_width)
                    {
                        $total_width = $product_width;
                    }

                    // height
                    $product_height = $orderline['data']->get_height();
                    if ($product_height == 0 || $product_height == '')
                    {
                        if ($instance_settings['height'] != '' && $instance_settings['height'] != 0)
                        {
                            $product_height = (int)$instance_settings['height'];
                        }
                        else
                        {
                            $product_height = (int)get_option('cdek24_height');
                        }
                    }
                    $total_height += (int)$product_height * (int)$orderline['quantity'];
                }

                $total = array();
                $total['weight'] = $total_weight;
                $total['length'] = $total_length;
                $total['width'] = $total_width;
                $total['height'] = $total_height;
                return ($total);
            }

            public function get_current_tarriff()
            {
                $chosen_methods = WC()
                    ->session
                    ->get('chosen_shipping_methods', array());
                $chosen_methods_id = explode(':', $chosen_methods[0]) [1];
                $shipping_class_names = WC()
                    ->shipping
                    ->get_shipping_method_class_names();
                $method_instance = new $shipping_class_names['cdek24']($chosen_methods_id);
                $field_value = $method_instance->get_option('tariffes');
                return $field_value;
            }

        }
    }

    //add your shipping method to WooCommers list of Shipping methods
    add_filter('woocommerce_shipping_methods', 'add_cdek24_shipping_method');
    function add_cdek24_shipping_method($methods)
    {
        $methods['cdek24'] = 'WC_cdek24_Shipping_Method';
        return $methods;
    }

}

