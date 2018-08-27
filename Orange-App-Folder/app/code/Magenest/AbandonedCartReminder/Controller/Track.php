<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/10/2015
 * Time: 10:35
 */

namespace Magenest\AbandonedCartReminder\Controller;

use Magento\Framework\App\ResponseInterface;

abstract class Track extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    protected $_urlBuilder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

}
