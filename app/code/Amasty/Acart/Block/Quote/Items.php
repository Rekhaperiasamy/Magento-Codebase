<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Block\Quote;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Items extends \Magento\Framework\View\Element\Template
{
    protected $_imageHelper;
    protected $_productConfig;
    protected $_priceCurrency;
    protected $_params = array(
        'mode' => array(
            'default' => 'table',
            'available' => array(
                'list', 'table'
            )
        ),
        'showImage' => array(
            'default' => 'yes',
            'available' => array(
                'yes', 'no'
            )
        ),
        'showConfigurableImage' => array(
            'default' => 'no',
            'available' => array(
                'yes', 'no'
            )
        ),
        'showPrice' => array(
            'default' => 'yes',
            'available' => array(
                'yes', 'no'
            )
        ),
        'priceFormat' => array(
            'default' => 'excludeTax',
            'available' => array(
                'excludeTax', 'includeTax'
            )
        ),
        'showDescription' => array(
            'default' => 'yes',
            'available' => array(
                'yes', 'no'
            )
        ),
        'optionList' => array(
            'default' => 'yes',
            'available' => array(
                'yes', 'no'
            )
        ),
    );

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        PriceCurrencyInterface $priceCurrency,
        \Amasty\Acart\Model\UrlManager $urlManager,
        array $data = []
    ){
        $this->_imageHelper = $imageHelper;
        $this->_productConfig = $productConfig;
        $this->_priceCurrency = $priceCurrency;
        $this->_urlManager = $urlManager;

        return parent::__construct($context, $data);
    }

    protected function _getLayoutParam($key)
    {
        $func = 'get' . $key;
        return in_array($this->$func(), $this->_params[$key]['available']) ? $this->$func() : $this->_params[$key]['default'];
    }

    public function getMode()
    {
        return $this->_getLayoutParam('mode');
    }

    public function showImage()
    {
        return $this->_getLayoutParam('showImage') == 'yes';
    }

    public function showConfigurableImage()
    {
        return $this->_getLayoutParam('showConfigurableImage') == 'yes';
    }

    public function showPrice()
    {
        return $this->_getLayoutParam('showPrice') == 'yes';
    }

    public function showDescription()
    {
        return $this->_getLayoutParam('showDescription') == 'yes';
    }

    public function showPriceIncTax()
    {
        return $this->_getLayoutParam('priceFormat') == 'includeTax';
    }

    public function showOptionList()
    {
        return $this->_getLayoutParam('optionList') == 'yes';
    }

    public function getQuoteItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    public function initProductImageHelper($visibleItem, $imageId)
    {
        $product = $visibleItem->getProduct();
        if (!$this->showConfigurableImage()) {
            foreach($this->getQuote()->getAllItems() as $item)
            {
                if ($item->getParentItemId() && $item->getParentItemId() == $visibleItem->getId())
                {
                    $product = $item->getProduct();
                    break;
                }
            }
        }

        $this->_imageHelper->init($product, $imageId);
    }

    public function getProductImageHelper()
    {
        return $this->_imageHelper;
    }

    public function getProduct($item)
    {
        return $item->getProduct()->load($item->getProduct()->getId());
    }

    public function getProductOptions($item)
    {
        /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
        $helper = $this->_productConfig;
        return $helper->getOptions($item);
    }

    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
        $helper = $this->_productConfig;
        $params = [
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
        ];
        return $helper->getFormattedOptionValue($optionValue, $params);
    }

    public function formatPrice($price)
    {
        return $this->_priceCurrency->format(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getQuote()->getStore()
        );
    }

    public function getPrice($item)
    {
        $price = null;
        if ($this->showPriceIncTax()){
            $price = $item->getPriceInclTax();
        } else {
            $price = $item->getPrice();
        }

        return $this->formatPrice($price);
    }

    protected function _initUrlManager()
    {
        if (!$this->_urlManager->getRule()) {
            $this->_urlManager->init($this->getRule(), $this->getHistory());
        }
    }


    public function getProductUrl($item)
    {
        $this->_initUrlManager();

        if ($item->getRedirectUrl()) {
            return $item->getRedirectUrl();
        }

        $product = $item->getProduct();

        $option = $item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        return $this->_urlManager->get($product->getUrlModel()->getUrl($product));
    }
}