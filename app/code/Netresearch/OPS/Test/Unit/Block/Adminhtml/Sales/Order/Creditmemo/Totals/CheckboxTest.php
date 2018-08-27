<?php

namespace Netresearch\OPS\Test\Unit\Block\Adminhtml\Sales\Order\Creditmemo\Totals;

class CheckboxTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTemplate()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var \Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\Totals\Checkbox $block */
        $block = $objectManager->getObject('Netresearch\OPS\Block\Adminhtml\Sales\Order\Creditmemo\Totals\Checkbox');
        $this->assertEquals('Netresearch_OPS::ops/sales/order/creditmemo/totals/checkbox.phtml', $block->getTemplate());
    }
}
