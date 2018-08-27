<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_SpecialOrders
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\SpecialOrders\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Form
 *
 */
class Form extends Template
{
    /**
     * @var Session
     */
    public $session;

    /**
     * Form construct
     *
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->session = $session;
    }

    /**
     * Returns action url for special order form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('special-orders/index/post', ['_secure' => true]);
    }

    /**
     * Get request post data
     * @return \Magento\Framework\DataObject
     */
    public function getFormData()
    {
        $formData = $this->session->getSpecialOrderFormData(true);
        $formDataObj=new \Magento\Framework\DataObject($formData ?: []);
        return $formDataObj;
    }

    /**
     * Get tea product categories
     * @return array
     */
    public function getProductCategories()
    {
        $productCategoryArray = ['Premium Ceylon Single Origin Tea',
            'Exceptional Range',
            'Single Region Selection',
            'Watte',
            'Fun Tea Selection',
            'Infusion',
            'Organic Tea Selection',
            'Natural Green Tea Selection',
            'Vivid Tea Selection',
            'Silver Jubilee Gourmet',
            'Tea Maker\'s Private Reserve',
            'Ceylon Orange Pekoe',
            'Gift of Tea',
            't-Series, Designer Gourmet Teas',
            'Decaffeinated Tea',
            'Ceylon Silver tips',
            'Accessories',
            'Ceylon Gold',
            'Tea Cordial',
            'Help me Find the Perfect Tea'];

        return $productCategoryArray;
    }

    /**
     * Get product tea types
     * @return array
     */
    public function getProductType()
    {
        $productTypeArray =['Black Tea',
        'Flavoured Black Tea',
        'Green Tea',
        'Flavoured Green Tea',
        'Earl Grey',
        'Chai &amp; Herbal Infusion',
        'Organic &amp; Decaffeinated Tea',
        'White Tea',
        'Oolong Tea',
        'Help me Find the Perfect Tea'];

        return $productTypeArray;
    }

    /**
     * Get tea format
     * @return array
     */
    public function getTeaFormat()
    {
        $teaFormatArray = ['Tea cup bags',
        'Loose Leaf Tea',
        'Tagless Tea Bags',
        'Individually Wrapped Tea Bags',
        'Luxury Leaf Tea Bags'];

        return $teaFormatArray;
    }
}
