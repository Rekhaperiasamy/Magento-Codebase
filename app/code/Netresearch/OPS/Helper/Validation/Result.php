<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Helper\Validation;

class Result
{
    protected $checkoutStepHelper = null;

    protected $config = null;

    protected $formBlock = null;

    protected $dataHelper = null;

    protected $result = [];

    /**
     * @var \Netresearch\OPS\Helper\Validation\Checkout\Step
     */
    protected $oPSValidationCheckoutStepHelper;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @param Checkout\Step $oPSValidationCheckoutStepHelper
     * @param \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     */
    public function __construct(
        \Netresearch\OPS\Helper\Validation\Checkout\Step $oPSValidationCheckoutStepHelper,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Data $oPSHelper
    ) {
        $this->oPSValidationCheckoutStepHelper = $oPSValidationCheckoutStepHelper;
        $this->oPSConfigFactory = $oPSConfigFactory;
        $this->oPSHelper = $oPSHelper;
    }
    /**
     * @param array $result
     */
    public function setResult(array $result)
    {
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param null $checkOutStepHelper
     */
    public function setCheckoutStepHelper($checkOutStepHelper)
    {
        $this->checkoutStepHelper = $checkOutStepHelper;
    }

    /**
     * @return null
     */
    public function getCheckoutStepHelper()
    {
        if (null === $this->checkoutStepHelper) {
            $this->checkoutStepHelper = $this->oPSValidationCheckoutStepHelper;
        }

        return $this->checkoutStepHelper;
    }

    /**
     * @param null $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return null
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = $this->oPSConfigFactory->create();
        }

        return $this->config;
    }

    /**
     * @param null $dataHelper
     */
    public function setDataHelper($dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return null
     */
    public function getDataHelper()
    {
        if (null === $this->dataHelper) {
            $this->dataHelper = $this->oPSHelper;
        }

        return $this->dataHelper;
    }

    /**
     * @param array $messages
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return array
     */
    public function getValidationFailedResult($messages, $quote)
    {
        $gotoSection            = $this->getCheckoutStepHelper()->getStep(array_keys($messages));
        $this->setBaseErroneousFields($messages, $gotoSection);
        $this->getFields($messages);
        $this->addErrorToExistingAddress($quote, $gotoSection);
        $this->cleanResult();

        return $this->getResult();
    }

    /**
     * @param $messages
     * @param $result
     *
     * @return mixed
     */
    protected function getFields($messages)
    {
        $mappedFields   = $this->getConfig()->getFrontendFieldMapping();
        $frontendFields = [];
        foreach ($messages as $key => $value) {
            if (array_key_exists($key, $mappedFields)) {
                $frontendFields = $this->oPSHelper->getFrontendValidationFields(
                    $mappedFields,
                    $key,
                    $value,
                    $frontendFields
                );
            }
        }
        $this->result['fields'] = $frontendFields;

        return $this;
    }

    /**
     * @param $quote
     * @param $gotoSection
     */
    protected function addErrorToExistingAddress($quote, $gotoSection)
    {
        if ($gotoSection == 'billing' && 0 < $quote->getBillingAddress()->getId()) {
            $this->result['fields']['billing-address-select'] = __(
                'Billing address contains invalid data'
            );
        }
        if ($gotoSection == 'shipping' && 0 < $quote->getShippingAddress()->getId()) {
            $this->result['fields']['shipping-address-select'] = __(
                'Shipping address contains invalid data'
            );
        }

        return $this;
    }

    /**
     * @param $messages
     * @param $result
     * @param $gotoSection
     *
     * @return mixed
     */
    protected function setBaseErroneousFields($messages, $gotoSection)
    {
        $this->result['error']        = implode(',', array_values($messages));
        $this->result['goto_section'] = $gotoSection;
        $this->result['opsError']     = true;

        return $this;
    }

    protected function cleanResult()
    {
        if (array_key_exists('update_section', $this->result)) {
            unset($this->result['update_section']);
        }

        return $this;
    }
}
