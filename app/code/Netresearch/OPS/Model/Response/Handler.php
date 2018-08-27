<?php
/**
 * Netresearch_OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Model\Response;

/**
 * Handler.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */

class Handler
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Handler constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $responseArray
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $paymentMethod
     * @param bool $shouldRegisterFeedback determines if the \Magento\Sales\Model\Order\Payment register*Feedback
     *                                     functions get called, defaults to true
     */
    public function processResponse(
        $responseArray,
        \Netresearch\OPS\Model\Payment\PaymentAbstract $paymentMethod,
        $shouldRegisterFeedback = true
    ) {
        $responseArray = array_change_key_case($responseArray, CASE_LOWER);
        return $this->getTypeHandler($responseArray['status'])
            ->handleResponse($responseArray, $paymentMethod, $shouldRegisterFeedback);
    }

    /**
     * @param $status
     *
     * @return \Netresearch\OPS\Model\Response\TypeInterface
     * @throws \Exception
     */
    protected function getTypeHandler($status)
    {
        $type = null;

        if (\Netresearch\OPS\Model\Status::isCapture($status)) {
            $type = \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_CAPTURE_TRANSACTION_TYPE;
        } elseif (\Netresearch\OPS\Model\Status::isRefund($status)) {
            $type = \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_REFUND_TRANSACTION_TYPE;
        } elseif (\Netresearch\OPS\Model\Status::isVoid($status)) {
            $type = \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_VOID_TRANSACTION_TYPE;
        } elseif (\Netresearch\OPS\Model\Status::isAuthorize($status)) {
            $type = \Netresearch\OPS\Model\Payment\PaymentAbstract::OPS_AUTHORIZE_TRANSACTION_TYPE;
        } elseif (\Netresearch\OPS\Model\Status::isSpecialStatus($status)) {
            $type = 'special';
        } else {
            throw new \Magento\Framework\Exception\PaymentException(__('Can not handle status %1.', $status));
        }

        $typeClass = "\\Netresearch\\OPS\\Model\\Response\\Type\\" . ucfirst($type);

        return $this->objectManager->create($typeClass);
    }
}
