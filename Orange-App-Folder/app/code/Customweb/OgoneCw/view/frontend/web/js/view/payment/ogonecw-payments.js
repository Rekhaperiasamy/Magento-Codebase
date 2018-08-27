/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_OgoneCw
 * 
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
        	
			{
			    type: 'ogonecw_mastercard',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_mastercard-method'
			},
			{
			    type: 'ogonecw_creditcard',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_creditcard-method'
			},
			{
			    type: 'ogonecw_acceptgiro',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_acceptgiro-method'
			},
			{
			    type: 'ogonecw_airplus',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_airplus-method'
			},
			{
			    type: 'ogonecw_americanexpress',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_americanexpress-method'
			},
			{
			    type: 'ogonecw_aurore',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_aurore-method'
			},
			{
			    type: 'ogonecw_cartebleue',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_cartebleue-method'
			},
			{
			    type: 'ogonecw_cofinoga',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_cofinoga-method'
			},
			{
			    type: 'ogonecw_dankort',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_dankort-method'
			},
			{
			    type: 'ogonecw_diners',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_diners-method'
			},
			{
			    type: 'ogonecw_discovercard',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_discovercard-method'
			},
			{
			    type: 'ogonecw_jcb',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_jcb-method'
			},
			{
			    type: 'ogonecw_lasercard',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_lasercard-method'
			},
			{
			    type: 'ogonecw_maestrouk',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_maestrouk-method'
			},
			{
			    type: 'ogonecw_solocard',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_solocard-method'
			},
			{
			    type: 'ogonecw_uatp',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_uatp-method'
			},
			{
			    type: 'ogonecw_visa',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_visa-method'
			},
			{
			    type: 'ogonecw_bcmc',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_bcmc-method'
			},
			{
			    type: 'ogonecw_uneurocom',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_uneurocom-method'
			},
			{
			    type: 'ogonecw_maestro',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_maestro-method'
			},
			{
			    type: 'ogonecw_postfinancecard',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_postfinancecard-method'
			},
			{
			    type: 'ogonecw_amazoncheckout',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_amazoncheckout-method'
			},
			{
			    type: 'ogonecw_belfiusdirectnet',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_belfiusdirectnet-method'
			},
			{
			    type: 'ogonecw_cashticket',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_cashticket-method'
			},
			{
			    type: 'ogonecw_cbconline',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_cbconline-method'
			},
			{
			    type: 'ogonecw_centeaonline',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_centeaonline-method'
			},
			{
			    type: 'ogonecw_edankort',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_edankort-method'
			},
			{
			    type: 'ogonecw_eps',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_eps-method'
			},
			{
			    type: 'ogonecw_fidorpay',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_fidorpay-method'
			},
			{
			    type: 'ogonecw_giropay',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_giropay-method'
			},
			{
			    type: 'ogonecw_ideal',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_ideal-method'
			},
			{
			    type: 'ogonecw_inghomepay',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_inghomepay-method'
			},
			{
			    type: 'ogonecw_kbconline',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_kbconline-method'
			},
			{
			    type: 'ogonecw_mpass',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_mpass-method'
			},
			{
			    type: 'ogonecw_paysafecard',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_paysafecard-method'
			},
			{
			    type: 'ogonecw_postfinanceefinance',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_postfinanceefinance-method'
			},
			{
			    type: 'ogonecw_directdebits',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_directdebits-method'
			},
			{
			    type: 'ogonecw_intersolve',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_intersolve-method'
			},
			{
			    type: 'ogonecw_pingping',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_pingping-method'
			},
			{
			    type: 'ogonecw_tunz',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_tunz-method'
			},
			{
			    type: 'ogonecw_cashuprepaid',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_cashuprepaid-method'
			},
			{
			    type: 'ogonecw_paypal',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_paypal-method'
			},
			{
			    type: 'ogonecw_directebanking',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_directebanking-method'
			},
			{
			    type: 'ogonecw_openinvoice',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_openinvoice-method'
			},
			{
			    type: 'ogonecw_masterpass',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_masterpass-method'
			},
			{
			    type: 'ogonecw_banktransfer',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_banktransfer-method'
			},
			{
			    type: 'ogonecw_instalmentinvoice',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_instalmentinvoice-method'
			},
			{
			    type: 'ogonecw_threexcb',
			    component: 'Customweb_OgoneCw/js/view/payment/method-renderer/ogonecw_threexcb-method'
			}
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);