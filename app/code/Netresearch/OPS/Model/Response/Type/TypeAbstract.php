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

use Netresearch\OPS\Model\Payment\PaymentAbstract;
use Netresearch\OPS\Model\Response\TypeInterface;

/**
 * Abstract.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */
?>
<?php

/**
 * @codingStandardsIgnoreStart
 *
 * Class \Netresearch\OPS\Model\Response\Type\TypeAbstract
 *
 * @method \Netresearch\OPS\Model\Payment\PaymentAbstract getMethodInstance()
 * @method \Netresearch\OPS\Model\Response\Type\TypeAbstract setMethodInstance(\Netresearch\OPS\Model\Payment\PaymentAbstract $instance)
 * @method int getStatus()
 * @method int getPayid()
 * @method bool hasPayidsub()
 * @method int getPayidsub()
 * @method float getAmount()
 * @method string getCurrency()
 * @method string getOrderid()
 * @method string getAavaddress()
 * @method string getAavcheck()
 * @method string get Aavzip()
 * @method string getAcceptance()
 * @method string getBin()
 * @method string getBrand()
 * @method string getCardno()
 * @method string getCccty()
 * @method string getCn()
 * @method string getCvccheck()
 * @method string getScoring()
 * @method bool hasScoring()
 * @method string getScoCategory()
 * @method bool hasScoCategory()
 * @method string getShasign()
 * @method string getSubbrand()
 * @method string getTrxdate()
 * @method string getVc()
 * @method int getNcstatus()
 * @method string getNcerror()
 * @method string getNcerrorplus()
 * @method string getHtmlAnswer()
 * @method string getIpcty()
 * @method bool hasAcceptance()
 * @method bool hasBrand()
 * @method bool hasMobilemode()
 * @method bool hasAlias()
 * @method bool getShouldRegisterFeedback() if feedback should get registered on payment object
 * @method \Netresearch\OPS\Model\Response\Type\TypeAbstract setShouldRegisterFeedback($shouldRegisterFeedback)
 *
 * @codingStandardsIgnoreEnd
 */
abstract class TypeAbstract extends \Magento\Framework\DataObject implements TypeInterface
{
    /**
     * @var \Netresearch\OPS\Model\Config
     */
    protected $config;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $aliasHelper;

    /**
     * TypeAbstract constructor.
     * @param \Netresearch\OPS\Model\Config $config
     * @param \Netresearch\OPS\Helper\Alias $aliasHelper
     * @param array $data
     */
    public function __construct(
        \Netresearch\OPS\Model\Config $config,
        \Netresearch\OPS\Helper\Alias $aliasHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->config = $config;
        $this->aliasHelper = $aliasHelper;
    }

    /**
     * @return \Netresearch\OPS\Model\Config
     */
    public function getConfig()
    {
        if (null === $this->getData('config')) {
            $this->setData('config', $this->config);
        }

        return $this->getData('config');
    }

    /**
     * Performs the necessary actions for Magento to progress the order state correctly and automatically build the
     * create sales objects
     *
     * @param array $responseArray
     * @param \Netresearch\OPS\Model\Payment\PaymentAbstract $paymentMethod
     * @param bool $shouldRegisterFeedback determines if the \Magento\Sales\Model\Order\Payment register*Feedback
     *                                     functions get called, defaults to true
     *
     * @return TypeInterface
     */
    public function handleResponse(
        $responseArray,
        \Netresearch\OPS\Model\Payment\PaymentAbstract $paymentMethod,
        $shouldRegisterFeedback = true
    ) {
        $this->setData(array_change_key_case($responseArray, CASE_LOWER));
        $this->setMethodInstance($paymentMethod);
        $this->setShouldRegisterFeedback($shouldRegisterFeedback);

        if ($this->getStatus() == $this->getMethodInstance()->getInfoInstance()->getAdditionalInformation('status')
            && $this->getTransactionId() == $paymentMethod->getInfoInstance()->getLastTransId()
        ) {
            return $this;
        }

        $this->setGeneralTransactionInfo();
        $this->_handleResponse();
        $this->updateAdditionalInformation();

        if ($this->getShouldRegisterFeedback() && $this->hasAlias()
            && $this->config->isAliasManagerEnabled(
                $this->getMethodInstance()->getCode(),
                $this->getMethodInstance()->getStore()
            )
        ) {
            $this->aliasHelper->saveAlias($responseArray);
        }

        return $this;
    }

    /**
     * Handles the specific actions for the concrete payment status
     */
    abstract protected function _handleResponse();

    /**
     * Updates the additional information of the payment info object
     *
     * @see \Netresearch\OPS\Model\Response\Type\Abstract::updateDefaultInformation
     * @see \Netresearch\OPS\Model\Response\Type\Abstract::setFraudDetectionParameters
     */
    protected function updateAdditionalInformation()
    {
        $this->updateDefaultInformation();
        $this->setFraudDetectionParameters();
        $this->setDeviceInformationParameters();
    }

    /**
     * Updates default information in additional information of the payment info object
     */
    protected function updateDefaultInformation()
    {
        $payment = $this->getMethodInstance()->getInfoInstance();

        $payment->setAdditionalInformation('paymentId', $this->getPayid())
            ->setAdditionalInformation('status', $this->getStatus());

        if ($this->hasAlias()) {
            $payment->setAdditionalInformation('alias', $this->getAlias());
        }

        if ($this->hasAcceptance()) {
            $payment->setAdditionalInformation('acceptence', $this->getAcceptance());
        }

        if ($this->hasBrand() && $this->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\Cc) {
            $payment->setAdditionalInformation('CC_BRAND', $this->getBrand());
        }
    }

    /**
     * Sets Transaction details (TransactionId etc.)
     */
    protected function setGeneralTransactionInfo()
    {
        $payment = $this->getMethodInstance()->getInfoInstance();

        $payment->setTransactionParentId($this->getPayid());
        $transId = $this->getTransactionId();

        $payment->setLastTransId($transId);
        $payment->setTransactionId($transId);
        $payment->setIsTransactionClosed(false);
    }

    /**
     * Updates fraud detection information on additional information of the payment info object
     */
    protected function setFraudDetectionParameters()
    {
        $payment = $this->getMethodInstance()->getInfoInstance();
        if ($this->hasScoring()) {
            $payment->setAdditionalInformation('scoring', $this->getScoring());
        }

        if ($this->hasScoCategory()) {
            $payment->setAdditionalInformation('scoringCategory', $this->getScoCategory());
        }

        $additionalScoringData = [];
        foreach ($this->getConfig()->getAdditionalScoringKeys() as $key) {
            if ($this->hasData(strtolower($key))) {
                if (false === mb_detect_encoding($this->getData(strtolower($key)), 'UTF-8', true)) {
                    $additionalScoringData[$key] = utf8_encode($this->getData(strtolower($key)));
                } else {
                    $additionalScoringData[$key] = $this->getData(strtolower($key));
                }
            }
        }

        $payment->setAdditionalInformation('additionalScoringData', serialize($additionalScoringData));
    }

    /**
     * Set default device information to additional data
     *
     * @throws \Magento\Framework\Exception\PaymentException
     */
    protected function setDeviceInformationParameters()
    {
        if (!$this->getMethodInstance() instanceof \Netresearch\OPS\Model\Payment\Bancontact) {
            return;
        }

        $payment = $this->getMethodInstance()->getInfoInstance();
        if ($this->hasMobilemode()) {
            $payment->setAdditionalInformation('MOBILEMODE', $this->getMobilemode());
        }
    }

    /**
     * @param string $orderComment
     * @param string $additionalInfo
     *
     * @return string
     * @throws \Magento\Framework\Exception\PaymentException
     */
    protected function addOrderComment($orderComment, $additionalInfo = '')
    {

        $orderComment = $this->getOrderComment($orderComment, $additionalInfo);
        $this->getMethodInstance()->getInfoInstance()->getOrder()->addStatusHistoryComment($orderComment);
    }

    /**
     * Add order comment about final status
     *
     * @param string $additionalInfo
     *
     * @throws \Magento\Framework\Exception\PaymentException
     */
    protected function addFinalStatusComment($additionalInfo = '')
    {
        $this->addOrderComment($this->getFinalStatusComment($additionalInfo));
    }

    /**
     * Add order comment about intermediate status
     *
     * @param string $additionalInfo
     *
     * @throws \Magento\Framework\Exception\PaymentException
     */
    protected function addIntermediateStatusComment($additionalInfo = '')
    {
        $this->addOrderComment($this->getIntermediateStatusComment($additionalInfo));
    }

    /**
     * Add order comment about refused status
     *
     * @param string $additionalInfo
     *
     * @throws \Magento\Framework\Exception\PaymentException
     */
    protected function addRefusedStatusComment($additionalInfo = '')
    {
        $this->addOrderComment($this->getRefusedStatusComment($additionalInfo));
    }

    /**
     * Add order comment about fraud status
     *
     * @param string $additionalInfo
     *
     * @throws \Magento\Framework\Exception\PaymentException
     */
    protected function addFraudStatusComment($additionalInfo = '')
    {
        $this->addOrderComment($this->getFraudStatusComment($additionalInfo));
    }

    /**
     * @param string $additionalInfo
     *
     * @return string
     */
    protected function getFinalStatusComment($additionalInfo = '')
    {
        $orderComment = __(
            'Received Ingenico ePayments feedback status update with final status %1.',
            $this->getStatus()
        );
        return $this->getOrderComment($orderComment, $additionalInfo);
    }

    /**
     * @param string $additionalInfo
     *
     * @return string
     */
    protected function getIntermediateStatusComment($additionalInfo = '')
    {
        $orderComment = __(
            'Received Ingenico ePayments feedback status update with intermediate status %1.',
            $this->getStatus()
        );
        return $this->getOrderComment($orderComment, $additionalInfo);
    }

    /**
     * @param string $additionalInfo
     *
     * @return string
     */
    protected function getRefusedStatusComment($additionalInfo = '')
    {
        $orderComment = __(
            'Received Ingenico ePayments feedback status update with refused status %1.',
            $this->getStatus()
        );
        return $this->getOrderComment($orderComment, $additionalInfo);
    }

    /**
     * @param string $additionalInfo
     *
     * @return string
     */
    protected function getFraudStatusComment($additionalInfo = '')
    {
        $orderComment = __(
            'Received Ingenico ePayments feedback status update with suspected fraud status %1.',
            $this->getStatus()
        );
        return $this->getOrderComment($orderComment, $additionalInfo);
    }

    /**
     * @param string $additionalInfo
     * @param string $orderComment
     *
     * @return string
     */
    protected function getOrderComment($orderComment, $additionalInfo = '')
    {
        if ($additionalInfo) {
            $orderComment .= ' ' . $additionalInfo;
        }

        return $orderComment;
    }

    /**
     * Merges the PAYID with the PAYIDSUB, if the latter is present, otherwise just returns the PAYID
     *
     * @return string
     */
    public function getTransactionId()
    {
        $transId = $this->getPayid();
        if ($this->hasPayidsub()) {
            $transId .= '/' . $this->getPayidsub();
        }

        return $transId;
    }
}
