<?php
/**
 * \Netresearch\OPS\Model\Payment\DexiaDirectNet
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class BelfiusDirectNet extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'Belfius Direct Net';
    protected $brand = 'Belfius Direct Net';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_belfiusDirectNet';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
