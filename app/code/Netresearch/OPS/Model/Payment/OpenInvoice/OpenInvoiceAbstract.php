<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Model\Payment\OpenInvoice;

/**
 * open invoice payment via Ingenico ePayments
 */
class OpenInvoiceAbstract extends \Netresearch\OPS\Model\Payment\PaymentAbstract
{
    protected $_needsCartDataForRequest = true;
    protected $_needsShipToParams = false;

    public function getEncoding()
    {
        $this->setEncoding($this->getConfigData('encoding'));
        return parent::getEncoding();
    }

    public function getMethodDependendFormFields($order, $requestParams = null)
    {
        $formFields = parent::getMethodDependendFormFields($order, $requestParams);

        // @codingStandardsIgnoreStart
        $birthday = new \DateTime($order->getCustomerDob());
        // @codingStandardsIgnoreEnd

        $gender = $order->getCustomerGender() == 1 ? 'M' : 'F';

        $billingAddress  = $order->getBillingAddress();
        $street = implode(' ', $billingAddress->getStreet());
        $regexp = '/^([^0-9]*)([0-9].*)$/';
        if (!preg_match($regexp, $street, $splittedStreet)) {
            $splittedStreet[1] = $street;
            $splittedStreet[2] = '';
        }

        $formFields['OWNERADDRESS']                     = trim($splittedStreet[1]);
        $formFields['ECOM_BILLTO_POSTAL_STREET_NUMBER'] = trim($splittedStreet[2]);

        $formFields['ECOM_BILLTO_POSTAL_NAME_FIRST']    = substr($billingAddress->getFirstname(), 0, 50);
        $formFields['ECOM_BILLTO_POSTAL_NAME_LAST']     = substr($billingAddress->getLastname(), 0, 50);
        $formFields['ECOM_SHIPTO_DOB']                  = $birthday->format('d/m/Y');
        $formFields['ECOM_CONSUMER_GENDER']             = $gender;

        return $formFields;
    }

    /**
     * @return string title for invoice termes configured in backend
     */
    public function getInvoiceTermsTitle()
    {
        return $this->getConfigData('invoice_terms_title');
    }

    /**
     * @return string url to the invoice terms configured in backend
     */
    public function getInvoiceTermsUrl()
    {
        return $this->getConfigData('invoice_terms_url');
    }

    /**
     * @return bool if invoice terms should be displayed in checkout
     */
    public function showInvoiceTermsLink()
    {
        return (bool) $this->getConfigData('show_invoice_terms');
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if ($quote && $quote->isVirtual()) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * Use shipping address for country check.
     *
     * @param string $country
     * @return bool
     */
    public function canUseForCountry($country)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();
        if ($quote && $quote->getShippingAddress()) {
            $country = $quote->getShippingAddress()->getCountry();
        }

        return parent::canUseForCountry($country);
    }
}
