<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\ResourceModel;

/**
 * Class Content
 * @package Aheadworks\Csblock\Model\ResourceModel
 */
class Content extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_csblock_content', 'id');
    }
}