<?php
/**
 * \Netresearch\OPS\Model\Payment\IngHomePay
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class IngHomePay extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'ING HomePay';
    protected $brand = 'ING HomePay';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_ingHomePay';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
