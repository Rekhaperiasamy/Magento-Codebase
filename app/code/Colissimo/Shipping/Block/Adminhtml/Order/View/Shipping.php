<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

/**
 * Class Shipping
 */
class Shipping extends Template implements TabInterface
{
    /**
     * Template
     *
     * @var string $_template
     */
    protected $_template = 'order/view/shipping.phtml';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Retrieve Shipping Address
     *
     * @return \Magento\Sales\Model\Order\Address|null
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Colissimo');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Colissimo');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $shippingAddress = $this->getShippingAddress();

        if (!$shippingAddress) {
            return false;
        }

        if (!$shippingAddress->getData('colissimo_product_code')) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}