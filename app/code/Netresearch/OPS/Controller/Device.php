<?php

namespace Netresearch\OPS\Controller;

abstract class Device extends \Magento\Framework\App\Action\Action
{
    const CONSENT_PARAMETER_KEY = 'consent';

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    protected $oPSConfig = null;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\Session $customerSession,
        \Netresearch\OPS\Model\Config $oPSConfig
    ) {
        parent::__construct($context);
        $this->jsonEncoder = $jsonEncoder;
        $this->customerSession = $customerSession;
        $this->oPSConfig = $oPSConfig;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function consent()
    {
        $resultArray = [self::CONSENT_PARAMETER_KEY => false];

        if ($this->oPSConfig->getDeviceFingerPrinting()) {
            $resultArray[self::CONSENT_PARAMETER_KEY] = (bool)$this->customerSession
                ->getData(\Netresearch\OPS\Model\Payment\PaymentAbstract::FINGERPRINT_CONSENT_SESSION_KEY);
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        return $this->getResponse()->setBody($this->jsonEncoder->encode($resultArray));
    }
}
