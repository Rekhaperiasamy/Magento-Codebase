<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 17/06/2016
 * Time: 13:57
 */
namespace Magenest\AbandonedCartReminder\Block\Form\Customer;

class Mobile extends \Magento\Framework\View\Element\Template
{

    protected $helper;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\AbandonedCartReminder\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helper = $dataHelper;

    }//end __construct()


    public function isMobileRequired()
    {
        return $this->helper->getIsMobileInputRequire();

    }//end isMobileRequired()
}//end class
