<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Controller\Adminhtml\Csblock;

use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Aheadworks\Csblock\Controller\Adminhtml\Csblock
 */
class Save extends \Aheadworks\Csblock\Controller\Adminhtml\Csblock
{
    /**
     * @var \Aheadworks\Csblock\Model\CsblockFactory
     */
    protected $csblockModelFactory;

    protected $contentModelFactory;

    protected $contentCollectionFactory;

    /**
     * @param Action\Context $context
     * @param \Aheadworks\Csblock\Model\CsblockFactory $CsblockModelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aheadworks\Csblock\Model\CsblockFactory $csblockModelFactory,
        \Aheadworks\Csblock\Model\ContentFactory $contentModelFactory,
        \Aheadworks\Csblock\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
    ) {
        $this->csblockModelFactory = $csblockModelFactory;
        $this->contentModelFactory = $contentModelFactory;
        $this->contentCollectionFactory = $contentCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /* @var $ruleModel \Aheadworks\Csblock\Model\Csblock */
            $csblockModel = $this->csblockModelFactory->create();

            $id = $this->getRequest()->getParam('id');
            if ($this->getRequest()->getParam('back') == 'new') {
                unset($data['id']);
                $id = null;
            }

            if ($id) {
                $csblockModel->load($id);

                /* parse custom block content */
                if (!array_key_exists('content', $data)) {
                    $data['content'] = ['id' => []];
                }
                $this->_removeOldBlockContent($data['content'], $id);
            }

            /* check page and remove excess data*/
            if ($data['page_type'] != \Aheadworks\Csblock\Model\Source\PageType::PRODUCT_PAGE) {
                $data['rule'] = '';
            }
			if ($data['page_type'] != \Aheadworks\Csblock\Model\Source\PageType::INTERMEDIATE_PAGE && $data['page_type'] != \Aheadworks\Csblock\Model\Source\PageType::CATEGORY_PAGE) {
                $data['category_ids'] = '';
            }

            $csblockModel->setData($data);

            try {
                $rule = $data['rule'];
                $csblockModel->loadPost($rule, ['csblock']);

                $csblockModel->save();

                $this->messageManager->addSuccessMessage(__('Block was successfully saved'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/csblock/edit', ['id' => $csblockModel->getId()]);
                }
                return $resultRedirect->setPath('*/csblock/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the block.'));
            }
            $data['id'] = $id;
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/csblock/');
    }

    protected function _removeOldBlockContent($data, $csblockId)
    {
        $contentCollection = $this->contentCollectionFactory->create();
        $contentCollection->addBlockIdFilter($csblockId);
        $allContentIds = $contentCollection->getAllIds();
        $newContentIds = array_keys($data['id']);
        $removeIds = array_diff($allContentIds, $newContentIds);

        foreach ($removeIds as $id) {
            $contentModel = $this->contentModelFactory->create();
            $contentModel->load($id);
            $contentModel->delete();
        }

        return $this;
    }
}
