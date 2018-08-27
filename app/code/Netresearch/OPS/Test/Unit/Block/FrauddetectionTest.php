<?php

namespace Netresearch\OPS\Test\Unit\Block;

class FrauddetectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTrackingCodeAid()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var \Netresearch\OPS\Block\frauddetection $block */
        $block = $objectManager->getObject('Netresearch\OPS\Block\Frauddetection');
        $this->assertEquals('10376', $block->getTrackingCodeAid());
    }
}
