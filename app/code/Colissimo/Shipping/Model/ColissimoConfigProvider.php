<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\Data\AddressExtensionFactory;

/**
 * Class ColissimoConfigProvider
 */
class ColissimoConfigProvider implements ConfigProviderInterface
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var Address $address
     */
    protected $address;

    /**
     * @var AddressExtensionFactory $addressExtensionFactory
     */
    protected $addressExtensionFactory;

    /**
     * @param Address $address
     * @param AddressExtensionFactory $addressExtensionFactory
     * @param Session $checkoutSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Address $address,
        AddressExtensionFactory $addressExtensionFactory,
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->address          = $address;
        $this->checkoutSession  = $checkoutSession;
        $this->scopeConfig      = $scopeConfig;
        $this->storeManager     = $storeManager;
        $this->addressExtensionFactory = $addressExtensionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        $output = [
            'colissimoUrl'    => $store->getUrl('colissimo'),
            'colissimoPickup' => 'colissimo_pickup',
            'colissimoOpen'   => $this->scopeConfig->getValue('carriers/colissimo/pickup/open'),
        ];

        return $output;
    }
}
