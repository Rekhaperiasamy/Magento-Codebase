<?php
/**
 * Copyright © 2015 Bala. All rights reserved.
 */

namespace Orange\Scoringfield\Controller\Adminhtml\Scoringresponse;

class NewAction extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
