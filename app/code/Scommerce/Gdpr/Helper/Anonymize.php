<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Helper;

/**
 * Class Anonymize
 * @package Scommerce\Gdpr\Helper
 */
class Anonymize
{
	/* @var \Magento\Framework\App\ResourceConnection */
    private $resource;

	/**
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }
	
	/**
     * Anonymize sale data (order or quote)
     *
     * @param \Magento\Sales\Model\Order|\Magento\Quote\Model\Quote $model
     * @return \Magento\Sales\Model\Order|\Magento\Quote\Model\Quote
     */
    public function sale($model)
    {
        if (! (
            $model instanceof \Magento\Sales\Model\Order ||
            $model instanceof \Magento\Quote\Model\Quote
        )) {
            return $model;
        }
        $model->setCustomerPrefix($this->getString());
		$model->setCustomerFirstname($this->getString());
        $model->setCustomerMiddlename($this->getString());
        $model->setCustomerLastname($this->getString());
		$model->setCustomerSuffix($this->getString());
		$model->setCustomerTaxvat($this->getString());
        $model->setCustomerEmail($this->obfuscateEmail($model->getCustomerEmail()));
        $model->setRemoteIp($this->obfuscateIp($model->getRemoteIp()));
        $model->setCustomerDob(null);
        $model->setBillingAddress($this->address($model->getBillingAddress()));
        $model->setShippingAddress($this->address($model->getShippingAddress()));
        return $model;
    }

    /**
     * Anonymize address data (billing or shipping)
     *
     * @param \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Quote\Api\Data\AddressInterface $model
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Quote\Api\Data\AddressInterface
     */
    public function address($model)
    {
        if (! (
            $model instanceof \Magento\Sales\Api\Data\OrderAddressInterface ||
            $model instanceof \Magento\Quote\Api\Data\AddressInterface
        )) {
            return $model;
        }
        $model->setPrefix($this->getString());
		$model->setFirstname($this->getString());
        if (strlen($model->getMiddlename())>0){
			$model->setMiddlename($this->getString());
		}
        $model->setLastname($this->getString());
        $model->setSuffix($this->getString());
		$model->setCompany($this->getString());
		$model->setEmail($this->obfuscateEmail($model->getEmail()));
		$model->setRegionId(null);
        $model->setRegion($this->getString());
        $model->setStreet($this->getString());
        $model->setCity($this->getString());
        $model->setPostcode($this->getString());
        $model->setTelephone($this->getString());
        $model->setFax($this->getString());
		$model->setVatId($this->getString());
		
		if ($model instanceof \Magento\Sales\Api\Data\OrderAddressInterface){
			$order_id = $model->getParentId();
			$addressName = $model->getFirstname();
			if (strlen($model->getMiddlename())>0){
				$addressName .= ' '. $model->getMiddlename();
			}
			if (strlen($model->getLastname())>0){
				$addressName .= ' '. $model->getLastname();
			}
			
			$connection = $this->resource->getConnection();
				
			if ($model->getAddressType()=="billing"){	
				$connection->update(
							$this->resource->getTableName('sales_invoice_grid'),
							[
								'billing_name'=>$addressName
							],
							'order_id = '.$order_id
						);
						
				$connection->update(
							$this->resource->getTableName('sales_creditmemo_grid'),
							[
								'billing_name'=>$addressName
							],
							'order_id = '.$order_id
						);
			}
			
			if ($model->getAddressType()=="shipping"){
				$connection->update(
							$this->resource->getTableName('sales_shipment_grid'),
							[
								'shipping_name'=>$addressName
							],
							'order_id = '.$order_id
						);
			}
		}
        return $model;
    }

    /**
     * Generate random string
     *
     * @param int $len
     * @return string
     */
    public function getString($len = 10)
    {
        $data = [];
        $chars = array_merge(range(0, 9), range('a', 'z'));
        for ($i = 0; $i < $len; $i++) {
            $data[] = $chars[array_rand($chars)];
        }
        return implode('', $data);
    }

    /**
     * Generate random date
     *
     * @param string $start Start date
     * @param string $end End date
     * @param string $format Returning format
     * @return string
     */
    public function getDate($start = '1970-01-01', $end = null, $format = 'Y-m-d H:i:s')
    {
        $end = is_null($end) ? '2005-01-01' : $end;
        $timestamp = mt_rand(strtotime($start), strtotime($end));
        return date($format, $timestamp);
    }

    /**
     * Obfuscate email
     *
     * @param string $email
     * @return string
     */
    public function obfuscateEmail($email)
    {
        $length = 15;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $tld = ["com", "net", "biz"];
        $name = '';
        for($i = 0; $i < $length; $i++) {
            $name .= $characters[rand(0, strlen($characters) -1)];
        }
        $domen = $tld[array_rand($tld)];
        return sprintf('%s@example.%s', $name, $domen);
    }

    /**
     * Obfuscate ip
     *
     * @param string $ip
     * @return string
     */
    public function obfuscateIp($ip)
    {
        // IPv4
        $ip = preg_replace(
            '~(\d+)\.(\d+)\.(\d+)\.(\d+)~',
            "$1.$2.x.x",
            $ip
        );

        // IPv6
        return preg_replace(
            '~(\w+)\:(\w+)\:(\w+)\:(\w+)\:(\w+)\:(\w+)\:(\w+)\:(\w+)~',
            "$1:$2:x:x:x:x:x:x",
            $ip
        );
    }
}
