<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Rule extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_acart_rule', 'rule_id');
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $db = $this->getConnection();
        $tbl   = $this->getTable('amasty_acart_attribute');

        $select = $db->select()->from($tbl, new \Zend_Db_Expr('DISTINCT code'));
        return $db->fetchCol($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of the rule
     * @param int $id
     * @param array $attributes
     * @return $this
     */
    public function saveAttributes($id, $attributes)
    {
        $db = $this->getConnection();
        $tbl = $this->getTable('amasty_acart_attribute');

        $db->delete($tbl, array('rule_id=?' => $id));

        $data = array();
        foreach ($attributes as $code){
            $data[] = array(
                'rule_id' => $id,
                'code'    => $code,
            );
        }
        $db->insertMultiple($tbl, $data);

        return $this;
    }
}
