<?php
namespace Orange\Coupon\Model;

class CouponImport
{
    protected $csvProcessor;
	protected $_logger;
	protected $_customGroupCollectionFactory;
	protected $_rule;
	protected $_coupon;
	protected $_rulecollectionFactory;
	protected $_amastyRuleFactory;
	
    /**     
     * @param Csv $csvReader
	 * @param logger $logger
     */
    public function __construct(
		\Magento\Framework\File\Csv $csvProcessor,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customGroupCollectionFactory,
		\Magento\SalesRule\Model\RuleFactory $rule,
		\Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
		\Magento\SalesRule\Model\CouponFactory $coupon,
		\Amasty\Rules\Model\RuleFactory $amastyRuleFactory
	) {
        $this->csvProcessor = $csvProcessor;	
		$this->_logger = $logger;
		$this->_customGroupCollectionFactory = $customGroupCollectionFactory;
		$this->_rule = $rule;
		$this->_coupon = $coupon;
		$this->_rulecollectionFactory = $ruleCollectionFactory;
		$this->_amastyRuleFactory = $amastyRuleFactory;
    }
	
	/**
     * Import Coupons from CSV file
     *
     * @param array $file file info retrieved from $_FILES array
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
		set_time_limit(0);//Added to avoid time out error
		ini_set('memory_limit', '2G');//Set memory limit to avoid memory exhaust
        $couponRawData = $this->csvProcessor->getData($file['tmp_name']);
        // first row of file represents headers
        $fileFields = $couponRawData[0];		
        $validFields = $this->_filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $couponsData = $this->_filterCouponData($couponRawData, $invalidFields, $validFields);
		/** REMOVE AFTER IMPLEMENTATION **/
		//$this->_deleteExistingRules();
		/** EOF REMOVE RULE **/
        foreach ($couponsData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            $regionsCache = $this->_importCoupon($dataRow);
        }
		ini_restore('max_execution_time');//restore the default time out
    }
	
	/**
     * Retrieve a list of fields required for CSV file (order is important!)
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('Coupon ID'),
            1 => __('Name'),
            2 => __('Code'),
            3 => __('Discount'),
            4 => __('Type'),
            5 => __('Free Product Discount'),
            6 => __('Max Uses'),
            7 => __('Valid From'),
			8 => __('Valid Until'),
            9 => __('Minimum Order Total'),
            10 => __('Price Type'),
            11 => __('Available Codes'),
            12 => __('Apply discount to'),            
            13 => __('Minimum Order Quantity'),
            14 => __('Applicable to Product Types'),
			15 => __('Applicable to Skus'),
			16 => __('Apply coupon to product'),
            17 => __('Applicable to Products')
        ];
    }
	
	/**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    protected function _filterFileFields(array $fileFields)
    {
        $filteredFields = $this->getRequiredCsvFields();
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $fileFieldsNum = count($fileFields);

        // process title-related fields that are located right after required fields with store code as field name)
        for ($index = $requiredFieldsNum; $index < $fileFieldsNum; $index++) {
            $titleFieldName = $fileFields[$index];
            $filteredFields[$index] = $titleFieldName;
        }

        return $filteredFields;
    }
		
	
	/**
     * Filter coupons data (i.e. unset all invalid fields and check consistency)
     *
     * @param array $couponRawData
     * @param array $invalidFields assoc array of invalid file fields
     * @param array $validFields assoc array of valid file fields
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _filterCouponData(array $couponRawData, array $invalidFields, array $validFields)
    {
        $validFieldsNum = count($validFields);
        foreach ($couponRawData as $rowIndex => $dataRow) {
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($couponRawData[$rowIndex]);
                continue;
            }
            // unset invalid fields from data row
            foreach ($dataRow as $fieldIndex => $fieldValue) {
                if (isset($invalidFields[$fieldIndex])) {
                    unset($couponRawData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields			
            if (count($couponRawData[$rowIndex]) != $validFieldsNum) {				
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file format.'));
            }
        }
        return $couponRawData;
    }
	
	/**
     * Retrieve a list of customer groups
     *
     * @return array
     */
	protected function _customerGroups()
	{
		$customerGroup = $this->_customGroupCollectionFactory->create();
		return $customerGroup->getAllIds();		
	}
	
	/**
     * Import Rule with coupons and conditions
     *
     * @param array $couponData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _importCoupon(array $couponData)
    {   
		$customerGroupIds = $this->_customerGroups();//Customer Groups
		$couponId = $couponData[0];//Coupon Id
		$couponName = $couponData[1];//Coupon Name
		$couponDescription = $couponData[2];//Coupon Description
		$couponDiscount = $couponData[3];//Discount Amount
		$couponDiscountType = $couponData[4];//Discount Type
		$couponMaxUse = $couponData[6];//Coupon max usage
		$couponStartDate = date('Y-m-d',strtotime($couponData[7]));//Coupon start date
		$couponEndDate = date('Y-m-d',strtotime($couponData[8]));//Coupon End date
		$couponMinOrderTotal = $couponData[9];//Min order total for coupon
		$couponApplyType = $couponData[10];//Check applicable for device or subscriptions
		$couponCodes = $couponData[11];//Coupon Codes
		$couponDiscountTo = $couponData[12];//Apply discount To
		$couponMinOrderQty = $couponData[13];//Min order Qty
		$couponProductType = $couponData[14];//Applicable to product types
		$couponSkus = $couponData[15];//Applicable product sku
		$isCouponApplicableToProducts = $couponData[16];// Is Given Products Applicable
		$couponProducts = $couponData[17];//Products
		//$isCouponApplicableToTerms = $couponData[18];//Is Given Terms Applicable
		//$couponTerms = $couponData[19];//Terms
		
		if($couponDiscountType == 'percentage'):
			$discountType = \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION;
		else:
			$discountType = \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION;
		endif;
		/** Percentage discount only applicable for Cheapeset and Expensive **/
		if($couponDiscountTo == 'cheapest'):
			$discountType = \Amasty\Rules\Helper\Data::TYPE_CHEAPEST;
		elseif($couponDiscountTo == 'expensive'):
			$discountType = \Amasty\Rules\Helper\Data::TYPE_EXPENCIVE;
		endif;
		$fixedDiscount = 0;
		if(($couponDiscountTo == 'cheapest' || $couponDiscountTo == 'expensive') && $couponDiscountType != 'percentage'):
			$fixedDiscount = $couponDiscount;
			$couponDiscount = 100;
		endif;
		$discountQty = 0;
		
		$this->_logger->addDebug('--------------------------------------------------');
		$this->_logger->addDebug('Coupon Id:'.$couponId);
		$this->_logger->addDebug('Coupon Name:'.$couponName);
		$this->_logger->addDebug('Coupon Description:'.$couponDescription);
		$this->_logger->addDebug('Coupon Discount:'.$couponDiscount);
		$this->_logger->addDebug('Coupon Type:'.$couponDiscountType);
		$this->_logger->addDebug('Max Uses:'.$couponMaxUse);
		$this->_logger->addDebug('Start Date:'.$couponStartDate);
		$this->_logger->addDebug('End Date:'.$couponEndDate);
		$this->_logger->addDebug('Min Order Total:'.$couponMinOrderTotal);
		$this->_logger->addDebug('CouponApplyType:'.$couponApplyType);
		$this->_logger->addDebug('CouponCodes:'.$couponCodes);
		$this->_logger->addDebug('CouponDiscounTo:'.$couponDiscountTo);
		$this->_logger->addDebug('Min Order Qty:'.$couponMinOrderQty);
		$this->_logger->addDebug('Product Types:'.$couponProductType);
		$this->_logger->addDebug('SKUs:'.$couponSkus);
		$this->_logger->addDebug('CouponProducts:'.$couponProducts);
		//$this->_logger->addDebug('Terms:'.$couponTerms);
		$this->_logger->addDebug('--------------------------------------------------');
		
		 
		$conditions = $this->_getRuleConditions($couponData);//Format Rule Conditions
		$actions = $this->_getRuleActions($couponData);//Format Rule Actions
		
		$rule = $this->_rule->create();
		$rule->setName($couponName)
			->setDescription($couponDescription)
			->setFromDate($couponStartDate)//Core Issue Persists
			->setToDate($couponEndDate)//Core Issue Persists
			->setCouponType(\Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC)
			//->setCouponCode('my-coupon-code')
			->setUseAutoGeneration(true)
			->setUsesPerCustomer(1)
			->setUsesPerCoupon($couponMaxUse)
			->setCustomerGroupIds($customerGroupIds)
			->setIsActive(1)
			->setConditionsSerialized('')
			->setActionsSerialized('')
			->setStopRulesProcessing(0)
			->setIsAdvanced(1)
			->setProductIds('')
			->setSortOrder(0)
			->setSimpleAction($discountType)
			->setDiscountAmount($couponDiscount)
			->setDiscountQty($discountQty)
			->setDiscountStep(0)
			->setSimpleFreeShipping('0')
			->setApplyToShipping('0')
			->setIsRss(0)
			->setWebsiteIds(array(1))
			->setStoreLabels(array($couponName));		
		$rule->setData("conditions",$conditions);
		$rule->setData("actions",$actions);
		$rule->loadPost($rule->getData());
		$rule->save();

		if($rule->getId()) {
			$ruleId = $rule->getId();
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$sql = "UPDATE `salesrule` SET `to_date` = '$couponEndDate', `from_date` = '$couponStartDate' WHERE `salesrule`.`rule_id` = '$ruleId'";
			$connection->query($sql);
		}
		
		/** Set Max discount amount for cheapest and expensive rule with fixed discount **/
		if ($rule->getId() && $couponDiscountType != 'percentage') {
			//Only for Cheapest and Expensive with fixed discount
            $amrulesRule = $this->_amastyRuleFactory->create();

            $amrulesRule
                ->load($rule->getId(), 'salesrule_id')
                ->setData('max_discount',$fixedDiscount)
                ->setData('salesrule_id', $rule->getId())
                ->save()
            ;
        }		
		$this->_createCoupons($rule->getRuleId(),$couponData);//Create and assign coupons to the created rule
        return $couponData;
    }
	
	/**
     * Delete all existing rules
     *
     */
	protected function _deleteExistingRules()
	{
		$rulecollection = $this->_rulecollectionFactory->create();
		foreach($rulecollection as $rule):
			$rule->delete();
		endforeach;		
	}
	
	/**
     * Create Coupons and assign to rule
     *
	 * @param INT $ruleId
     * @param array $couponData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	protected function _createCoupons($ruleId,array $couponData)
	{
		$rawCoupons = explode(",",$couponData[11]);
		$coupons = array_filter($rawCoupons);
		$couponExpiry = date('Y-m-d',strtotime($couponData[8]));		
		foreach($coupons as $couponCode):
			$couponCode = trim($couponCode);
			if($couponCode!=''):
				$coupon = $this->_coupon->create();
				$coupon->setId(null);
				$coupon->setRuleId($ruleId);
				$coupon->setUsageLimit(intval($couponData[6]));
				//$coupon->setUsagePerCustomer(1);
				//$coupon->setTimesUsed($timesUsed);
				$coupon->setExpirationDate($couponExpiry);
				$coupon->setCreatedAt(time());
				$coupon->setType(\Magento\SalesRule\Helper\Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED);
				$coupon->setCode($couponCode);
				$coupon->save();
			endif;
		endforeach;		
	}
	
	/**
     * Create Rule Conditions
     *
     * @param array $couponData
	 * @return array $conditions
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	protected function _getRuleConditions(array $couponData)
	{
		$conditions = array();
		$conditions = array(
			"1"         => array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Combine",
					"aggregator"    => "all",
					"value"         => "1",
					"new_child"     => null
				),
			"1--1"      => array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Product\Found",
					"aggregator"    => "any",
					"value"         => "1",
					"new_child"     => null
				),				
		);
		if($couponData[10] == 'monthly_fee'):			
			$monthlyConditions = array(
				"1--1--1"   => array(
						"type"          => "Magento\SalesRule\Model\Rule\Condition\Product",
						"attribute"     => "attribute_set_id",
						"operator"      => "==",
						"value"         => $this->_getAttributeSetId('prepaid')
					),
				"1--1--2"   => array(
						"type"          => "Magento\SalesRule\Model\Rule\Condition\Product",
						"attribute"     => "attribute_set_id",
						"operator"      => "==",
						"value"         => $this->_getAttributeSetId('postpaid')
					),
				"1--1--3"   => array(
						"type"          => "Magento\SalesRule\Model\Rule\Condition\Product",
						"attribute"     => "attribute_set_id",
						"operator"      => "==",
						"value"         => $this->_getAttributeSetId('iew')
					)
			);
			$conditions = $conditions + $monthlyConditions;
		endif;		
		if($couponData[10] == 'fixed_price'):			
			$applicableTypes = explode(",",$couponData[14]);			
			if($couponData[14]!=''):
				$attributeSetconds = $this->getAttributeSetConditions($couponData[14]);//Format condition based on product type				
				$conditions = $conditions + $attributeSetconds;
			endif;
		endif;
		
		$minOrderTotal = floatval($couponData[9]);		
		if($minOrderTotal > 0):
			//Format condition for min order total
			$totalConditions = array(
				"1--4"  => array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Address",
					"attribute"    => "base_subtotal",
					"operator"         => ">",
					"value"     => $minOrderTotal
				)
			);
			$conditions = $conditions + $totalConditions;
		endif;
		$minOrderQty = floatval($couponData[13]);
		if($minOrderQty > 0):
			//Format condition for min order qty
			$qtyConditions = array(
				"1--4"  => array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Address",
					"attribute"    => "total_qty",
					"operator"         => ">",
					"value"     => $minOrderQty
				)
			);
			$conditions = $conditions + $qtyConditions;
		endif;
		
		return $conditions;
	}
	
	/**
     * Format condition for attributeset
     *
     * @param array $conditions	      
     */
	protected function getAttributeSetConditions($conditions) 
	{
		$categories = explode(",",$conditions);
		$attributeSetConds = array();
		$i=1;
		foreach($categories as $category):
			$attributeSetId = $this->_getAttributeSetId(trim($category));//Get Attribute Set Id
			$attributeSetConds["1--1--".$i] = array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Product",
					"attribute"     => "attribute_set_id",
					"operator"      => "==",
					"value"         => $attributeSetId
				);
			$i++;
		endforeach;
		return $attributeSetConds;
	}
	
	/**
     * Create Rule Actions
     *
     * @param array $couponData
	 * @return array $actions
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	protected function _getRuleActions(array $couponData)
	{
		$actions = array();
		if($couponData[10] == 'monthly_fee'):		
			$initActions = array(
				"1"         => array(
						"type"          => "Magento\SalesRule\Model\Rule\Condition\Product\Combine",
						"aggregator"    => "any",
						"value"         => "1",
						"new_child"     => false
					)
			);
			if($couponData[15]!=''):
				$skuActions = $this->_getSkuActions($couponData[15]);
				$actions = $initActions + $skuActions;
			endif;
		endif;
		$actions = array(
			"1"         => array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Product\Combine",
					"aggregator"    => "all",
					"value"         => "1",
					"new_child"     => true
				),
			"1--1"      => array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Product\Combine",
					"aggregator"    => "any",
					"value"         => "1",
					"new_child"     => null
				),				
		);
		if($couponData[10] == 'fixed_price'):
			$applicableTypes = explode(",",$couponData[14]);
			if($couponData[14]!=''):
				$attributeSetActions = $this->getAttributeSetActions($couponData[14]);
				$actions = $actions + $attributeSetActions;
			endif;
		endif;
		if($couponData[15] != ''):			
			$applicableSkuActions = $this->getApplicableSkuActions($couponData[15]);
			$actions = $actions + $applicableSkuActions;
		endif;
		if($couponData[17] != ''):
			//Applicable products			
			$applicableProductsAction = $this->getApplicableProductsAction($couponData);
			$actions = $actions + $applicableProductsAction;
		endif;
		//if($couponData[19] != ''):
			//Applicable terms
			//$isApplicable = $couponData[18];
		//endif;
		return $actions;
	}
	
	/**
     * Format condition for applicablesku
     *
     * @param array $conditions	      
     */
	protected function getApplicableSkuActions($conditions) 
	{
		$applicableSkus = explode(",",$conditions);
		$applicableSkusConds = array();
		$i=4;//increase this value if condition is getting replaced
		foreach($applicableSkus as $sku):			
			$applicableSkusConds["1--1--".$i] = array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Product",
					"attribute"     => "sku",
					"operator"      => "==",
					"value"         => trim($sku)
				);
			$i++;
		endforeach;
		return $applicableSkusConds;
	}
	
	/**
     * Format condition for attributeset
     *
     * @param array $conditions	      
     */
	protected function getAttributeSetActions($conditions) 
	{
		$categories = explode(",",$conditions);
		$attributeSetConds = array();
		$i=1;//increase this value if condition is getting replaced
		foreach($categories as $category):
			$attributeSetId = $this->_getAttributeSetId(trim($category));
			$attributeSetConds["1--1--".$i] = array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Product",
					"attribute"     => "attribute_set_id",
					"operator"      => "==",
					"value"         => $attributeSetId
				);
			$i++;
		endforeach;
		return $attributeSetConds;
	}
	
	/**
     * Format condition for applicableproducts
     *
     * @param array $conditions	      
     */
	protected function getApplicableProductsAction($couponData) 
	{
		$isApplicable = ($couponData[16] == 'Yes' || $couponData[16] == '') ? 1:0;
		$actions = array(
			"1--2"      => array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Product\Combine",
					"aggregator"    => "any",
					"value"         => $isApplicable,
					"new_child"     => null
				),				
		);
		$applicableProducts = explode(",",$couponData[17]);
		$applicableProductsConds = array();
		$i=1;
		foreach($applicableProducts as $productName):				
			$applicableProductsConds["1--2--".$i] = array(
					"type"          => "Magento\SalesRule\Model\Rule\Condition\Product",
					"attribute"     => "name",
					"operator"      => "==",
					"value"         => trim($productName)
				);
			$i++;
		endforeach;
		$actions = $actions + $applicableProductsConds;
		return $actions;
	}
	
	/**
     * Format condition for subscription product applicable SKUs
     *
     * @param array $conditions	      
     */
	protected function _getSkuActions($skus)
	{
		$skus = explode(",",$skus);
		$i=1;//increase this value if condition is getting replaced
		foreach($skus as $sku):
			$sku = trim($sku);
			$skuConds["1--".$i] = array(
						"type"          => "Magento\SalesRule\Model\Rule\Condition\Product",
						"attribute"     => "sku",
						"operator"      => "==",
						"value"         => $sku
					);
			$i++;
		endforeach;
		return $skuConds;
	}
	
	/**
     * Format condition for attributeset
     *
     * @param array $csvData
	 * @return INT $attributeSetId
     */
	protected function _getAttributeSetId($csvData)
	{
		/** Attribute Set Ids Hardcoded need to update manually if changed **/
		$attributeSets['mobile_phone'] = '12';
		$attributeSets['accessory'] = '13';
		$attributeSets['prepaid'] = '10';
		$attributeSets['postpaid'] = '11';
		$attributeSets['iew'] = '15';
		$attributeSetId = $attributeSets[$csvData];
		return $attributeSetId;
	}
}