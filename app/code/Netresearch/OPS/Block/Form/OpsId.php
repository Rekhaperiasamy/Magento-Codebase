<?php
/**
 * \Netresearch\OPS\Block\Form\OpsId
 *
 * @package   OPS
 * @copyright 2012 Netresearch App Factory AG <http://www.netresearch.de>
 * @author    Thomas Birke <thomas.birke@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Block\Form;

class OpsId extends \Magento\Payment\Block\Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netresearch_OPS::ops/form/opsId.phtml');
    }
}
