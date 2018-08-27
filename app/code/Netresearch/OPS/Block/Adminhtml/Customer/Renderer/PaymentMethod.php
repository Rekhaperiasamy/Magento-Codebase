<?php

namespace Netresearch\OPS\Block\Adminhtml\Customer\Renderer;

/**
 * PaymentMethod.php
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 */
class PaymentMethod extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * PaymentMethod constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $methodCode = $row->getData($this->getColumn()->getIndex());
        $instance = $this->paymentHelper->getMethodInstance($methodCode);
        if ($instance) {
            return $instance->getTitle();
        }
        return '';
    }
}
