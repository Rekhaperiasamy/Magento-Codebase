/**
 * @package   OPS
 * @copyright 2017 Netresearch GmbH & Co. KG <http://www.netresearch.de>
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @license   OSL 3.0
 */
define([],function () {
   return function (HTP, hash) {

       var doc = document;
       var form = doc.createElement('form');

       if (HTP.getBrand()) {
           var brandElement = doc.createElement('input');
           brandElement.id = 'CARD.BRAND';
           brandElement.name = 'CARD.BRAND';
           brandElement.value = HTP.getBrand();
           form.appendChild(brandElement);
       }

       var pspidElement = doc.createElement('input');
       pspidElement.id = 'ACCOUNT.PSPID';
       pspidElement.name = 'ACCOUNT.PSPID';
       pspidElement.value = HTP.getPspid();

       var orderIdElement = doc.createElement('input');
       orderIdElement.name = 'ALIAS.ORDERID';
       orderIdElement.id = 'ALIAS.ORDERID';
       orderIdElement.value = HTP.getOrderId();

       var acceptUrlElement = doc.createElement('input');
       acceptUrlElement.name = 'PARAMETERS.ACCEPTURL';
       acceptUrlElement.id = 'PARAMETERS.ACCEPTURL';
       acceptUrlElement.value = HTP.getAliasAcceptUrl();

       var exceptionUrlElement = doc.createElement('input');
       exceptionUrlElement.name = 'PARAMETERS.EXCEPTIONURL';
       exceptionUrlElement.id = 'PARAMETERS.EXCEPTIONURL';
       exceptionUrlElement.value = HTP.getAliasExceptionUrl();

       var paramplusElement = doc.createElement('input');
       paramplusElement.name = 'PARAMETERS.PARAMPLUS';
       paramplusElement.id = 'PARAMETERS.PARAMPLUS';
       paramplusElement.value = 'RESPONSEFORMAT=JSON';

       var aliasElement = doc.createElement('input');
       aliasElement.name = 'ALIAS.ALIASID';
       aliasElement.id = 'ALIAS.ALIASID';
       aliasElement.value = '';

       if (HTP.getAliasManager()) {
           var storePermanentlyElement = doc.createElement('input');
           storePermanentlyElement.name = 'ALIAS.STOREPERMANENTLY';
           storePermanentlyElement.id = 'ALIAS.STOREPERMANENTLY';
           storePermanentlyElement.value = 'N';
           form.appendChild(storePermanentlyElement);
       }

       var paymentMethodElement = doc.createElement('input');
       paymentMethodElement.name = 'Card.PaymentMethod';
       paymentMethodElement.id = 'Card.PaymentMethod';
       paymentMethodElement.value = HTP.getPaymentMethod();

       var localeElement = doc.createElement('input');
       localeElement.name = 'Layout.Language';
       localeElement.id = 'Layout.Language';
       localeElement.value = HTP.getLocale();
       form.appendChild(localeElement);

       htpTemplateName = HTP.getHtpTemplate();
       if (typeof htpTemplateName !== 'undefined') {
           var htpTemplateElement = doc.createElement('input');
           htpTemplateElement.name = 'LAYOUT.TEMPLATENAME';
           htpTemplateElement.id = 'LAYOUT.TEMPLATENAME';
           htpTemplateElement.value = HTP.getHtpTemplate();
           form.appendChild(htpTemplateElement);
       }

       if (hash) {
           var hashElement = doc.createElement('input');
           hashElement.id = 'SHASIGNATURE.SHASIGN';
           hashElement.name = 'SHASIGNATURE.SHASIGN';
           hashElement.value = hash.toUpperCase();
           form.appendChild(hashElement);
       }

       if (window.FORM_KEY && !hash) {
           var formkeyElement = doc.createElement('input');
           formkeyElement.id = 'form_key';
           formkeyElement.name = 'form_key';
           formkeyElement.value = window.FORM_KEY;
           form.appendChild(formkeyElement);
       }

       form.id = 'ops_request_form';
       form.method = 'post';
       form.action = HTP.getUrl();
       var submit = document.createElement('submit');
       form.appendChild(submit);

       form.appendChild(pspidElement);
       form.appendChild(acceptUrlElement);
       form.appendChild(exceptionUrlElement);
       form.appendChild(orderIdElement);
       form.appendChild(paramplusElement);
       form.appendChild(aliasElement);
       form.appendChild(paymentMethodElement);

       return form;
   }
});
