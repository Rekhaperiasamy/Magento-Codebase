<?php
namespace Orange\Export\Model\ResourceModel;

class Abandonedorderexport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Date model
     * 
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        $this->_date = $date;
        parent::__construct($context);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('orange_abandonorder_export', 'export_id');
    }
 
    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Mageplaza\HelloWorld\Model\Post $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
		//Can create log before save
        return parent::_beforeSave($object);
    }
}
