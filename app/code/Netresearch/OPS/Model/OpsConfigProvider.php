<?php

namespace Netresearch\OPS\Model;

use Magento\Framework\Exception\LocalizedException;

class OpsConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    const PAYMENT_LOGO_UPLOAD_DIR = 'netresearch/ops/logo/';

    /**
     * @var Config
     */
    private $config;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'ops' => [
                    'paymentRedirectUrl' => $this->config->getPaymentRedirectUrl(),
                    'threeDSRedirectUrl' => $this->config->get3dSecureRedirectUrl(),
                    'consentUrl' => $this->config->getConsentUrl(),
                    'paymentRedirectMessage' => $this->config->getRedirectMessage(),
                    'logoData' => $this->getPaymentsLogoData()
                ],
                'opsIdeal' => [
                    'issuers' => $this->getIDealIssuers()
                ],
                'opsInterSolve' => [
                    'brands' => $this->config->getIntersolveBrands()
                ],
                'opsDirectEbanking' => [
                    'brands' => explode(',', $this->config->getDirectEbankingBrands())
                ],
                'opsBankTransfer' => [
                    'countries' => $this->getBankTransferCountries()
                ],
                'opsDirectDebit' => [
                    'countries' => $this->getDirectDebitCountries()
                ],
                'kwixo' => [
                    'apresReception' => [
                        'pmLogo' => $this->getViewFileUrl('Netresearch_OPS::images/kwixo/apres_reception.jpg')
                    ],
                    'comptant' => [
                        'pmLogo' => $this->getViewFileUrl('Netresearch_OPS::images/kwixo/comptant.jpg')
                    ],
                    'credit' => [
                        'pmLogo' => $this->getViewFileUrl('Netresearch_OPS::images/kwixo/credit.jpg')
                    ]
                ],
                'opsFlex' => [
                    'isDefaultOptionActive' => $this->getIsFlexDefaultOptionActive(),
                    'defaultOptionTitle' => $this->getFlexDefaultOptionTitle(),
                    'infoKeyTitle' => \Netresearch\OPS\Model\Payment\Flex::INFO_KEY_TITLE,
                    'methods' => $this->getFlexMethods()
                ],
                'opsOpenInvoice' => $this->getOpenInvoiceConfig()
            ]
        ];
        return $config;
    }

    protected function getOpenInvoiceConfig()
    {
        $config = [
            \Netresearch\OPS\Model\Payment\OpenInvoiceAt::CODE => [],
            \Netresearch\OPS\Model\Payment\OpenInvoiceDe::CODE => [],
            \Netresearch\OPS\Model\Payment\OpenInvoiceNl::CODE => [],
        ];

        array_walk(
            $config,
            function (&$value, $key) {
                if ($this->scopeConfig->getValue(
                    sprintf('payment/%s/show_invoice_terms', $key)
                )
                ) {
                    $value = [
                        'title' => $this->scopeConfig->getValue(
                            sprintf('payment/%s/invoice_terms_title', $key)
                        ),
                        'link'  => $this->scopeConfig->getValue(
                            sprintf('payment/%s/invoice_terms_url', $key)
                        )
                    ];
                }
            }
        );

        return $config;
    }

    /**
     * return the ideal issuers
     *
     * @return array
     */
    protected function getIDealIssuers()
    {
        return $this->scopeConfig
            ->getValue('payment/ops_iDeal/issuer', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    protected function getBankTransferCountries()
    {
        $countryIds = explode(',', $this->config->getBankTransferCountryIds());
        $countryCollection = $this->countryCollectionFactory->create()->addCountryCodeFilter($countryIds);
        $countries = $countryCollection->toOptionArray(__('--Please Select--'));

        if (in_array('*', $countryIds)) {
            array_push($countries, ['value' => '*', 'label' => __('Miscellaneous Countries')]);
        }

        return $countries;
    }

    /**
     * @return array
     */
    protected function getDirectDebitCountries()
    {
        $countryIds = explode(',', $this->config->getDirectDebitCountryIds());
        $countryCollection = $this->countryCollectionFactory->create()->addCountryCodeFilter($countryIds);
        $countries = $countryCollection->toOptionArray(__('--Please Select--'));

        if (in_array('*', $countryIds)) {
            array_push($countries, ['value' => '*', 'label' => __('Miscellaneous Countries')]);
        }

        return $countries;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    protected function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()], $params);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    protected function getIsFlexDefaultOptionActive($storeId = null)
    {
        return (bool) $this->scopeConfig
            ->getValue(
                sprintf('payment/%s/default', \Netresearch\OPS\Model\Payment\Flex::CODE),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    protected function getFlexDefaultOptionTitle($storeId = null)
    {
        return (string) $this->scopeConfig
            ->getValue(
                sprintf('payment/%s/default_title', \Netresearch\OPS\Model\Payment\Flex::CODE),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    /**
     * get configurable payment methods
     *
     * @param null $storeId
     * @return \string[][]
     */
    public function getFlexMethods($storeId = null)
    {

        $methods = $this->scopeConfig
            ->getValue(
                sprintf('payment/%s/methods', \Netresearch\OPS\Model\Payment\Flex::CODE),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        if (!is_array($methods)) {
            $methods = unserialize($methods);
        }

        return $methods;
    }

    /**
     * Get payments logo data
     *
     * @return array
     */
    public function getPaymentsLogoData()
    {
        $paymentsData = $this->scopeConfig->getValue('payment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $logoData = [];

        foreach ($paymentsData as $paymentCode => $data) {
            if (strpos($paymentCode, 'ops_') !== 0) {
                continue;
            }

            if (!empty($data['logo'])) {
                $logoSrc =
                    $this->storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                    . self::PAYMENT_LOGO_UPLOAD_DIR . $data['logo'];
            } else {
                $logoSrc = $this->getViewFileUrl('Netresearch_OPS::images/logo/' . $paymentCode . '.png');
            }

            if (!empty($data['logo_position'])) {
                $logoClass = 'ops-payment-logo-' . $data['logo_position'];
            } else {
                $logoClass = 'ops-payment-logo-hidden';
            }

            $logoData[$paymentCode] = [
                'src'   => $logoSrc,
                'class' => $logoClass
            ];
        }

        return $logoData;
    }
}
