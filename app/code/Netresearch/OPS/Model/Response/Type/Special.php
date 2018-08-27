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

namespace Netresearch\OPS\Model\Response\Type;

/**
 * Special.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
?>
<?php

use Magento\Framework\Exception\PaymentException;

class Special extends \Netresearch\OPS\Model\Response\Type\TypeAbstract
{
    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $opsDataHelper;

    public function __construct(
        \Netresearch\OPS\Model\Config $config,
        \Netresearch\OPS\Helper\Data $opsDataHelper,
        \Netresearch\OPS\Helper\Alias $aliasHelper,
        array $data = []
    ) {
        parent::__construct($config, $aliasHelper, $data);
        $this->opsDataHelper = $opsDataHelper;
    }

    /**
     * Handles the specific actions for the concrete payment statuses
     */
    protected function _handleResponse()
    {
        if (!\Netresearch\OPS\Model\Status::isSpecialStatus($this->getStatus())) {
            throw new PaymentException(__('%1 is not a special status!', $this->getStatus()));
        }

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getMethodInstance()->getInfoInstance();
        $order = $payment->getOrder();

        if ($this->getStatus() == \Netresearch\OPS\Model\Status::WAITING_FOR_IDENTIFICATION) {
            $payment->setIsTransactionPending(true);
            $payment->setAdditionalInformation('HTML_ANSWER', $this->getHtmlAnswer());
            $order->addStatusHistoryComment(
                $this->getIntermediateStatusComment(__('Customer redirected for 3DS authorization.'))
            );
        }

        if ($this->getStatus() == \Netresearch\OPS\Model\Status::WAITING_CLIENT_PAYMENT) {
            $order->addStatusHistoryComment(
                $this->getIntermediateStatusComment(
                    __(
                        'Customer received your payment instructions, waiting for actual payment.'
                    )
                )
            );
        }

        if ($this->getStatus() == \Netresearch\OPS\Model\Status::INVALID_INCOMPLETE) {
            //save status information to order before exception
            $this->updateAdditionalInformation();
            $payment->save();

            $message = __('Ingenico ePayments status 0, the action failed.');
            if ($this->opsDataHelper->isAdminSession()) {
                $message .= ' ' . $this->getNcerror() . ' ' . $this->getNcerrorplus();
            }
            throw new PaymentException(__($message));
        }

        $order->save();
    }
}
