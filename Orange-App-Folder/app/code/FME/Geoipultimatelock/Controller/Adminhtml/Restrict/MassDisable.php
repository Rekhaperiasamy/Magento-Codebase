<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Controller\Adminhtml\Restrict;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use FME\Geoipultimatelock\Model\ResourceModel\Restrict\CollectionFactory;

/**
 * Class MassDisable
 */
class MassDisable extends \Magento\Backend\App\Action
{

    /**
     * @var Filter
     */
    protected $_massFilter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $massFilter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $massFilter, CollectionFactory $collectionFactory)
    {
        $this->_massFilter = $massFilter;
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
        $collection = $this->_massFilter->getCollection($this->collectionFactory->create());

        foreach ($collection as $item) {
            $item->setIsActive(false);
            $item->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been disabled.', $collection->getSize()));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
