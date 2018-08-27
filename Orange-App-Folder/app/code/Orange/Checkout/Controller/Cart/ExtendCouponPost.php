<?php
namespace Orange\Checkout\Controller\Cart;

class ExtendCouponPost extends \Magento\Checkout\Controller\Cart\CouponPost
{
	/**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $couponCode = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
			if (!$this->getRequest()->isAjax()) {
				return $this->_goBack();
			}
			else {
				$message = __("Ce code promo n'est pas valable.");
				if (!$this->getRequest()->isAjax()) {
					$this->messageManager->addError($message);
				}
				$response['status'] = 'fail';
				$response['message'] =$message;
				$this->getResponse()->representJson(
				$this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
				);
			}
        }

        try {
			$response = [];
			$storeCode = $this->_storeManager->getStore()->getCode();
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get('Magento\Framework\Escaper');
                if (!$itemsCount) {
                    if ($isCodeLengthValid) {
                        $coupon = $this->couponFactory->create();
                        $coupon->load($couponCode, 'code');
                        if ($coupon->getId()) {
                            $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
							$message = __(
                                    "Le code promo a été appliqué à votre commande.",
                                    $escaper->escapeHtml($couponCode)
                                );
							if (!$this->getRequest()->isAjax()) {
								$this->messageManager->addSuccess($message);
							}
							$response['status'] = 'success';
							$response['message'] = $message;
                        } else {
							$message = __(
                                    "Ce code promo n'est pas valable.",
                                    $escaper->escapeHtml($couponCode)
                                );
							if (!$this->getRequest()->isAjax()) {
								$this->messageManager->addError($message);
							}
							$response['status'] = 'failed';
							$response['message'] =$message;
                        }
                    } else {
						$message = __(
                                "Ce code promo n'est pas valable.",
                                $escaper->escapeHtml($couponCode)
                            );
						if (!$this->getRequest()->isAjax()) {
							$this->messageManager->addError($message);
						}
						$response['status'] = 'fail';
						$response['message'] = $message;
                    }
                } else {
                    if ($isCodeLengthValid && $couponCode == $cartQuote->getCouponCode()) {
						$message = __(
                                "Le code promo a été appliqué à votre commande.",
                                $escaper->escapeHtml($couponCode)
                            );
						if (!$this->getRequest()->isAjax()) {
							$this->messageManager->addSuccess($message);
						}
						$response['status'] = 'success';
						$response['message'] = $message;
                    } else {
						$message = __(
                                "Ce code promo n'est pas valable.",
                                $escaper->escapeHtml($couponCode)
                            );
						if (!$this->getRequest()->isAjax()) {
							$this->messageManager->addError($message);
						}
						$response['status'] = 'fail';
						$response['message'] = $message;
                        $this->cart->save();
                    }
                }
            } else if(!$codeLength) {
				// $message = __("Ce code promo n'est pas valable.");
				// if (!$this->getRequest()->isAjax()) {
					// $this->messageManager->addError($message);
				// }
				// $response['status'] = 'fail';
				// $response['message'] =$message;				
			} else {
				$message = __('Vous avez annulé le code de coupon.');
				if (!$this->getRequest()->isAjax()) {
					$this->messageManager->addSuccess($message);
				}
				$response['status'] = 'cancel';
				$response['message'] = $message;
            }
			$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug($couponCode);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
			$response['status'] = 'fail';
			$response['message'] = $e->getMessage();
			if (!$this->getRequest()->isAjax()) {
				$this->messageManager->addError($e->getMessage());
			}
        } catch (\Exception $e) {
			$message = __('Nous ne pouvons pas appliquer le code de coupon.');
			if (!$this->getRequest()->isAjax()) {
				$this->messageManager->addError($message);
			}
			$response['status'] = 'fail';
			$response['message'] = $message;
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
		if ($this->getRequest()->isAjax()) {
			$quoteData = $this->_checkoutSession->getQuote()->getData();
			$response['grandTotal']=$quoteData['grand_total'];
			$response['coupon_code']=$quoteData['coupon_code'];
			$response['coupon_description']=$quoteData['coupon_description'];
			$response['subtotal']=$quoteData['subtotal'];
			$response['subtotal_discount']=$quoteData['subtotal_with_discount'];
			$response['subscription_discount_total']=$quoteData['subscription_total'];
			$response['subscription_total']=$quoteData['subscription_total'];
			$response['subsidy']=$quoteData['subsidy_discount'];
			
			$this->getResponse()->representJson(
				$this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
			);
		}
		else {
			return $this->_goBack();
		}
    }
}