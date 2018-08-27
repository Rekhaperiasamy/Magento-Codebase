<?php
namespace Netresearch\OPS\Model\Backend\Operation\Refund\Additional;

/**
 * @author      Paul Siedler <paul.siedler@netresearch.de>
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class OpenInvoiceNl extends \Netresearch\OPS\Model\Backend\Operation\Parameter\Additional\OpenInvoiceNlAbstract
{
    protected $creditmemo = [];
    protected $amount = 0;
    protected $refundHelper = null;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    protected $salesOrderInvoiceFactory;

    /**
     * @var \Netresearch\OPS\Helper\Order\Refund
     */
    protected $oPSOrderRefundHelper;

    public function __construct(
        \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Magento\Sales\Model\Order\InvoiceFactory $salesOrderInvoiceFactory,
        \Netresearch\OPS\Helper\Order\Refund $oPSOrderRefundHelper
    ) {
        parent::__construct($oPSPaymentRequestHelper, $oPSHelper);
        $this->salesOrderInvoiceFactory = $salesOrderInvoiceFactory;
        $this->oPSOrderRefundHelper = $oPSOrderRefundHelper;
    }
    
    /**
     * @param \Magento\Sales\Model\AbstractModel $itemContainer
     * @return array
     */
    public function extractAdditionalParams(\Magento\Sales\Model\AbstractModel $itemContainer = null)
    {
        $invoice = null;
        if ($itemContainer instanceof \Magento\Sales\Model\Order\Invoice && $itemContainer) {
            $invoice = $itemContainer;
        } elseif ($itemContainer instanceof \Magento\Sales\Block\Order\Creditmemo && $itemContainer) {
            $invoice = $this->salesOrderInvoiceFactory->create()->load($itemContainer->getInvoiceId());
        }

        if ($invoice == null) {
            // if invoice is not set we load id hard from the request params
            $invoice = $this->getRefundHelper()->getInvoiceFromCreditMemoRequest();
        }
        $this->creditmemo = $this->getRefundHelper()->getCreditMemoFromRequest();

        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
            $this->extractFromCreditMemoItems($invoice);
            // We dont extract from discount data for the moment, because partial refunds are a problem
            $this->extractFromInvoicedShippingMethod($invoice);
            $this->extractFromAdjustments($invoice);
            // Overwrite amount to fix Magentos rounding problems (eg +1ct)
            $this->additionalParams['AMOUNT'] = $this->amount;
        }

        return $this->additionalParams;
    }

    /**
     * extracts all data from the invoice according to the credit memo items
     *
     * @param $itemContainer
     */
    protected function extractFromCreditMemoItems(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        foreach ($invoice->getItemsCollection() as $item) {
            if (array_key_exists($item->getOrderItemId(), $this->creditmemo['items'])) {
                $configurableTypeCode = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
                if ($item->getParentItemId()
                    && $item->getParentItem()->getProductType() == $configurableTypeCode
                    || $item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                    continue;
                }
                $this->additionalParams['ITEMID' . $this->itemIdx]    = substr($item->getOrderItemId(), 0, 15);
                $this->additionalParams['ITEMNAME' . $this->itemIdx]  = substr($item->getName(), 0, 30);
                $this->additionalParams['ITEMPRICE' . $this->itemIdx] = $this->getOpsDataHelper()->getAmount(
                    $item->getBasePriceInclTax()
                );
                $itemAmount = $this->getOpsDataHelper()->getAmount($item->getBasePriceInclTax());
                $creditMemoItemQty = $this->creditmemo['items'][$item->getOrderItemId()]['qty'];
                $this->amount += $itemAmount * $creditMemoItemQty;
                $this->additionalParams['ITEMQUANT' . $this->itemIdx] = $creditMemoItemQty;
                $this->additionalParams['ITEMVATCODE' . $this->itemIdx]
                                                                        =
                    str_replace(',', '.', (string)(float)$item->getTaxPercent()) . '%';
                $this->additionalParams['TAXINCLUDED' . $this->itemIdx] = 1;
                ++$this->itemIdx;
            }
        }
    }

    protected function extractFromInvoicedShippingMethod(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        if ($this->creditmemo['shipping_amount'] > 0) {
            $this->additionalParams['ITEMID' . $this->itemIdx]    = 'SHIPPING';
            $this->additionalParams['ITEMNAME' . $this->itemIdx]  =
                substr($invoice->getOrder()->getShippingDescription(), 0, 30);
            $this->additionalParams['ITEMPRICE' . $this->itemIdx] = $this->getOpsDataHelper()
                ->getAmount($this->creditmemo['shipping_amount']);
            $this->amount += $this->getOpsDataHelper()->getAmount($this->creditmemo['shipping_amount']);
            $this->additionalParams['ITEMQUANT' . $this->itemIdx]   = 1;
            $this->additionalParams['ITEMVATCODE' . $this->itemIdx] = $this->getShippingTaxRate($invoice) . '%';
            $this->additionalParams['TAXINCLUDED' . $this->itemIdx] = 1;
            ++$this->itemIdx;
        }
    }

    /**
     * extracts all data from the adjustment fee/refund
     *
     * @param $invoice
     */
    protected function extractFromAdjustments(\Magento\Sales\Model\Order\Invoice $invoice)
    {

        if ($this->creditmemo['adjustment_positive'] > 0) {
            $this->additionalParams['ITEMID' . $this->itemIdx]    = 'ADJUSTREFUND';
            $this->additionalParams['ITEMNAME' . $this->itemIdx]  = 'Adjustment Refund';
            $this->additionalParams['ITEMPRICE' . $this->itemIdx] = $this->getOpsDataHelper()
                ->getAmount($this->creditmemo['adjustment_positive']);
            $this->amount += $this->getOpsDataHelper()->getAmount($this->creditmemo['adjustment_positive']);
            $this->additionalParams['ITEMQUANT' . $this->itemIdx]   = 1;
            $this->additionalParams['ITEMVATCODE' . $this->itemIdx] = $this->getShippingTaxRate($invoice) . '%';
            $this->additionalParams['TAXINCLUDED' . $this->itemIdx] = 1;
            ++$this->itemIdx;
        }
        if ($this->creditmemo['adjustment_negative'] > 0) {
            $this->additionalParams['ITEMID' . $this->itemIdx]    = 'ADJUSTFEE';
            $this->additionalParams['ITEMNAME' . $this->itemIdx]  = 'Adjustment Fee';
            $this->additionalParams['ITEMPRICE' . $this->itemIdx] = $this->getOpsDataHelper()
                ->getAmount(-$this->creditmemo['adjustment_negative']);
            $this->amount += $this->getOpsDataHelper()->getAmount(-$this->creditmemo['adjustment_negative']);
            $this->additionalParams['ITEMQUANT' . $this->itemIdx]   = 1;
            $this->additionalParams['ITEMVATCODE' . $this->itemIdx] = $this->getShippingTaxRate($invoice) . '%';
            $this->additionalParams['TAXINCLUDED' . $this->itemIdx] = 1;
            ++$this->itemIdx;
        }
    }

    /**
     * gets the refund helper
     *
     * @return \Netresearch\OPS\Helper\Order\Refund|null
     */
    protected function getRefundHelper()
    {
        if (null === $this->refundHelper) {
            $this->refundHelper = $this->oPSOrderRefundHelper;
        }

        return $this->refundHelper;
    }
}
