<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use FME\Geoipultimatelock\Model\ResourceModel\Rule\CollectionFactory;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        //echo '<pre>';print_r($collection->getData());exit;
        $collectionSize = $collection->getSize();

        foreach ($collection as $item) {
            $item->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return void
     */
//    public function execute() {
//        // Get IDs of the selected news
//        $geoipDefaultStoreIds = $this->getRequest()->getParams(); echo '<pre>';print_r($geoipDefaultStoreIds);exit;
//
//        foreach ($geoipDefaultStoreIds as $id) {
//            try {
//                /** @var $geoipdefaultstoreModel \FME\Geoipultimatelock\Model\Geoipultimatelock */
//                $geoipDefaultStoreModel = $this->_geoipDefaultStoreFactory->create();
//                $geoipDefaultStoreModel->load($id)
//                        ->delete();
//            } catch (\Exception $e) {
//                $this->messageManager->addError($e->getMessage());
//            }
//        }
//
//        if (count($geoipDefaultStoreIds)) {
//            $this->messageManager->addSuccess(
//                    __('A total of %1 record(s) were deleted.', count($geoipDefaultStoreIds))
//            );
//        }
//
//        $this->_redirect('*/*/index');
//    }
}
