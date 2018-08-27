<?php
/**
 * \Netresearch\OPS\Model\Payment\FortisPayButton
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class FortisPayButton extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'Fortis Pay Button';
    protected $brand = 'Fortis Pay Button';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_fortisPayButton';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
