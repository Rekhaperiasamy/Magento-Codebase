<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Testimonial
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Testimonial\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Item.
 */
class Item extends Action
{
    /**
     * Check current user permission on resource and privilege.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dilmah_Testimonial::dilmah_testimonial');
    }

    /**
     * Initialize action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initResultPage()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Netstarter_Testimonial::dilmah_testimonial')
            ->addBreadcrumb(__('Testimonial'), __('Testimonial'))
            ->addBreadcrumb(__('Item'), __('Item'));

        return $resultPage;
    }

    /**
     * execute function.
     *
     * @return void
     */
    public function execute()
    {
    }
}
