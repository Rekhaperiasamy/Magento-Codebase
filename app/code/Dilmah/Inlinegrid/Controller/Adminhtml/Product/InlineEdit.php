<?php
namespace Dilmah\Inlinegrid\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class InlineEdit extends \Magento\Backend\App\Action {

    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    private $product;
    protected $productRepository;
    protected $resultJsonFactory;
    protected $dataObjectHelper;
    protected $logger;
	protected $stockRegistry;

    public function __construct(
    Action\Context $context, ProductRepositoryInterface $productRepository,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
			\Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
            \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
            \Psr\Log\LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
		 $this->stockRegistry = $stockRegistry;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute() {
        $resultJson = $this->resultJsonFactory->create();

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                        'messages' => [__('Please correct the data sent.')],
                        'error' => true,
            ]);
        }

        
	  foreach ($postItems as $productId => $productRow) {
            $this->setProduct($this->productRepository->getById($productId));
			
			if (isset ($productRow['qty'])){
			

            // saving
			$sku = $this->product->getSku();
			$stockItem = $this->stockRegistry->getStockItemBySku($sku);
			$qty = $productRow['qty'];
			$sku = $productRow['sku'];
            $stockItem->setQty($qty);
            $stockItem->setIsInStock((bool)$qty); 
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
            
        }
			}

        return $resultJson->setData([
                    'messages' => $this->getErrorMessages(),
                    'error' => $this->isErrorExists()
        ]);
		
    }

    protected function getErrorMessages() {
        $messages = [];
        foreach ($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    protected function isErrorExists() {
        return (bool) $this->getMessageManager()->getMessages(true)->getCount();
    }

    protected function setProduct(ProductInterface $product) {
        $this->product = $product;
        return $this;
    }

    protected function getProduct() {
        return $this->product;
    }
}