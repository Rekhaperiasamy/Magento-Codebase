<?php

namespace Netresearch\OPS\Test\Unit\Block\Form\Kwixo;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ApresReceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netresearch\OPS\Block\Form\Kwixo\ApresReception
     */
    protected $block = null;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject('Netresearch\OPS\Block\Form\Kwixo\ApresReception');
    }

    public function testGetTemplate()
    {
        $this->assertEquals('Netresearch_OPS::ops/form/kwixo/apres_reception.phtml', $this->block->getTemplate());
    }

    public function testGetPmLogo()
    {
        $this->assertEquals('Netresearch_OPS::images/kwixo/apres_reception.jpg', $this->block->getPmLogo());
    }
}
