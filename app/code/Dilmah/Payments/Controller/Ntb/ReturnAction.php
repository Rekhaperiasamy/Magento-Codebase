<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Controller\Ntb;

use Magento\Framework\Controller\ResultFactory;

class ReturnAction extends \Dilmah\Payments\Controller\Ntb\AbstractNtb
{
    /**
     * Return from PayPal and dispatch customer to order review page
     *
     * @return void|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->_initCheckout();
        $encryptedReceiptPay = $this->getRequest()->getParam('encryptedReceiptPay');
        if ($encryptedReceiptPay != '') {
            $transactionStatus = $this->_checkout->validateReceipt($encryptedReceiptPay);
            $this->saveHistoryReturnInfo();
            switch ($transactionStatus) {
                case \Dilmah\Payments\Model\Ntb\Checkout::ACCEPTED:
                    $this->_forward('placeOrder');
                    return;
                    break;
                default:
                    $this->messageManager->addError(
                        __('We can\'t process the payment approval. Gateway returned "%1"', $transactionStatus)
                    );
                    break;
            }
        }
        try {
            throw new \Exception('Could not find Encrypted Receipt');
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t process American Express approval.')
            );
        }

        return $resultRedirect->setPath('checkout/cart');
    }
}
