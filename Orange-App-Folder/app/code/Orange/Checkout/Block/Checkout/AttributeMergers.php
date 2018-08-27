<?php
namespace Orange\Checkout\Block\Checkout;
use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Helper\Address as AddressHelper;

class AttributeMergers extends AttributeMerger
{
    protected function getFieldOptions($attributeCode, array $attributeConfig)
    {
        $options = isset($attributeConfig['options']) ? $attributeConfig['options'] : [];
		if($attributeCode == 'region_id') {
			return $regionoptions = array();
		}
        return ($attributeCode == 'country_id') ? $this->orderCountryOptions($options) : $options;
    }
}
