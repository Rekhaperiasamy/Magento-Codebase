<?php
/**
 * \Netresearch\OPS\Model\Payment\EDankort
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class EDankort extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'eDankort';
    protected $brand = 'eDankort';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_eDankort';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
