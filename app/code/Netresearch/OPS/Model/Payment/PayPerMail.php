<?php

namespace Netresearch\OPS\Model\Payment;

/**
 * PayPerMail.php
 *
 * @author    sebastian.ertner@netresearch.de
 * @copyright Copyright (c) 2017 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 */
class PayPerMail extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    const CODE = 'ops_payPerMail';

    const INFO_KEY_TITLE = 'paypermail_title';
    const INFO_KEY_PM    = 'paypermail_pm';
    const INFO_KEY_BRAND = 'paypermail_brand';


    /** payment code */
    protected $_code = self::CODE;

    /**
     * @var bool
     */
    protected $_canUseInternal = true;

    /**
     * @var bool
     */
    protected $_canUseCheckout = false;

    /**
     * @param null $payment
     * @return string
     */
    public function getOpsCode($payment = null)
    {
        return $this->getInfoInstance()->getAdditionalInformation(self::INFO_KEY_PM);
    }

    /**
     * @param null $payment
     * @return string
     */
    public function getOpsBrand($payment = null)
    {
        return $this->getInfoInstance()->getAdditionalInformation(self::INFO_KEY_BRAND);
    }
}
