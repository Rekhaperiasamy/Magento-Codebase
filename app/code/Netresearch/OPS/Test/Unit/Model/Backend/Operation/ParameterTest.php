<?php
namespace Netresearch\OPS\Test\Unit\Model\Backend\Operation;

/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch_OPS
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Parameter
     */
    private $model;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Capture\ParameterFactory
     */
    private $backendOperationCaptureParameterFactory;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Refund\ParameterFactory
     */
    private $backendOperationRefundParameterFactory;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Capture\Parameter
     */
    private $backendOperationCaptureParameter;

    /**
     * @var \Netresearch\OPS\Model\Backend\Operation\Refund\Parameter
     */
    private $backendOperationRefundParameter;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->backendOperationCaptureParameter
                             = $this->getMock(
                                 '\Netresearch\OPS\Model\Backend\Operation\Capture\Parameter',
                                 ['getDataHelper', 'getOrderHelper'],
                                 [],
                                 '',
                                 false,
                                 false
                             );
        $this->backendOperationRefundParameter
                             = $this->getMock(
                                 '\Netresearch\OPS\Model\Backend\Operation\Refund\Parameter',
                                 [],
                                 [],
                                 '',
                                 false,
                                 false
                             );
        $this->backendOperationCaptureParameterFactory
                             = $this->getMock(
                                 '\Netresearch\OPS\Model\Backend\Operation\Capture\ParameterFactory',
                                 [],
                                 [],
                                 '',
                                 false,
                                 false
                             );
        $this->backendOperationRefundParameterFactory
                             = $this->getMock(
                                 '\Netresearch\OPS\Model\Backend\Operation\Refund\ParameterFactory',
                                 [],
                                 [],
                                 '',
                                 false,
                                 false
                             );
        $this->backendOperationCaptureParameterFactory->expects($this->any())
                                                      ->method('create')
                                                      ->will($this->returnValue($this->backendOperationCaptureParameter));
        $this->backendOperationRefundParameterFactory->expects($this->any())
                                                     ->method('create')
                                                     ->will($this->returnValue($this->backendOperationRefundParameter));
        $this->model
            = $this->objectManager->getObject(
                '\Netresearch\OPS\Model\Backend\Operation\Parameter',
                [
                                                  'oPSBackendOperationCaptureParameterFactory' => $this->backendOperationCaptureParameterFactory,
                                                  'oPSBackendOperationRefundParameterFactory'  => $this->backendOperationRefundParameterFactory
                                              ]
            );
    }

    /**
     * @expectedException \Exception
     */
    public function testGetParameterForWillThrowException()
    {
        $fakePayment      = new \Magento\Framework\DataObject();
        $arrInfo          = [];
        $amount           = 0;
        $opsPaymentMethod = $this->getMock('Netresearch\OPS\Model\Payment\Cc', [], [], '', false, false);
        $this->model->getParameterFor(
            'NOT SUPPORTED OPERATION TYPE',
            $opsPaymentMethod,
            $fakePayment,
            $amount,
            $arrInfo
        );
    }
}
