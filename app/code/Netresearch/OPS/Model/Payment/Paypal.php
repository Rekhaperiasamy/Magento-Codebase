<?php
/**
 * \Netresearch\OPS\Model\Payment\Paypal
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class Paypal extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'PAYPAL';
    protected $brand = 'PAYPAL';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_paypal';

    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
