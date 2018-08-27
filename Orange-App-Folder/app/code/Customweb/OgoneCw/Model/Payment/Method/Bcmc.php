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

namespace Customweb\OgoneCw\Model\Payment\Method;

class Bcmc extends AbstractMethod
{
	const PAYMENT_METHOD_CODE = 'ogonecw_bcmc';
	const PAYMENT_METHOD_NAME = 'bcmc';

	/**
	 * Payment method code
	 *
	 * @var string
	 */
	protected $_code = self::PAYMENT_METHOD_CODE;

	/**
	 * Payment method name
	 *
	 * @var string
	 */
	protected $_name = self::PAYMENT_METHOD_NAME;

	/**
	 * Payment Method features
	 */
	protected $_isGateway = true;
	protected $_isInitializeNeeded = true;
	protected $_canAuthorize = true;
	protected $_canReviewPayment = true;
	protected $_canCapture = true;
	protected $_canCapturePartial = true;
	protected $_canRefund = false;
	protected $_canRefundInvoicePartial = false;
	protected $_canVoid = true;
	protected $_canUseInternal = true;
}
