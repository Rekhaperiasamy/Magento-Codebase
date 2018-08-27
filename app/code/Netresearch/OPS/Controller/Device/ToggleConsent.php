<?php

namespace Netresearch\OPS\Controller\Device;

class ToggleConsent extends \Netresearch\OPS\Controller\Device
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->oPSConfig->getDeviceFingerPrinting()) {
            $consent = (bool)$this->getRequest()->getParam(self::CONSENT_PARAMETER_KEY);
            $this->customerSession
                ->setData(\Netresearch\OPS\Model\Payment\PaymentAbstract::FINGERPRINT_CONSENT_SESSION_KEY, $consent);
        }

        return $this->consent();
    }
}
