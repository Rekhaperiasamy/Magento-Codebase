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

namespace Customweb\OgoneCw\Block\Adminhtml\Transaction\View;

class Form extends \Customweb\OgoneCw\Block\Adminhtml\Transaction\AbstractTransaction
{
	/**
	 * Retrieve transaction order
	 *
	 * @return \Magento\Sales\Model\Order
	 */
	public function getOrder()
	{
		return $this->getTransaction()->getOrder();
	}

    /**
     * Retrieve source
     *
     * @return \Customweb\OgoneCw\Model\Authorization\Transaction
     */
    public function getSource()
    {
        return $this->getTransaction();
    }

    /**
     * Retrieve order url
     *
     * @return string
     */
    public function getOrderUrl()
    {
        return $this->getUrl('sales/order/view', ['order_id' => $this->getTransaction()->getOrderId()]);
    }

    /**
     * Retrieve formated price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
    	return $this->getTransaction()->getOrder()->formatPrice($price);
    }

    /**
     * Retrieve order info block settings
     *
     * @return array
     */
    public function getOrderInfoData()
    {
    	return [];
    }
}