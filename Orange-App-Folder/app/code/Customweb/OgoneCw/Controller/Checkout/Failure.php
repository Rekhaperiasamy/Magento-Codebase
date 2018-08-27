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

namespace Customweb\OgoneCw\Controller\Checkout;

class Failure extends \Customweb\OgoneCw\Controller\Checkout
{
	public function execute()
	{
		$transactionId = $this->getRequest()->getParam('cstrxid'); //Transaction ID for Visa, MasterCard, Bancontact 
		if (!empty($transactionId)) {
			$transaction = $this->getTransaction($transactionId);
			if ($transaction->getOrder() instanceof \Magento\Sales\Model\Order) {
				if ($transaction->getOrder()->canCancel()) {	
					$incrementId = $transaction->getOrder()->getIncrementId(); //Added for incident 39791249 coupon reactivation
					$transaction->getOrder()->cancel()->save();	
					$this->reactivateCoupon($incrementId); //Added for incident 39791249 coupon reactivation					
				}
			}
		}
		$transaction = $this->getTransaction($transactionId);
		return $this->handleFailure($transaction, $transaction->getLastErrorMessage());
	}
}