<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Afeature
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Afeature\Controller\Adminhtml;

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
        return $this->_authorization->isAllowed('Dilmah_Afeature::dilmah_afeature');
    }

    /**
     * Initialize action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initResultPage()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Dilmah_Afeature::dilmah_afeature')
            ->addBreadcrumb(__('Afeature'), __('Afeature'))
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
