<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class PaymentsConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCodes = [
        Ntb::PAYMENT_METHOD_NTB_CODE,
        GlobalPay::PAYMENT_METHOD_GLOBAL_PAY_CODE
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['dilmah_payments']['redirectUrl'][$code] = $this->getMethodRedirectUrl($code);
                $config['payment']['dilmah_payments']['termsUrl'][$code] = $this->getMethodTermsUrl($code);
                $config['payment']['instructions'][$code] = $this->getInstructions($code);

                if ($code == GlobalPay::PAYMENT_METHOD_GLOBAL_PAY_CODE) {
                    $config['payment']['dilmah_payments']['terms'][$code] = $this->getMethodTermsContent($code);
                }
            }
        }
        return $config;
    }

    /**
     * Get instructions text from config
     *
     * @param string $code
     * @return string
     */
    protected function getInstructions($code)
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getInstructions()));
    }

    /**
     * Return redirect URL for method
     *
     * @param string $code
     * @return mixed
     */
    protected function getMethodRedirectUrl($code)
    {
        return $this->methods[$code]->getCheckoutRedirectUrl();
    }

    /**
     * Return content for terms and condition
     *
     * @param string $code
     * @return mixed
     */
    protected function getMethodTermsContent($code)
    {
        return $this->methods[$code]->getTermsContent();
    }

    /**
     * Return redirect URL for method
     *
     * @param string $code
     * @return mixed
     */
    protected function getMethodTermsUrl($code)
    {
        return $this->methods[$code]->getCheckoutTermsUrl();
    }
}
