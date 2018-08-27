<?php

namespace Orange\Checkout\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor as CheckoutLayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

/**
 * Class DisableRenderersAndPaymentsJs
 *
 * @package Orange\Checkout\Plugin
 */
class DisableRenderersAndPaymentsJs
{
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * DisableRenderersAndPaymentsJs constructor.
     *
     * @param PaymentHelper $paymentHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(PaymentHelper $paymentHelper, ScopeConfigInterface $scopeConfig)
    {
        $this->paymentHelper = $paymentHelper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @see https://github.com/magento/magento2/issues/4868#issuecomment-259244034
     * @param CheckoutLayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function beforeProcess(CheckoutLayoutProcessor $subject, array $jsLayout)
    {
        $configuration = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['renders']['children'];

        if (!isset($configuration)) {
            return [$jsLayout];
        }

        $paymentMethods = $this->getPaymentMethods();
        foreach ($configuration as $paymentGroup => $groupConfig) {
            foreach (array_keys($groupConfig['methods']) as $paymentCode) {
                if (!in_array($paymentCode, $paymentMethods)) {
                    unset($configuration[$paymentGroup]['methods'][$paymentCode]);
                }
            }

            if (empty($configuration[$paymentGroup]['methods'])) {
                unset($configuration[$paymentGroup]);
            }
        }

        return [$jsLayout];
    }

    /**
     * @return array
     */
    protected function getPaymentMethods()
    {
        $paymentMethods = $this->scopeConfig->getValue('payment');
        foreach ($paymentMethods as $paymentCode => $paymentMethod) {
            if (!isset($paymentMethod['active'])) {
                continue;
            }

            if (!isset($paymentMethod['active']) || !$paymentMethod['active']) {
                unset($paymentMethods[$paymentCode]);
            }
        }

        return array_keys($paymentMethods);
    }
}