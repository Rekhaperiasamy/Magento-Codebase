<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model;

/**
 * Class Content
 * @package Aheadworks\Csblock\Model
 */
class Content extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Aheadworks\Csblock\Model\ResourceModel\Content');
    }
}