<?php
/**
 * \Netresearch\OPS\Model\Payment\PostFinanceEFinance
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Model\Payment;

class PostFinanceEFinance extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $pm = 'PostFinance e-finance';
    protected $brand = 'PostFinance e-finance';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** payment code */
    protected $_code = 'ops_postFinanceEFinance';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';
}
