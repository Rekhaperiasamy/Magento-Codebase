<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_OgoneCw
 * 
 */

namespace Customweb\OgoneCw\Model\Config\Source\Airplus;

class Currency implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			['value' => 'AED', 'label' => __('United Arab Emirates dirham (AED)')],
			['value' => 'ANG', 'label' => __('Netherlands Antillean guilder (ANG)')],
			['value' => 'ARS', 'label' => __('Argentine peso (ARS)')],
			['value' => 'AUD', 'label' => __('Australian dollar (AUD)')],
			['value' => 'AWG', 'label' => __('Aruban florin (AWG)')],
			['value' => 'BGN', 'label' => __('Bulgarian lev (BGN)')],
			['value' => 'BRL', 'label' => __('Brazilian real (BRL)')],
			['value' => 'BYR', 'label' => __('Belarusian ruble (BYR)')],
			['value' => 'CAD', 'label' => __('Canadian dollar (CAD)')],
			['value' => 'CHF', 'label' => __('Swiss franc (CHF)')],
			['value' => 'CNY', 'label' => __('Chinese yuan (CNY)')],
			['value' => 'CZK', 'label' => __('Czech koruna (CZK)')],
			['value' => 'DKK', 'label' => __('Danish krone (DKK)')],
			['value' => 'EGP', 'label' => __('Egyptian pound (EGP)')],
			['value' => 'EUR', 'label' => __('Euro (EUR)')],
			['value' => 'GBP', 'label' => __('Pound sterling (GBP)')],
			['value' => 'GEL', 'label' => __('Georgian lari (GEL)')],
			['value' => 'HKD', 'label' => __('Hong Kong dollar (HKD)')],
			['value' => 'HRK', 'label' => __('Croatian kuna (HRK)')],
			['value' => 'HUF', 'label' => __('Hungarian forint (HUF)')],
			['value' => 'ILS', 'label' => __('Israeli new shekel (ILS)')],
			['value' => 'ISK', 'label' => __('Icelandic króna (ISK)')],
			['value' => 'JPY', 'label' => __('Japanese yen (JPY)')],
			['value' => 'KRW', 'label' => __('South Korean won (KRW)')],
			['value' => 'LTL', 'label' => __('Lithuanian litas (LTL)')],
			['value' => 'LVL', 'label' => __('Latvian lats (LVL)')],
			['value' => 'MAD', 'label' => __('Moroccan dirham (MAD)')],
			['value' => 'MXN', 'label' => __('Mexican peso (MXN)')],
			['value' => 'NOK', 'label' => __('Norwegian krone (NOK)')],
			['value' => 'NZD', 'label' => __('New Zealand dollar (NZD)')],
			['value' => 'PLN', 'label' => __('Polish złoty (PLN)')],
			['value' => 'RON', 'label' => __('Romanian new leu (RON)')],
			['value' => 'RUB', 'label' => __('Russian rouble (RUB)')],
			['value' => 'SEK', 'label' => __('Swedish krona (SEK)')],
			['value' => 'SGD', 'label' => __('Singapore dollar (SGD)')],
			['value' => 'THB', 'label' => __('Thai baht (THB)')],
			['value' => 'TRY', 'label' => __('Turkish lira (TRY)')],
			['value' => 'UAH', 'label' => __('Ukrainian hryvnia (UAH)')],
			['value' => 'USD', 'label' => __('United States dollar (USD)')],
			['value' => 'XAF', 'label' => __('CFA franc BEAC (XAF)')],
			['value' => 'XOF', 'label' => __('CFA franc BCEAO (XOF)')],
			['value' => 'XPF', 'label' => __('CFP franc (XPF)')],
			['value' => 'ZAR', 'label' => __('South African rand (ZAR)')],
		];
	}
}
