<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Checkout\Plugin\Block\Checkout;

/**
 * Class LayoutProcessor
 *
 * @package Dilmah\Checkout\Plugin\Block\Checkout
 */
class LayoutProcessor
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * LayoutProcessor constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        $this->storeId = $this->storeManager->getStore()->getId();
    }

    /**
     * After plugin for process function
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $result
     * @return mixed
     * @SuppressWarnings("unused")
     */
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, $result)
    {
        // @codingStandardsIgnoreStart
        //removing items
        unset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['fax']);
        unset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['config']['tooltip']);
        unset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][1]);

        //sort order
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['sortOrder']= 50;
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['sortOrder']= 60;
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['sortOrder'] = 70;
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['sortOrder']= 80;
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['sortOrder']= 90;
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['sortOrder']= 100;
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['sortOrder']= 110;

        $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['promo-giftcard-title']['sortOrder']= 50;
        $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount']['sortOrder']= 110;
        $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['giftCardAccount']['sortOrder']= 120;

        $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['reward']['sortOrder']= 30;
        $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['storeCredit']['sortOrder']= 40;

        $result['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['sortOrder']= 101;
        $result['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['sortOrder']= 100;

        //change label texts
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['label']= __('Company (Optional)');
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street'][0]['label']= __('Street name');
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['label']= __('City/ Suburb');
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['label']= __('Postcode');

        $result['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['children']['subtotal']['config']['title']= __('Subtotal');
        $result['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['children']['grand-total']['config']['title']= __('Total');

        //placeholder texts
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['placeholder']= __('Optional');
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']['placeholder']= __('e.g. John');
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['lastname']['placeholder']= __('Smith');
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['placeholder']= __('Select Country');
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['placeholder'] = __('e.g. 12 George Street');
        switch ($this->storeId)
        {
            case 1: //Global shop
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['notice']= __('We don\'t ship to P.O box and A.P.O numbers');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['class']= 'note';
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['notice']= __(' ');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['class']= 'country-validation-note'; // country validation for DT-1340
                break;
            
            case 7: //USA
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['placeholder']= __('e.g. Los Angeles');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['placeholder']= __('e.g. California');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region']['placeholder']= __('e.g. California');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['placeholder']= __('e.g. 12345-6789');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['placeholder']= __('e.g. (01) 800-876-2535');
                break;

            case 8: //UK
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['placeholder']= __('e.g. Roath');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['placeholder']= __('e.g. Cardiff');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region']['placeholder']= __('e.g. Cardiff');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['placeholder']= __('e.g. AB12 3CD');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['placeholder']= __('e.g. +44 (0)207 123 4567');
                break;

            case 9: //FR
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['placeholder'] = __('e.g. 27 RUE PASTEUR');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['placeholder']= __('e.g. CABOURG');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['placeholder']= __('e.g. Vosges');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region']['placeholder']= __('e.g.Vosges');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['placeholder']= __('e.g. 12345');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['placeholder']= __('e.g. +33 (0)1 40 40 58 58');
                break;

            case 11: //NL
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['placeholder'] = __('e.g. Overtoom 74');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['placeholder']= __('e.g. Drachten');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['placeholder']= __('e.g. 1234 AB');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['placeholder']= __('e.g. +31 (0)20 555 1111');
                break;


            default:
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['placeholder']= __('e.g. Haymarket');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['placeholder']= __('e.g. New South Wales');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region']['placeholder']= __('e.g. New South Wales');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['placeholder']= __('e.g. 2000');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['placeholder']= __('e.g. 02 1234 5678');
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['notice']= __(' '); //used to trick the country validation for DT-663
                $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['class']= 'country-validation-note';
                break;

        }
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['notice']= __('In case we need to contact you for the delivery');
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['class']= 'note';

        // @codingStandardsIgnoreEnd
        return $result;

    }
}
