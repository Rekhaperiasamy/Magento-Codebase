<?php
/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\ClosedTransaction;

/**
 * @author     Michael Lühr <michael.luehr@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Warning extends \Magento\Backend\Block\Template
{
    /**
     * Internal constructor, that is called from real constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netresearch_OPS::ops/sales/order/creditmemo/closed-transaction/warning.phtml');
    }
}
