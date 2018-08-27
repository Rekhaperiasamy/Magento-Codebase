<?php
namespace Netresearch\OPS\Test\Unit\Model\Source\Cc;

    /**
     * Netresearch_OPS
     * NOTICE OF LICENSE
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * http://opensource.org/licenses/osl-3.0.php
     * DISCLAIMER
     * Do not edit or add to this file if you wish to upgrade this extension to
     * newer versions in the future.
     *
     * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
     * @license   Open Software License (OSL 3.0)
     * @link      http://opensource.org/licenses/osl-3.0.php
     */
/**
 * RecurringTypesTest.php
 *
 * @category Payment provider
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
class TypesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Source\Cc\Types
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->config        = new \Magento\Framework\DataObject(['all_cc_types' => ['VISA']]);
        $configFactory       = $this->getMock('Netresearch\OPS\Model\ConfigFactory', [], [], '', false, false);
        $configFactory->expects($this->any())->method('create')->will($this->returnValue($this->config));
        $this->model = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\Source\Cc\Types',
            ['oPSConfigFactory' => $configFactory]
        );
    }

    public function testToOptionArray()
    {
        $this->assertContains(['label' => 'VISA', 'value' => 'VISA'], $this->model->toOptionArray());
    }
}
