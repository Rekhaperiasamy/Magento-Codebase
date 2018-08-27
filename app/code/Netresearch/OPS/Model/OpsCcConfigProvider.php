<?php

namespace Netresearch\OPS\Model;

use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Framework\Exception\LocalizedException;
use Netresearch\OPS\Model\Payment\DirectDebit;

class OpsCcConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * @var array
     */
    protected static $methodCodes
        = [
            'opsCc'          => \Netresearch\OPS\Model\Payment\Cc::CODE,
            'opsDc'          => \Netresearch\OPS\Model\Payment\Debitcard::CODE,
            'opsDirectDebit' => DirectDebit::CODE
        ];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var QuoteSession
     */
    protected $backendSession;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * OpsCcConfigProvider constructor.
     *
     * @param Config                                      $config
     * @param \Magento\Checkout\Model\Session             $checkoutSession
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        Config $config,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        QuoteSession $backendSession
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->localeResolver = $localeResolver;
        $this->assetRepo = $assetRepo;
        $this->backendSession = $backendSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = ['payment' => [
            'opsHTP' => [
                'hashUrl'           => $this->config->getGenerateHashUrl(),
                'url'               => $this->config->getAliasGatewayUrl(),
                'pspid'             => $this->config->getPSPID(),
                'orderId'           => $this->getOrderId(),
                'aliasExceptionUrl' => $this->config->getAliasExceptionUrl(),
                'locale'            => $this->localeResolver->getLocale()
            ]
        ]];
        foreach (self::$methodCodes as $methodKey => $methodCode) {
            $methodConfig = [
                'ccBrands'              => explode(',', $this->config->getAcceptedCcTypes($methodCode)),
                'inlinePaymentCcTypes'  => $this->config->getInlinePaymentCcTypes($methodCode),
                'aliasAcceptUrl'        => $this->config->getAliasAcceptUrl(null, false, $methodCode),
                'aliasManager'          => $this->config->isAliasManagerEnabled($methodCode),
                'isThreeDSecureEnabled' => (bool)$this->config->get3dSecureIsActive($methodCode),
                'brandImage'            => $this->getBrandLogos($methodCode),
                'paymentMethod'         => $methodCode === DirectDebit::CODE ? '' : 'CreditCard',
                'htpTemplate'           => $this->config->getHtpTemplateName($methodCode)
            ];
            $config['payment'][$methodKey] = $methodConfig;
        }

        return $config;
    }

    private function getBrandLogos($methodCode)
    {
        $logos = [];
        $brands = $this->config->getCardTypes($methodCode);
        foreach ($brands as $brand) {
            $logos[$brand] = $this->getImageForBrand($brand);
        }

        return $logos;
    }

    /**
     * @param string $brand
     *
     * @return string
     */
    private function getImageForBrand($brand)
    {
        $brandName = str_replace(' ', '', $brand);
        if ($brandName) {
            return $this->getViewFileUrl('Netresearch_OPS::images/alias/brands/' . $brandName . '.png');
        }

        return null;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array  $params
     *
     * @return string
     */
    private function getViewFileUrl($fileId)
    {
        try {
            $params = ['_secure' => false, 'area' => 'frontend'];

            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * @return mixed
     */
    protected function getOrderId()
    {
        return $this->checkoutSession->getQuote()->getId() ? : $this->backendSession->getQuote()->getId();
    }
}
