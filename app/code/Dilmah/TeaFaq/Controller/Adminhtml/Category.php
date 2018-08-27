<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Category
 *
 * @package Dilmah\TeaFaq\Controller\Adminhtml
 */
abstract class Category extends Action
{
    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dilmah_TeaFaq::dilmah_tfaq');
    }

    /**
     * Initialize action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initResultPage()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Dilmah_TeaFaq::dilmah_tfaq')
            ->addBreadcrumb(__('Tea FAQ'), __('Tea FAQ'))
            ->addBreadcrumb(__('Category'), __('Category'));
        return $resultPage;
    }
}
