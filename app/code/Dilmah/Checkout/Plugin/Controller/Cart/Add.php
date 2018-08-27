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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Checkout\Controller\Cart\Add
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
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
    }

    /**
     * Add product to shopping cart action
     *
     * @param \Magento\Checkout\Controller\Cart\Add $subject
     * @param \Closure $proceed
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings("unused")
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Cart\Add $subject,
        \Closure $proceed
    ) {
        if (!$subject->_formKeyValidator->validate($subject->getRequest())) {
            return $subject->resultRedirectFactory->create()->setPath('*/*/');
        }
        $engraveId = null;
        $params = $subject->getRequest()->getParams();
        if (!isset($params['qty'])) {
            $params['qty'] = 1;
        }
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' =>
                        $subject->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $subject->_initProduct();

            //get engrave custom option id
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

            //match with the existing cart item custom option id's and find if there's engraving text available.
            // if so the qty cannot exceed 1
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

            //Add bundle products with options to the cart
            if (!isset($params['bundle_option'])) {
                if ($product->getTypeId() == 'bundle' &&
                    $product->getData(\Dilmah\Catalog\Helper\Data::IS_PACK_PRODUCT_ATTRIBUTE) == 1
                ) {
                    $selection = $this->getFirstSelectionItemForBundleProduct($product);
                    $optionArr[$selection->getOptionId()] = $selection->getSelectionId();
                    $params['bundle_option'] = $optionArr;
                }
            }
            $this->_coreRegistry->register('added_product', $product);
            $related = $subject->getRequest()->getParam('related_product');

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
                    $subject->messageManager->addError(
                        $subject->_objectManager->get('Magento\Framework\Escaper')->escapeHtml(
                            // @codingStandardsIgnoreStart
                            sprintf(__('Please select minimum of %s products or multiples of %s products from the options.'), $packSize)
                            // @codingStandardsIgnoreEnd
                        )
                    );
                    return $subject->goBack();
                } else {
                    $params['units'] = $bundleQty / $packSize;
                }
            }

            /*
             * DT-1931
             * pack products are not allowed to buy in-between pack sizes. Ex: A product with pack size 6 is not allowed
             * to buy 1 or 2 packs, it has be in multiples of pack_size
             */
            if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                && $product->getData(\Dilmah\Catalog\Helper\Data::IS_PACK_PRODUCT_ATTRIBUTE) == 1
                && !empty($params['bundle_option_qty'])
                && $packSize > 0
            ) {
                $bundleQty = 0;
                foreach ($params['bundle_option_qty'] as $optionQty) {
                    $bundleQty += $optionQty;
                }
                if ($bundleQty % $packSize != 0) {
                    $obj = new \Magento\Framework\DataObject();
                    $obj->setStatus(0);
                    $obj->setMessage(sprintf(__('Please select multiples of %s packs.'), $packSize));
                    $subject->messageManager->addError(
                        $subject->_objectManager->get('Magento\Framework\Escaper')->escapeHtml(
                        // @codingStandardsIgnoreStart
                            $obj->getMessage()
                        // @codingStandardsIgnoreEnd
                        )
                    );
                    return $subject->goBack(null, null, $obj);
                }

            }

            /**
             * Check product availability
             */
            if (!$product) {
                return $subject->goBack();
            }

            $subject->cart->addProduct($product, $params);
            if (!empty($related)) {
                $subject->cart->addProductsByIds(explode(',', $related));
            }

            $subject->cart->save();

            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
            $subject->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $subject->getRequest(), 'response' => $subject->getResponse()]
            );

            if (!$subject->_checkoutSession->getNoCartRedirect(true)) {
                if (!$subject->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $subject->messageManager->addSuccessMessage($message);
                }
                return $subject->goBack(null, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($subject->_checkoutSession->getUseNotice(true)) {
                $subject->messageManager->addNotice(
                    $subject->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $subject->messageManager->addError(
                        $subject->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                    );
                }
            }

            $url = $subject->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $subject->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                $url = $subject->_redirect->getRedirectUrl($cartUrl);
            }

            return $subject->goBack($url);

        } catch (\Exception $e) {
            $subject->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $subject->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $subject->goBack();
        }
    }

    /**
     * Get first Selection Items for bundle products
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getFirstSelectionItemForBundleProduct($product)
    {
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );
        $selection = $selectionCollection->getFirstItem();

        return $selection;
    }
}
