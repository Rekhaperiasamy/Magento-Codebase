<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Controller\Adminhtml\Item;

use Dilmah\TeaFaq\Controller\Adminhtml\Item;

/**
 * Class Index
 *
 * @package Dilmah\TeaFaq\Controller\Adminhtml\Category
 */
class Index extends Item
{
    /**
     * Grid Items Function Controller
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->initResultPage();
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Item'));
        return $resultPage;
    }
}
