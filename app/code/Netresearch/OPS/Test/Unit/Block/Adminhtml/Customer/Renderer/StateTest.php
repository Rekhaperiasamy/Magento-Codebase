<?php

namespace Netresearch\OPS\Test\Unit\Block\Adminhtml\Customer\Renderer;

class StateTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $column = new \Magento\Framework\DataObject();
        $column->setIndex('state');
        $row = new \Magento\Framework\DataObject();
        $row->setData(['state' => \Netresearch\OPS\Model\Alias\State::ACTIVE]);
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var \Netresearch\OPS\Block\Adminhtml\Customer\Renderer\State $block */
        $block = $objectManager->getObject('Netresearch\OPS\Block\Adminhtml\Customer\Renderer\State');
        $block->setColumn($column);
        $this->assertEquals(__(\Netresearch\OPS\Model\Alias\State::ACTIVE), $block->render($row));
    }
}
