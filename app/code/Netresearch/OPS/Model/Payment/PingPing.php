<?php
/**
 * \Netresearch\OPS\Model\Payment\PingPing
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class PingPing extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'PingPing';
    protected $brand = 'PingPing';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_pingPing';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
