<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Form;

class Ideal extends \Magento\Payment\Block\Form
{

    /**
     * @var \Netresearch\OPS\Model\Payment\IDealFactory
     */
    protected $oPSPaymentIDealFactory;

    public function __construct(
        \Netresearch\OPS\Model\Payment\IDealFactory $oPSPaymentIDealFactory
    ) {
        $this->oPSPaymentIDealFactory = $oPSPaymentIDealFactory;
    }
    /**
     * Init OPS payment form
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netresearch_OPS::ops/form/ideal.phtml');
    }

    /**
     * return the ideal issuers
     *
     * @return array
     */
    public function getIssuers()
    {
        return $this->oPSPaymentIDealFactory->create()->getIDealIssuers();
    }
}
