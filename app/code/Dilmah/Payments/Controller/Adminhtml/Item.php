<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Controller\Adminhtml;

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
        return $this->_authorization->isAllowed('Dilmah_Payments::dilmah_payments');
    }

    /**
     * Initialize action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initResultPage()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Dilmah_Payments::dilmah_payments')
            ->addBreadcrumb(__('Payments'), __('Payments'))
            ->addBreadcrumb(__('History'), __('History'));

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
