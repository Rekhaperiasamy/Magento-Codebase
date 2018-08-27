/*browser:true*/
/*global define*/
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
                type: 'ops_directDebit',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-directDebit'
            },
            {
                type: 'ops_postFinanceEFinance',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_eDankort',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_kbcOnline',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_iDeal',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-eideal'
            },
            {
                type: 'ops_belfiusDirectNet',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_bankTransfer',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-bankTransfer'
            },
            {
                type: 'ops_openInvoiceDe',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-openInvoice'
            },
            {
                type: 'ops_openInvoiceNl',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-openInvoice'
            },
            {
                type: 'ops_openInvoiceAt',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-openInvoice'
            },
            {
                type: 'ops_cbcOnline',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_giroPay',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_Masterpass',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_interSolve',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-interSolve'
            },
            {
                type: 'ops_cashU',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_paypal',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_eps',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_pingPing',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_fortisPayButton',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_directEbanking',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-directEbanking'
            },
            {
                type: 'ops_cc',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-cc'
            },
            {
                type: 'ops_dc',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-dc'
            },
            {
                type: 'ops_ingHomePay',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_paysafecard',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_kwixoCredit',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-kwixo'
            },
            {
                type: 'ops_kwixoApresReception',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-kwixo'
            },
            {
                type: 'ops_kwixoComptant',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-kwixo'
            },
            {
                type: 'ops_flex',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-flex'
            },
            {
                type: 'ops_chinaUnionPay',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            },
            {
                type: 'ops_BCMC',
                component: 'Netresearch_OPS/js/view/payment/method-renderer/ops-redirect'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
