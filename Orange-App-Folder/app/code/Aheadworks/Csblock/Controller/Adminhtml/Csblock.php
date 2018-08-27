<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Controller\Adminhtml;

/**
 * Class Csblock
 * @package Aheadworks\Csblock\Controller\Adminhtml
 */
abstract class Csblock extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Init action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_Csblock::main');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Blocks'));
        $resultPage->getLayout()->getBlock('aw_csblock.menu')->setCurrentItemKey(
            \Aheadworks\Csblock\Block\Adminhtml\Menu::ITEM_BLOCK
        );

        return $resultPage;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aheadworks_Csblock::csblock');
    }
}
