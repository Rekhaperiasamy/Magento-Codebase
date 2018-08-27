<?php
namespace Netresearch\OPS\Test\Unit\Model\System\Config\Backend\Flex;

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
     * @copyright Copyright (c) 2016 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
     * @license   Open Software License (OSL 3.0)
     * @link      http://opensource.org/licenses/osl-3.0.php
     */
/**
 * MethodsTest.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
class MethodsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\System\Config\Backend\Flex\Methods
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model
                             = $this->objectManager->getObject('\Netresearch\OPS\Model\System\Config\Backend\Flex\Methods');
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Can not save empty title or PM fields
     */
    public function testSaveWithEmptyException()
    {
        $this->model->setValue($this->getEmpty());
        $this->model->save();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage PM and Brand combination must be unique
     */
    public function testSaveWithDuplicateException()
    {
        $this->model->setValue($this->getDuplicate());
        $this->model->save();
    }

    public function testSave()
    {
        $this->model = $this->getMock(
            '\Netresearch\OPS\Model\System\Config\Backend\Flex\Methods',
            ['_getResource'],
            [],
            '',
            false,
            false
        );
        $this->model->expects($this->once())
                    ->method('_getResource')
                    ->will($this->returnValue($this->getMockForAbstractClass(
                        '\Magento\Framework\Model\ResourceModel\Db\AbstractDb',
                        [],
                        '',
                        false,
                        false,
                        false,
                        ['save']
                    )));
        $this->model->setValue([$this->getSimpleData()]);
        $this->model->save();
    }

    protected function getDuplicate()
    {
        return [
            $this->getSimpleData(),
            $this->getSimpleData()
        ];
    }

    protected function getEmpty()
    {
        return [
            $this->getSimpleData(),
            [
                'title' => '',
                'brand' => '',
                'pm'    => ''
            ]
        ];
    }

    protected function getSimpleData()
    {
        return [
            'title' => 'foo',
            'brand' => 'bar',
            'pm'    => 'zzz'
        ];
    }
}
