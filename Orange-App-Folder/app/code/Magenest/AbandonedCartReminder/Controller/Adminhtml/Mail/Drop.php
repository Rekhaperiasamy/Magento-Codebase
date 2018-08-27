<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/01/2016
 * Time: 11:25
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Mail;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class Drop extends Action
{


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $this->_view->loadLayout('abandonedcartreminder_mail_preview_popup');
        $this->_view->renderLayout();

    }//end execute()

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::mail');

    }//end _isAllowed()
}//end class
