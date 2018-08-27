<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Checkout
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Checkout\Plugin\Controller\Cart;

use Magento\Checkout\Model\Cart as CustomerCart;

/**
 * Class UpdateItemOptions
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateItemOptions extends \Magento\Checkout\Controller\Cart\UpdateItemOptions
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Add constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
    }

    /**
     * Function aroundExecute
     * @param \Magento\Checkout\Controller\Cart\UpdateItemOptions $subject
     * @param \Closure $proceed
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings("unused")
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Cart\UpdateItemOptions $subject,
        \Closure $proceed
    ) {
        $id = (int)$this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();
        $engraveId = null;
        if (!isset($params['options'])) {
            $params['options'] = [];
        }
        if (!isset($params['qty'])) {
            $params['qty'] = 1;
        }
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $this->cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the quote item.'));
            }

            $product = $quoteItem->getProduct();
            if ($product->getIsEngravable() == 1) {
                if ($options = $product->getOptions()):
                    foreach ($options as $option) :
                        $pos = strpos(strtolower($option->getTitle()), 'engrav');
                        if ($pos !== false) :
                            $engraveId = $option->getId();
                        endif;
                    endforeach;
                endif;
            }

            if (isset($params['options'])) {
                $arrOptions = $params['options'];
                foreach ($arrOptions as $key => $arrOption) {
                    if ($key == $engraveId) {
                        if ($arrOption != '') {
                            if ($params['qty'] > 1) {
                                $subject->messageManager->addError(
                                    $subject->_objectManager->get('Magento\Framework\Escaper')->escapeHtml(
                                    // @codingStandardsIgnoreStart
                                        sprintf(__('Maximum engraving product quantity could be 1.'))
                                    // @codingStandardsIgnoreEnd
                                    )
                                );
                                return $subject->goBack();
                            }
                        }
                    }
                }
            }

            $this->_coreRegistry->register('added_product', $product);

            $packSize = $product->getData(\Dilmah\Catalog\Helper\Data::PACK_SIZE_ATTRIBUTE);
            if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                && $product->getData(\Dilmah\Catalog\Helper\Data::IS_COMBO_PRODUCT_ATTRIBUTE) == 1
                && !empty($params['bundle_option_qty'])
                && $packSize > 0
            ) {
                $bundleQty = 0;
                foreach ($params['bundle_option_qty'] as $optionQty) {
                    $bundleQty += $optionQty;
                }

                if ($bundleQty % $packSize != 0) {
                    $this->messageManager->addError(
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml(
                            __(
                                'Please select minimum of %1 products or multiples of %1 products from the options.',
                                $packSize
                            )
                        )
                    );
                    return $this->_goBack();
                } else {
                    $params['units'] = $bundleQty / $packSize;
                }
            }

            $item = $this->cart->updateItem($id, new \Magento\Framework\DataObject($params));
            if (is_string($item)) {
                throw new \Magento\Framework\Exception\LocalizedException(__($item));
            }
            if ($item->getHasError()) {
                throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            $this->_eventManager->dispatch(
                'checkout_cart_update_item_complete',
                ['item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );
            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        '%1 was updated in your shopping cart.',
                        $this->_objectManager->get('Magento\Framework\Escaper')
                            ->escapeHtml($item->getProduct()->getName())
                    );
                    $this->messageManager->addSuccess($message);
                }
                return $this->_goBack($this->_url->getUrl('checkout/cart'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError($message);
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);
            if ($url) {
                return $this->resultRedirectFactory->create()->setUrl($url);
            } else {
                $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($cartUrl));
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the item right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_goBack();
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
