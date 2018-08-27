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

namespace Customweb\OgoneCw\Model\Config\Source;

class CountryCheck implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			['value' => 'inactive', 'label' => __('Inactive')],
			['value' => 'all', 'label' => __('All country codes must match.')],
			['value' => 'ip_country_code_issuer_code', 'label' => __('IP country code and issuer country code must match.')],
			['value' => 'ip_country_code_billing_code', 'label' => __('IP country and billing country code must match.')],
			['value' => 'issuer_code_billing_code', 'label' => __('Issuer country code and billing country code.')],
		];
	}
}
