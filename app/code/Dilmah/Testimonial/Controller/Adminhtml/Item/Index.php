<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Testimonial
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Testimonial\Controller\Adminhtml\Item;

use Dilmah\Testimonial\Controller\Adminhtml\Item;

/**
 * Class Index.
 */
class Index extends Item
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->initResultPage();
        $resultPage->getConfig()->getTitle()->prepend(__('Testimonials'));

        return $resultPage;
    }
}
