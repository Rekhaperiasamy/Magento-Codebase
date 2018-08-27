<?php
namespace Orange\Checkout\Plugin;

use Magento\Framework\Exception\LocalizedException;

class CartPlugin
{
  /**
   * @var \Magento\Quote\Model\Quote
   */
    protected $_checkoutSession;

    protected $request;

    /**
     * Plugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function afterAddProduct(\Magento\Checkout\Model\Cart $subject, $result)
    {
      $productID = $this->request->getParam('sku', 0);
      if (empty($productID)) {
        $productSku = array();
        $productId = $this->request->getParam('id', 0);
        $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($productId);
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($productId, true);
        foreach ($requiredChildrenIds as $ids) {
          foreach ($ids as $id) {
            $childProducts = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);
            $productSku[] = $childProducts->getSku();
          }
        }
        $productID = $productSku;
      }
      $this->_checkoutSession->setTealiumEventType("add");
      $this->_checkoutSession->setAddCartProduct($productID);
    }

    public function beforeRemoveItem(\Magento\Checkout\Model\Cart $subject, $result)
    {
      $data = $this->request->getPostValue();
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
      $itemsCollection = $cart->getQuote()->getItemById($data['id']);
      if(!empty($itemsCollection))
      {
        $productId = $itemsCollection->getProduct()->getSku();

        if (strpos($productId, '+')) {
          $productId = explode('+', $productId);
        }
        $this->_checkoutSession->setTealiumEventType("remove");
        $this->_checkoutSession->setRemovedCartItem($productId);
      }
    }
}
