<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Sales
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Sales\Model\Order\Pdf;

/**
 * Sales Order Invoice PDF model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }
            /* Add Footer */
            $this->insertFooter($page, $invoice->getStore());
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * footer area for invoice pdf
     *
     * @param $page
     * @param $store
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function insertFooter($page, $store)
    {
        $font = $this->_setFontRegular($page, 10);
        $this->y -= 30;
        foreach (explode(
                     "\n",
                     $this->_scopeConfig->getValue(
                         'sales/identity/invoice_footer',
                         \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                         $store
                     )
                 ) as $value) {
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 200, true, true) as $_value) {
                    $page->drawText(
                        trim(strip_tags($_value)),
                        $this->getAlignCenter($_value, 100, 400, $font, 10),
                        $this->y,
                        'UTF-8'
                    );
                    $this->y -= 10;
                }
            }
        }
    }
}
