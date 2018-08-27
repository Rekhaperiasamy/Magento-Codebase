<?php
namespace Orange\Catalogversion\Controller\Adminhtml\Catalogversionproduct;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\ProductRepository;
class Draft extends \Magento\Backend\App\Action {
	protected $_productRepository;
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Catalog\Model\Product $productModel,
			\Magento\Catalog\Model\Category $categoryModel,
			ProductRepository $productRepository,
			\Magento\Framework\App\ResourceConnection $resourceConnection
	){
		parent::__construct($context);
		$this->_auth = $context->getAuth();
		$this->productModel = $productModel;
		$this->categoryModel = $categoryModel;
		$this->_productRepository = $productRepository;
		$this->resourceConnection = $resourceConnection;
	}
    public function execute() {
		print_r($_REQUEST);
        $id = $this->getRequest()->getParam('id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load($id);
        $orgData = $product->getData();
        if (!empty($orgData['entity_id'])) {
            $productid = $orgData['entity_id'];
            $sku = $orgData['sku'];
            $name = $orgData['name'];
            $status = $orgData['status'];
            $price = $orgData['price'];
            $urlkey = $orgData['url_key'];
            $metatitle          = !empty($orgData['meta_title']) ? $orgData['meta_title'] : '';
			$metakeyword		= !empty($orgData['meta_keyword']) ? $orgData['meta_keyword'] : '';
			$metadescription	= !empty($orgData['meta_description']) ? $orgData['meta_description'] : '';
            $description = !empty($orgData['description']) ? $orgData['description'] : '';
            $shortdescription = !empty($orgData['short_description']) ? $orgData['short_description'] : '';

            /** Category Collection * */
            $categories = $product->getCategoryIds();
            $category = [];
            foreach ($categories as $key => $val) {
                $categoryId = $val;
                $categoryData = $this->categoryModel->load($categoryId)->getData();

                $category[] = $categoryData['name'];
            }
            $categories = implode(",", $category);

            /*             * * start related ,cross sell and upsell product ** */
            $relatedProducts = $product->getRelatedProducts();
            $upSellProducts = $product->getUpSellProducts();
            $crossSellProducts = $product->getCrossSellProducts();
            $relatedProducsArray = array();
            $upSellProducsArray = array();
            $crossProducsArray = array();

            if (!empty($relatedProducts)) {

                foreach ($relatedProducts as $relatedProduct) {
                    $relatedProducsArray[] = $relatedProduct->getSku();
                }
            }


            if (!empty($upSellProducts)) {

                foreach ($upSellProducts as $upSellProduct) {

                    $upSellProducsArray[] = $upSellProduct->getSku();
                }
            }

            if (!empty($crossSellProducts)) {

                foreach ($crossSellProducts as $crossSellProduct) {

                    $crossProducsArray[] = $crossSellProduct->getSku();
                }
            }

            $_product = $objectManager->get('Magento\Catalog\Model\Product')->load($id);

            if (!empty($orgData['visibility'])) {

                $attributeCode = 'visibility';
                $valueforOptionID = $orgData[$attributeCode];
                $attr = $_product->getResource()->getAttribute($attributeCode);
                $visibilityValue = $attr->getSource()->getOptionText($valueforOptionID);
            } else {

                $visibilityValue = '';
            }
            $visibility = $visibilityValue;

            /*             * * Inventory Details ** */
            $StockAndQuantity = $orgData['quantity_and_stock_status'];

            if (isset($StockAndQuantity['is_in_stock'])) {
                $oldStatus = $StockAndQuantity['is_in_stock'];

                if ($oldStatus) {
                    $stock = 'In Stock';
                } else {
                    $stock = 'Out of Stock';
                }
            } else {
                $oldStatus = $product->getOrigData('quantity_and_stock_status');
                if ($oldStatus) {
                    $stock = 'In Stock';
                } else {
                    $stock = 'Out of Stock';
                }
            }

            $quantity = $StockAndQuantity['qty'];

            /*             * * Video Managment ** */
            $galleryImageURL = '';
            $galleryImages = $product->getMediaGalleryImages();

            foreach ($galleryImages as $images) {
                $imageURL = $images->getUrl();
                $galleryImageURL[] = $imageURL;
            }
            if ($galleryImageURL != "") {
                $imageURLPath = implode(",", $galleryImageURL);
            }
            $relatedProductSKU = serialize($relatedProducsArray);
            $upsellProductSKU = serialize($upSellProducsArray);
            $crossSellProductSKU = serialize($crossProducsArray);
            //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $sql = "Select * FROM eav_attribute where  entity_type_id=4 AND is_user_defined";
            $attributesCollection = $connection->fetchAll($sql);
            $attributes = [];
            $custom_attribues = [];
            /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
            foreach ($attributesCollection as $attribute) {

                $attributeCode = $attribute['attribute_code'];
                $attributeLabel = $attribute['frontend_label'];
                if (isset($orgData[$attributeCode])) {
                    $value = '';
                    if ($attribute['frontend_input'] == 'select') {

                        $valueforOptionID = $orgData[$attributeCode];
                        $attr = $_product->getResource()->getAttribute($attributeCode);
                        $optionText = $attr->getSource()->getOptionText($valueforOptionID);
                        $value = $optionText;
                    } elseif ($attribute['frontend_input'] == 'boolean') {

                        if ($orgData[$attributeCode]) {
                            $value = 'yes';
                        } else {
                            $value = 'No';
                        }
                    } else {
                        $value = $orgData[$attributeCode];
                    }
                    $custom_attribues[$attributeLabel] = $value;
                }
            }
            $finalData = serialize($custom_attribues);
            $tableName = $resource->getTableName('catalogversion_draft');
			$draft = $objectManager->create('Orange\Catalogversion\Model\Catalogdraft');
			$draft->setProductid($productid);
			$draft->setSku($sku);
			$draft->setName($name);
			$draft->setStatus($status);
			$draft->setPrice($price);
			$draft->setQuantity($quantity);
			$draft->setCustomAttribueInfo($finalData);
			$draft->setStock($stock);
			$draft->setCategories($categories);
			$draft->setVisibility($visibility);
			$draft->setUrlkey($urlkey);
			$draft->setMetatitle($metatitle);
			$draft->setMetakeyword($metakeyword);
			$draft->setMetadescription($metadescription);
			$draft->setDescription($description);
			$draft->setShortdescription($description);
			 if ($galleryImageURL != "") {
			 $imageURLPath = implode(",", $galleryImageURL);
			$draft->setImageurls($imageURLPath);
			}
			else
			{
				$draft->setImageurls('');
			}
			$draft->setRelatedProductInfo($relatedProductSKU);
			$draft->setUpsellProductInfo($upsellProductSKU);
			$draft->setCrosssellProductInfo($crossSellProductSKU);
			$draft->save();
           
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('catalog/product/edit', ['id' => $id,]);
        return $resultRedirect;
    }
}
