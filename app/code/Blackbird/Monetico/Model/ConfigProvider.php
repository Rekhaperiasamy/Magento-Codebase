<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Model;

use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Blackbird\Monetico\Model\Config\Source\Environment;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var array
     */
    protected $_configData = [];

    /**
     * @param \Blackbird\Monetico\Model\ConfigFactory $configFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ConfigFactory $configFactory,
        UrlInterface $urlBuilder
    ) {
        $this->config = $configFactory->create();
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    public function getMethodCode()
    {
        return $this->config->getMethodCode();
    }

    /**
     * @param string $methodCode
     * @return $this
     */
    public function setMethodCode($methodCode)
    {
        $this->config->setMethodCode($methodCode);

        return $this;
    }

    /**
     * @param string $key
     * @param int $storeId
     * @return string
     */
    public function getSystemConfigValue($key, $storeId = null)
    {
        return $this->config->getValue($key, $storeId);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        if (empty($this->_configData)) {
            $config = [
                'payment' => [
                    'monetico' => [
                        'redirectUrl' => [
                            'back' => $this->getBackRedirectUrl(),
                            'success' => $this->getSuccessRedirectUrl(),
                            'failure' => $this->getFailureRedirectUrl(),
                        ],
                        'paymentUrl' => $this->getPaymentFormAction(),
                        'capturePaymentUrl' => $this->getCapturePaymentFormAction(),
                        'refundPaymentUrl' => $this->getRefundPaymentFormAction(),
                        'TPE' => $this->config->getValue('tpe_number'),
                        'societe' => $this->config->getValue('site_code'),
                        'locale' => $this->config->getLocale(),
                    ],
                ],
            ];

            $this->_configData = $config;
        }

        return $this->_configData;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->config->getVersion();
    }

    /**
     * @return bool
     */
    public function isDebugModeEnabled()
    {
        return $this->config->isDebugModeEnabled();
    }

    /**
     * @return bool
     */
    public function isPaymentExpressEnabled()
    {
        return (bool)$this->config->getValue('payment_express');
    }

    /**
     * @param float $amount
     * @return bool
     */
    public function is3DSecureDisabled($amount)
    {
        $bool = (bool)$this->config->getValue('verify_3dsecure');
        $bool = $bool && !empty($this->getMaximumAmount3DSecure());
        $bool = $bool && $this->getMaximumAmount3DSecure() <= $amount;

        return $bool;
    }

    /**
     * @param float $amount
     * @return bool
     */
    public function is3DSecureEnabled($amount)
    {
        return !$this->is3DSecureDisabled($amount);
    }

    /**
     * @return bool
     */
    public function hasDisabledOptions()
    {
        return !(bool)$this->config->getValue('allow_options');
    }

    /**
     * @return array
     */
    public function getDisabledOptions()
    {
        return explode(',', $this->config->getValue('disabled_options'));
    }

    /**
     * @return int
     */
    public function getNumberOfTerms()
    {
        return (int)$this->config->getValue('number_terms');
    }

    /**
     * @param int $term
     * @return int
     */
    public function getTermRate($term)
    {
        return (int)$this->config->getValue('term_rate_' . $term);
    }

    /**
     * @param int $term
     * @return int
     */
    public function getMultiplierTermRate($term)
    {
        return $this->getTermRate($term) / 100;
    }

    /**
     * @return bool
     */
    public function isTermsRateValid()
    {
        $totalRate = 0;

        for ($i = 1; $i <= $this->getNumberOfTerms(); $i++) {
            $totalRate += $this->getTermRate($i);
        }

        return ($totalRate == 100);
    }

    /**
     * @return string
     */
    public function getMaximumAmount3DSecure()
    {
        return $this->config->getValue('verify_3dsecure_max_amount');
    }

    /**
     * @return string
     */
    public function getTransferDescription()
    {
        $text = trim($this->config->getValue('transaction_description'));

        if (empty($text)) {
            $text = __('Payment from %code_societe% through %payment_method% for the Order n°%order_id%');
        }

        return $text;
    }

    /**
     * @return bool
     */
    public function isBasePriceCurrencyEnabled()
    {
        return (bool) $this->config->getValue('use_base_currency');
    }

    /**
     * Retrieve the payment URL
     *
     * @param array $params
     * @return string
     */
    public function getPaymentFormAction(array $params = [])
    {
        return sprintf(
            'https://p.monetico-services.com/%spaiement.cgi%s',
            $this->isSandbox() ? 'test/' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    /**
     * Retrieve the capture payment URL
     *
     * @todo use next release
     * @param array $params
     * @return string
     */
    public function getCapturePaymentFormAction(array $params = [])
    {
        return sprintf(
            'https://p.monetico-services.com/%scapture_paiement.cgi%s',
            $this->isSandbox() ? 'test/' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    /**
     * Retrieve the refund payment URL
     *
     * @todo use next release
     * @param array $params
     * @return string
     */
    public function getRefundPaymentFormAction(array $params = [])
    {
        return sprintf(
            'https://p.monetico-services.com/%srecredit_paiement.cgi%s',
            $this->isSandbox() ? 'test/' : '',
            $params ? '?' . http_build_query($params) : ''
        );
    }

    /**
     * Check if the payment module is in sandbox mode
     *
     * @return bool
     */
    public function isSandbox()
    {
        return ($this->config->getValue('environment') === Environment::ENVIRONMENT_SANDBOX);
    }

    /**
     * Retrieve the back redirect URL
     *
     * @return string
     */
    protected function getBackRedirectUrl()
    {
        return $this->_urlBuilder->getDirectUrl('checkout/cart');
    }

    /**
     * Retrieve the success redirect URL
     *
     * @return string
     */
    protected function getSuccessRedirectUrl()
    {
        return $this->_urlBuilder->getDirectUrl('monetico/payment/success');
    }

    /**
     * Retrieve the failure redirect URL
     *
     * @return string
     */
    protected function getFailureRedirectUrl()
    {
        return $this->_urlBuilder->getDirectUrl('monetico/payment/failure');
    }
}
