<?php
/**
 * \Netresearch\OPS\Model\Payment\GiroPay
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class GiroPay extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'giropay';
    protected $brand = 'giropay';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_giroPay';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
