<?php

namespace Netresearch\OPS\Model\Payment;

/**
 * Flex.php
 *
 * @author    paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 */
class Flex extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    const CODE = 'ops_flex';

    const INFO_KEY_TITLE = 'flex_title';
    const INFO_KEY_PM = 'flex_pm';
    const INFO_KEY_BRAND = 'flex_brand';

    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** info source path */
    protected $_infoBlockType = 'Netresearch\OPS\Block\Info\Flex';

    protected $_formBlockType = 'Netresearch\OPS\Block\Form\Flex';

    /** payment code */
    protected $_code = self::CODE;

    protected $_needsCartDataForRequest = true;

    /**
     * {@inheritdoc}
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        $additionalData = $data->getAdditionalData();
        $title = isset($additionalData[self::INFO_KEY_TITLE]) ? $additionalData[self::INFO_KEY_TITLE] : null;
        $pm = isset($additionalData[self::INFO_KEY_PM]) ? $additionalData[self::INFO_KEY_PM] : null;
        $brand = isset($additionalData[self::INFO_KEY_BRAND]) ? $additionalData[self::INFO_KEY_BRAND] : null;

        $info = $this->getInfoInstance();
        if ($title) {
            $info->setAdditionalInformation(self::INFO_KEY_TITLE, $title);
        }
        if ($pm) {
            $info->setAdditionalInformation(self::INFO_KEY_PM, $pm);
        }
        if ($brand) {
            $info->setAdditionalInformation(self::INFO_KEY_BRAND, $brand);
        }

        return $this;
    }

    public function getOpsCode($payment = null)
    {
        return $this->getInfoInstance()->getAdditionalInformation(self::INFO_KEY_PM);
    }

    public function getOpsBrand($payment = null)
    {
        return $this->getInfoInstance()->getAdditionalInformation(self::INFO_KEY_BRAND);
    }
}
