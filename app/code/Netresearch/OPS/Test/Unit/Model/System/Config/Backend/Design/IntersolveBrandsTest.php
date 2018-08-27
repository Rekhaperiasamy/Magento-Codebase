<?php
namespace Netresearch\OPS\Test\Unit\Model\System\Config\Backend\Design;

    /**
     * Netresearch OPS
     * NOTICE OF LICENSE
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * http://opensource.org/licenses/osl-3.0.php
     * DISCLAIMER
     * Do not edit or add to this file if you wish to upgrade this extension to
     * newer versions in the future.
     *
     * @category    Netresearch
     * @package     Netresearch_OPS
     * @copyright   Copyright (c) 2012 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
/**
 * OPS System Config Backend Design Brands
 *
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 */
class IntersolveBrandsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\System\Config\Backend\Intersolve\Brands
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model         = $this->objectManager->getObject('\Netresearch\OPS\Model\System\Config\Backend\Intersolve\Brands');
    }

    public function testSave()
    {
        $invalidData = [
            ['brand' => '123', 'value' => '1234'],
            ['brand' => '123', 'value' => '1234']
        ];
        $this->model->setValue($invalidData);
        $this->setExpectedException('\Magento\Framework\Exception\LocalizedException', 'Brands must be unique');
        $this->model->save();
        $validData = [
            ['brand' => '123', 'value' => '1234'],
            ['brand' => '1234', 'value' => '1234']
        ];
        $this->model->setValue($validData);
        $this->assertTrue(($this->model->save() instanceof \Netresearch\OPS\Model\System\Config\Backend\Intersolve\Brands));
    }
}
