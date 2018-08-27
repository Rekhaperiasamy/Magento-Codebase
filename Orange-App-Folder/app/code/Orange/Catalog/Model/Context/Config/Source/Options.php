<?php

namespace Orange\Catalog\Model\Context\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;

use Magento\Framework\DB\Ddl\Table;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Customer\Api\Data\GroupInterface;

/**

* Custom Attribute Renderer

*/

class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource

{

	/**
     * @param LocatorInterface            $locator
     * @param UrlInterface                $urlBuilder
     * @param ArrayManager                $arrayManager
	 * @param CustomerGroup               $customerGroup
     */
    public function __construct(
		CustomerGroup $customerGroup
    ) {
		$this->customerGroup = $customerGroup;
    }

	/**

	* @var OptionFactory

	*/

	protected $optionFactory;

	/**

	* @param OptionFactory $optionFactory

	*/

	/**

	* Get all options

	*

	* @return array

	*/

	public function getAllOptions()

	{
		$groupOptions = $this->customerGroup->toOptionArray();
		array_unshift($groupOptions , array('value' => GroupInterface::CUST_GROUP_ALL,'label' => 'ALL CONTEXT'));	
        array_unshift($groupOptions , array('value' => '','label' => 'Select Context'));	
        return $groupOptions;
	}

}