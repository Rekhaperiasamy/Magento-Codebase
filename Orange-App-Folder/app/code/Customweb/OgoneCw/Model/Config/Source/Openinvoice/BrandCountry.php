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

namespace Customweb\OgoneCw\Model\Config\Source\Openinvoice;

class BrandCountry implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			['value' => 'at', 'label' => __('Austria (AT)')],
			['value' => 'ch', 'label' => __('Switzerland (CH)')],
			['value' => 'de', 'label' => __('Germany (DE)')],
			['value' => 'dk', 'label' => __('Denmark (DK)')],
			['value' => 'fi', 'label' => __('Finland (FI)')],
			['value' => 'nl', 'label' => __('Netherlands (NL)')],
			['value' => 'no', 'label' => __('Norway (NO)')],
			['value' => 'se', 'label' => __('Sweden (SE)')],
		];
	}
}