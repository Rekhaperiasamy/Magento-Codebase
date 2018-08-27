<?php
namespace Netresearch\OPS\Model\Payment;

/**
 * \Netresearch\OPS\Model\Payment\ApresReception
 *
 * @package
 * @copyright 2013 Netresearch
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license   OSL 3.0
 */
class KwixoComptant extends \Netresearch\OPS\Model\Payment\Kwixo\KwixoAbstract
{
    protected $pm = 'KWIXO_STANDARD';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /**
     * @var string
     */
    protected $_formBlockType = 'Netresearch\OPS\Block\Form\Kwixo\Comptant';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Redirect';

    /** payment code */
    protected $_code = 'ops_kwixoComptant';
}
