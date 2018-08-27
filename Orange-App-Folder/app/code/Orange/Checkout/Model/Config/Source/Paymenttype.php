<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Checkout\Model\Config\Source;

class Paymenttype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Net Banking')],
            ['value' => 1, 'label' => __('Credit Card')]
        ];
    }
}
