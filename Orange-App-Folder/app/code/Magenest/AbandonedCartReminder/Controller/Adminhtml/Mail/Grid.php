<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/10/2015
 * Time: 15:49
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Mail;

class Grid extends \Magenest\AbandonedCartReminder\Controller\Adminhtml\Mail
{


    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        return $resultLayout;

    }//end execute()

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::mail');

    }//end _isAllowed()
}//end class
