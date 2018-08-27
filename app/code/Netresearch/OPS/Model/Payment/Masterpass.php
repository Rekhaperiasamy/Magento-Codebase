<?php

namespace Netresearch\OPS\Model\Payment;

/**
 * @package
 * @copyright 2016 Netresearch
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license   OSL 3.0
 */
class Masterpass extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'MasterPass';
    protected $brand = 'MasterPass';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_Masterpass';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
