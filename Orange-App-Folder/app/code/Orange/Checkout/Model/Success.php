<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Checkout\Model;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Success extends \Magento\Checkout\Model\Session
{
	/**
     * Get checkout quote instance by current session
     *
     * @return Quote
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
   	public function getOrder($incrementId)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('Magento\Sales\Model\Order')->load($incrementId, 'increment_id');
		$tempSession = $objectManager->get('Magento\Checkout\Model\Session')->getNewcheckout();
		$model = $objectManager->create('Orange\Abandonexport\Model\Items');
		$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$order->getQuoteId());
		$tempSession = unserialize($abandonexport->getFirstItem()->getStepsecond());
		$gender = $tempSession['gender'];
		$billingId = '';
        if($tempSession & $tempSession['c_dob'] != ""):
          $c_dob =  $tempSession['c_dob'];
		  $c_dob = str_replace('/', '-', $c_dob);
		  $dob = date("Y-m-d H:i:s", strtotime($c_dob));
        else:
		  $dob = "";
		endif; 	
		//$billingStreet  = $tempSession['b_number'] . " " .$tempSession['b_street'];
		$billingStreet[0]  = $tempSession['b_street'] . " " .$tempSession['b_number'];
		$billingStreet[1]  = $tempSession['b_box'];
		if($order->getBillingAddress())
		{
		$billingId   = $order->getBillingAddress()->getId();
		$billingAddress  = $objectManager->create('Magento\Sales\Model\Order\Address')->load($billingId);
			    $billingAddress
              ->setFirstname($tempSession['first_name'])
              ->setLastname($tempSession['last_name'])
			  ->setStreet($billingStreet)
			  ->setDob($dob)
			  ->setPrefix($gender)
              ->setCity($tempSession['b_city'])
			  ->setPostcode($tempSession['b_postcode_city'])
			  ->setBirthPlace($tempSession['cust_birthplace'])
			  ->save();
		}	  
	
	    if($order->getShippingAddress())
		{
		$shippingId  = $order->getShippingAddress()->getId();
		$shippingAddress = $objectManager->create('Magento\Sales\Model\Order\Address')->load($shippingId);
          if(isset($tempSession['s_number'])):
		  $sNumber = $tempSession['s_number'];
		  else:
		  $sNumber = $tempSession['b_number'];  
		  endif;
		   if(isset($tempSession['s_street'])):
		  $sStreet = $tempSession['s_street'];
		  else:
		  $sStreet = $tempSession['b_street'];
		  endif;
		  if(isset($tempSession['s_firstname']) && $tempSession['s_firstname'] != "" ):
		  $sFirstname = $tempSession['s_firstname'];
		  else:
		  $sFirstname = $tempSession['first_name'];
		  endif;
		    if(isset($tempSession['s_name'])):
		  $sName = $tempSession['s_name'];
		  else:
		  $sName = $tempSession['last_name'];
		  endif;
		   if(isset($tempSession['s_city'])):
		  $scity = $tempSession['s_city'];
		  else:
		  $scity = $tempSession['b_city'];
		  endif;
		   if(isset($tempSession['s_postcode_city'])):
		  $sPost = $tempSession['s_postcode_city'];
		  else:
		  $sPost = $tempSession['b_postcode_city'];
		  endif;
		  if(isset($tempSession['s_attention_n'])):
		  $sAttention = $tempSession['s_attention_n'];
		  else:
		  $sAttention = '';
		  endif;
	    	 if(isset($tempSession['s_number']) && isset($tempSession['s_street']) && isset($tempSession['s_box'])) {
				$sBox = $tempSession['s_box'];
			 }  else if(isset($tempSession['s_number']) && isset($tempSession['s_street'])) {
				$sBox = "";
			 } else {
				$sBox = $tempSession['b_box'];
			 }
		$shippingStreet[0] =  $sStreet . " " .$sNumber;
		$shippingStreet[1] =  $sBox;
	

	    $shippingAddress
              ->setFirstname($sFirstname)
              ->setLastname($sName)
			  ->setStreet($shippingStreet)
			  ->setDob($dob)
			  ->setPrefix($gender)
              ->setCity($scity)
              ->setAttentionOf($sAttention)
			  ->setPostcode($sPost)
			  ->setBirthPlace($tempSession['cust_birthplace'])
			  ->save();
		 }	  
	 return $billingId;		  
	}
}
