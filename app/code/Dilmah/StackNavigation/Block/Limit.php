<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_StackNavigation
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\StackNavigation\Block;

use Magento\Framework\View\Element\Template\Context;
use Netstarter\StackNavigation\Helper\Data;

class Limit extends \Netstarter\StackNavigation\Block\Limit
{

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Limit constructor.
     * @param Context $context
     * @param Data $dataHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $dataHelper, $data);
    }

    /**
     * get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }
}
