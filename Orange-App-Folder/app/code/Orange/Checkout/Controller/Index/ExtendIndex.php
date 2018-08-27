<?php
namespace Orange\Checkout\Controller\Index;

class ExtendIndex extends \Magento\Checkout\Controller\Onepage
{
    public function execute()
    {
        /** @var \Magento\Checkout\Helper\Data $checkoutHelper */

        return $this->_redirect('checkout/newstep/index', ['_secure' => true]);
    }
}