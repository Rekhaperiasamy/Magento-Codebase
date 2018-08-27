<?php
namespace Netresearch\OPS\Test\Unit\Model;

class OpsConfigProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Model\OpsConfigProvider
     */
    private $object;

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Config | \PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    private $countryCollectionFactory;


    /**
     * @var \Magento\Framework\View\Asset\Repository | \PHPUnit_Framework_MockObject_MockObject
     */
    private $assetRepo;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManager  | \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Model\Store | \PHPUnit_Framework_MockObject_MockObject
     */
    private $store;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->config = $this->getMock(\Netresearch\OPS\Model\Config::class, [], [], '', false, false);
        $this->scopeConfig = $this->getMock(\Magento\Framework\App\Config::class, [], [], '', false, false);
        $this->countryCollectionFactory
            = $this->getMock(
                '\Magento\Directory\Model\ResourceModel\Country\CollectionFactory',
                [],
                [],
                '',
                false,
                false
            );
        $this->assetRepo = $this->getMock(
            '\Magento\Framework\View\Asset\Repository',
            [],
            [],
            '',
            false,
            false
        );
        $this->request = $this->getMock('\Magento\Framework\App\Request\Http', [], [], '', false, false);
        $this->store         = $this->getMock('\Magento\Store\Model\Store', [], [], '', false, false);
        $this->storeManager  = $this->getMock('\Magento\Store\Model\StoreManager', [], [], '', false, false);
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));
        $this->object = $this->objectManager->getObject(
            '\Netresearch\OPS\Model\OpsConfigProvider',
            [
                'config'                   => $this->config,
                'scopeConfig'              => $this->scopeConfig,
                'countryCollectionFactory' => $this->countryCollectionFactory,
                'assetRepo'                => $this->assetRepo,
                'request'                  => $this->request,
                'storeManager'             => $this->storeManager,
            ]
        );
    }

    public function testGetConfig()
    {
        $paymentRedirectUrl = 'paymentRedirectUrl';
        $threeDSRedirectUrl = 'threeDSRedirectUrl';
        $consentUrl = 'consentUrl';
        $intersolveBrands = 'intersolveBrands';
        $directEbankingBrands = 'direct,Ebanking,Brands';
        $idealIssuers = 'ideal,Issuers';
        $bankTransferCountryIds = '1,2,3';
        $bankTransferCountryArray = ['value' => 'usa', 'label' => 'USA'];
        $directDebitCountryIds = '1,2,3';
        $directDebitCountryArray = ['value' => 'usa', 'label' => 'USA'];
        $apresReceptionLogo = 'apresReceptionLogo';
        $comptantLogo = 'comptantLogo';
        $creditLogo = 'creditLogo';
        $isFlexDefaultOptionActive = true;
        $flexDefaultOptionTitle = 'flexDefaultOptionTitle';
        $flexMethods = serialize(['test']);
        $config = [
            'payment' => [
                'ops' => [
                    'paymentRedirectUrl' => $paymentRedirectUrl,
                    'threeDSRedirectUrl' => $threeDSRedirectUrl,
                    'consentUrl' => $consentUrl,
                    'paymentRedirectMessage' => null,
                    'logoData' => []
                ],
                'opsIdeal' => [
                    'issuers' => $idealIssuers
                ],
                'opsInterSolve' => [
                    'brands' => $intersolveBrands
                ],
                'opsDirectEbanking' => [
                    'brands' => explode(',', $directEbankingBrands)
                ],
                'opsBankTransfer' => [
                    'countries' => $bankTransferCountryArray
                ],
                'opsDirectDebit' => [
                    'countries' => $directDebitCountryArray
                ],
                'kwixo' => [
                    'apresReception' => ['pmLogo' => $apresReceptionLogo],
                    'comptant' => ['pmLogo' => $comptantLogo],
                    'credit' => ['pmLogo' => $creditLogo]
                ],
                'opsFlex' => [
                    'isDefaultOptionActive' => $isFlexDefaultOptionActive,
                    'defaultOptionTitle' => $flexDefaultOptionTitle,
                    'infoKeyTitle' => \Netresearch\OPS\Model\Payment\Flex::INFO_KEY_TITLE,
                    'methods' => unserialize($flexMethods)
                ],
                'opsOpenInvoice' => [
                    'ops_openInvoiceAt' => [],
                    'ops_openInvoiceDe' => [],
                    'ops_openInvoiceNl' => []
                ]
            ]
        ];
        $this->config->expects($this->once())
            ->method('getPaymentRedirectUrl')
            ->will($this->returnValue($paymentRedirectUrl));

        $this->config->expects($this->once())
            ->method('get3dSecureRedirectUrl')
            ->will($this->returnValue($threeDSRedirectUrl));

        $this->config
            ->expects($this->once())
            ->method('getConsentUrl')
            ->will($this->returnValue($consentUrl));

        $this->config
            ->expects($this->once())
            ->method('getIntersolveBrands')
            ->will($this->returnValue($intersolveBrands));

        $this->config
            ->expects($this->once())
            ->method('getDirectEbankingBrands')
            ->will($this->returnValue($directEbankingBrands));

        $this->config
            ->expects($this->once())
            ->method('getBankTransferCountryIds')
            ->will($this->returnValue($bankTransferCountryIds));

        $this->config
            ->expects($this->once())
            ->method('getDirectDebitCountryIds')
            ->will($this->returnValue($directDebitCountryIds));

        $this->scopeConfig
            ->expects($this->any())
            ->method('getValue')
            ->will($this->onConsecutiveCalls(
                $config,
                $idealIssuers,
                $isFlexDefaultOptionActive,
                $flexDefaultOptionTitle,
                $flexMethods
            ));

        $countryCollection = $this->getMock(
            '\Magento\Directory\Model\ResourceModel\Country\Collection',
            [],
            [],
            '',
            false,
            false
        );
        $countryCollection
            ->expects($this->any())
            ->method('addCountryCodeFilter')
            ->will($this->returnSelf());

        $countryCollection
            ->expects($this->any())
            ->method('toOptionArray')
            ->will($this->onConsecutiveCalls($bankTransferCountryArray, $directDebitCountryArray));

        $this->countryCollectionFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($countryCollection));

        $this->request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(false));

        $this->assetRepo
            ->expects($this->any())
            ->method('getUrlWithParams')
            ->will($this->onConsecutiveCalls($apresReceptionLogo, $comptantLogo, $creditLogo));

        $this->assertEquals($config, $this->object->getConfig());
    }

    public function testGetPaymentsLogoData()
    {
        $paymentsData = [
            'payment1'     => [],
            'ops_payment1' => [],
            'ops_payment2' => [
                'logo' => 'logo2.png'
            ],
            'ops_payment3' => [
                'logo'          => 'logo3.png',
                'logo_position' => 'left'
            ]
        ];
        $logoData     = [
            'ops_payment1' => [
                'src'   => 'logo_base_url/netresearch/ops/logo/logo1.png',
                'class' => 'ops-payment-logo-hidden'
            ],
            'ops_payment2' => [
                'src'   => 'logo_base_url/netresearch/ops/logo/logo2.png',
                'class' => 'ops-payment-logo-hidden'
            ],
            'ops_payment3' => [
                'src'   => 'logo_base_url/netresearch/ops/logo/logo3.png',
                'class' => 'ops-payment-logo-left'
            ]
        ];

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->will($this->returnValueMap([
                                             [
                                                 'payment',
                                                 \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                                 null,
                                                 $paymentsData
                                             ]
                                         ]));
        $this->store->expects($this->any())
            ->method('getBaseUrl')
            ->with(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            ->will($this->returnValue('logo_base_url/'));
        $this->assetRepo->expects($this->any())
            ->method('getUrlWithParams')
            ->will($this->returnValue('logo_base_url/netresearch/ops/logo/logo1.png'));


        $this->assertEquals($logoData, $this->object->getPaymentsLogoData());
    }
}
