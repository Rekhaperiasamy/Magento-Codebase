<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/10/2015
 * Time: 15:52
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

class Grid extends \Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule
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
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::rule');

    }//end _isAllowed()
}//end class
