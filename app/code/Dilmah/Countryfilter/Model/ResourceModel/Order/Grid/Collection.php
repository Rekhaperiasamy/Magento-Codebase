<?php
namespace Dilmah\Countryfilter\Model\ResourceModel\Order\Grid;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
/**
* Order grid extended collection
*/
class Collection extends OriginalCollection
{
protected function _renderFiltersBefore()
{
$this->getSelect()->join('sales_order_address', 'main_table.entity_id = sales_order_address.parent_id',array('country_id','region'))->where("sales_order_address.address_type =  'shipping'");
parent::_renderFiltersBefore();
}
}
