<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Review
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Review\Block\Product\View;

/**
 * Class ListView
 */
class ListView extends \Magento\Review\Block\Product\View
{

    /**
     * Prepare product review list toolbar
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $toolbar = $this->getLayout()->getBlock('product_review_list.toolbar');
        if ($toolbar) {
            $toolbar->setAvailableLimit([3=>3])
                ->setLimit(3)
                ->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }

    /**
     * Add rate votes
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->getReviewsCollection()->load()->addRateVotes();
        return parent::_beforeToHtml();
    }

    /**
     * Return review url
     *
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return $this->getUrl('*/*/view', ['id' => $id]);
    }
}
