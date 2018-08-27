<?php
namespace Orange\StockManagement\Controller\Adminhtml\Stock;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;
    protected $stock;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(\Orange\StockManagement\Model\Stock $stock, Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->stock             = $stock;
    }
    
    public function execute()
    {
        //$this->stock->updatestock();
        /* $stock = $this->stock;
        $stock->setProductSku('989038774');
        $stock->setProductQty('10');
        $stock->save(); */
        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->setActiveMenu('Orange_Stock::stock');
        $this->resultPage->getConfig()->getTitle()->set((__('Stock')));
        return $this->resultPage;
    }
}