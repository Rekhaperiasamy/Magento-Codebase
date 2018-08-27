<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Checkout\Block;

use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Store\Model\ScopeInterface;

/**
 * Shopping cart block
 */
class Cart extends AbstractCart
{

    /**
     * @var int
     */
    const XML_PATH_SHIPPING_MESSAGE_ACTIVE = 'shipping/message/active';

    /**
     * @var string
     */
    const XML_PATH_SHIPPING_MESSAGE_THRESHOLD = 'shipping/message/threshold';

    /**
     * @var string
     */
    const XML_PATH_SHIPPING_MESSAGE_MESSAGE = 'shipping/message/message';

    /**
     * @var float
     */
    const DEFAULT_THRESHOLD = 50;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var int
     */
    protected $active;

    /**
     * @var float
     */
    protected $threshold;

    /**
     * @var string
     */
    protected $message;

    /**
     * Cart constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->localeCurrency = $localeCurrency;
        $store = $this->_storeManager->getStore();
        $this->currencyCode = $store->getCurrentCurrencyCode();
        $this->active = $this->_scopeConfig->getValue(
            self::XML_PATH_SHIPPING_MESSAGE_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
        $this->threshold = $this->_scopeConfig->getValue(
            self::XML_PATH_SHIPPING_MESSAGE_THRESHOLD,
            ScopeInterface::SCOPE_STORE
        );
        $this->message = $this->_scopeConfig->getValue(
            self::XML_PATH_SHIPPING_MESSAGE_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is cart free shipping message enabled
     * @return int|mixed
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Get Free shipping message alert
     * @return int
     */
    public function getFreeShippingAlertMessage()
    {
        $threshold = $this->threshold;
        if (!is_numeric($threshold)) {
            $threshold = self::DEFAULT_THRESHOLD;
        }
        if ($threshold - $this->getQuote()->getSubtotal() > 0) {
            return $threshold - $this->getQuote()->getSubtotal();
        }
        return false;
    }

    /**
     * Get shipping message entered in backend
     * @return mixed|string
     */
    public function getShippingMessage()
    {
        //if more than one %s is used in the configuration value for shipping message
        if (substr_count($this->message, '%s') > 1) {
            //take the part after the first occurrence of %s
            $msgPart = substr($this->message, strpos($this->message, '%s') + 2);
            //replace %s with empty string
            $msgPart = str_replace('%s', '', $msgPart);
            //take the original sentence and replace the sentence after the first occurrence of %s with the new sentence
            // part
            $this->message = str_replace(
                substr($this->message, strpos($this->message, '%s') + 1),
                $msgPart,
                $this->message
            );
        }
        return $this->message;
    }

    /**
     * Get currency code
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Retrieve currency name by code
     * @param string $code
     * @return string
     */
    public function getCurrencySymbol($code)
    {
        $currency = $this->localeCurrency->getCurrency($code);
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    /**
     * Get threshold value for free shipping text
     * @return float|mixed
     */
    public function getThreshold()
    {
        return $this->threshold;
    }
}
