<?php

class CDEK24_sdek24 {

	public static function is_pro_light() {
		return false;
	}

	public static function beginning_light_pro_text() {
		return self::is_pro_light() ? '' : '<span style="color:#7a0000">Доступно начиная с версии <a href="http://bikowskiy.com/product/cdek-kalkulyator-integraciya/" target="_blank" style="color: #7a0000">CDEK Калькулятор + Интеграция</a></span><br>';
	}

	public static function beginning_pro_text() {
		return self::is_pro_light() ? '' : '<span style="color:#7a0000">Доступно начиная с версии <a href="http://bikowskiy.com/product/cdek-kalkulyator-integraciya/" target="_blank" style="color: #7a0000">CDEK Калькулятор + Интеграция</a></span><br>';
	}
}