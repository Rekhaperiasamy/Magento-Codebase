<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class RuleQuote extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_acart_rule_quote', 'rule_quote_id');
    }
}