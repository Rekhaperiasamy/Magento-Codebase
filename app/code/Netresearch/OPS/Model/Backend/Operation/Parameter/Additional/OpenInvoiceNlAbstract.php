<?php
namespace Netresearch\OPS\Model\Backend\Operation\Parameter\Additional;

/**
 * @author      Paul Siedler <paul.siedler@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class OpenInvoiceNlAbstract implements AdditionalInterface
{
    protected $additionalParams = [];
    protected $opsDataHelper = null;
    protected $itemIdx = 1;

    /**
     * @var \Netresearch\OPS\Helper\Payment\Request
     */
    protected $oPSPaymentRequestHelper;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    public function __construct(
        \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper
    ) {
        $this->oPSPaymentRequestHelper = $oPSPaymentRequestHelper;
        $this->oPSHelper = $oPSHelper;
    }
    /**
     * @param \Magento\Sales\Model\AbstractModel $itemContainer
     *
     * @return array
     */
    public function extractAdditionalParams(\Magento\Sales\Model\AbstractModel $itemContainer)
    {
        if ($itemContainer instanceof \Magento\Sales\Model\Order\Invoice) {
            $this->_additionalParams = $this->oPSPaymentRequestHelper->extractOrderItemParameters($itemContainer);
        }

        return $this->additionalParams;
    }

    /**
     * extracts all necessary data from the invoice items
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return $this
     *
     * @deprecated
     */
    protected function extractFromInvoiceItems(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        foreach ($invoice->getItemsCollection() as $item) {
            /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
            $configurableProductTypeCode = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
            // filter out configurable products
            if ($item->getParentItemId()
                && $item->getParentItem()->getProductType() == $configurableProductTypeCode
                || $item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                continue;
            }
            $this->additionalParams['ITEMID' . $this->itemIdx]    = substr($item->getOrderItemId(), 0, 15);
            $this->additionalParams['ITEMNAME' . $this->itemIdx]  = substr($item->getName(), 0, 30);
            $this->additionalParams['ITEMPRICE' . $this->itemIdx] = $this->getOpsDataHelper()
                ->getAmount($item->getBasePriceInclTax());
            $this->additionalParams['ITEMQUANT' . $this->itemIdx] = $item->getQty();
            $this->additionalParams['ITEMVATCODE' . $this->itemIdx]
                                                                    =
                str_replace(',', '.', (string)(float)$item->getTaxPercent()) . '%';
            $this->additionalParams['TAXINCLUDED' . $this->itemIdx] = 1;
            ++$this->itemIdx;
        }

        return $this;
    }

    /**
     * extract the necessary data from the shipping data of the invoice
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return $this
     *
     * @deprecated
     */
    protected function extractFromInvoicedShippingMethod(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $amount = $invoice->getBaseShippingInclTax();
        if (0 < $amount) {
            $this->additionalParams['ITEMID' . $this->itemIdx]      = 'SHIPPING';
            $this->additionalParams['ITEMNAME' . $this->itemIdx]    =
                substr($invoice->getOrder()->getShippingDescription(), 0, 30);
            $this->additionalParams['ITEMPRICE' . $this->itemIdx]   = $this->getOpsDataHelper()->getAmount($amount);
            $this->additionalParams['ITEMQUANT' . $this->itemIdx]   = 1;
            $this->additionalParams['ITEMVATCODE' . $this->itemIdx] = $this->getShippingTaxRate($invoice) . '%';
            $this->additionalParams['TAXINCLUDED' . $this->itemIdx] = 1;
            ++$this->itemIdx;
        }

        return $this;
    }

    /**
     * retrieves used shipping tax rate
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return float
     *
     * @deprecated
     */
    protected function getShippingTaxRate(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $taxRate       = 0.0;
        $order         = $invoice->getOrder();

        $taxRate = (floatval($this->oPSPaymentRequestHelper->getShippingTaxRate($order)));

        return $taxRate;
    }

    /**
     * gets the ops data helper
     *
     * @return \Netresearch\OPS\Helper\Data
     */
    protected function getOpsDataHelper()
    {
        if (null === $this->opsDataHelper) {
            $this->opsDataHelper = $this->oPSHelper;
        }

        return $this->opsDataHelper;
    }

    /**
     * @param $itemContainer
     *
     * @deprecated
     */
    protected function extractFromDiscountData($invoice)
    {
        $amount = $invoice->getBaseDiscountAmount();
        if (0 > $amount) {
            $couponRuleName = 'DISCOUNT';
            $order          = $invoice->getOrder();
            if ($order->getCouponRuleName() && strlen(trim($order->getCouponRuleName())) > 0) {
                $couponRuleName = substr(trim($order->getCouponRuleName()), 0, 30);
            }
            $this->additionalParams['ITEMID' . $this->itemIdx]    = 'DISCOUNT';
            $this->additionalParams['ITEMNAME' . $this->itemIdx]  = $couponRuleName;
            $this->additionalParams['ITEMPRICE' . $this->itemIdx] = $this->getOpsDataHelper()->getAmount($amount);
            $this->additionalParams['ITEMQUANT' . $this->itemIdx]   = 1;
            $this->additionalParams['ITEMVATCODE' . $this->itemIdx] = $this->getShippingTaxRate($invoice) . '%';
            $this->additionalParams['TAXINCLUDED' . $this->itemIdx] = 1;
            ++$this->itemIdx;
        }
    }
}
