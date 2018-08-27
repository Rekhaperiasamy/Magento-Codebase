<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_OgoneCw
 * 
 */

namespace Customweb\OgoneCw\Model\ResourceModel;

abstract class AbstractVersionedModel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Name of the version number field
	 *
	 * @var string
	 */
	protected $_versionNumberFieldName = 'version_number';

	/**
	 * Get name of the version number field
	 *
	 * @return string
	 */
	public function getVersionNumberFieldName()
	{
		if (empty($this->_versionNumberFieldName)) {
			throw new \Exception(__('Empty version number field name'));
		}
		return $this->_versionNumberFieldName;
	}

	protected function updateObject(\Magento\Framework\Model\AbstractModel $object)
	{
		$condition = $this->getConnection()->quoteInto($this->getIdFieldName() . '=?', $object->getId());

		$nextVersion = null;
		$currentVersion = $object->getData($this->getVersionNumberFieldName());
		if($currentVersion !== null){
			$nextVersion = $currentVersion + 1;
			$condition .= $this->getConnection()->quoteInto(' AND '.$this->getVersionNumberFieldName().'=?', $currentVersion);
		} else {
			$nextVersion = 1;
		}

		/**
		 * Not auto increment primary key support
		*/
		if ($this->_isPkAutoIncrement) {
			$data = $this->prepareDataForUpdate($object);
			$data[$this->getVersionNumberFieldName()] = $nextVersion;
			if (!empty($data)) {
				$rowAffected = $this->getConnection()->update($this->getMainTable(), $data, $condition);
				if($rowAffected == 0) {
					throw new \Customweb\OgoneCw\Model\Exception\OptimisticLockingException(get_class($object), $object->getId());
				}
				$object->setVersionNumber($nextVersion);
			}
		} else {
			$select = $this->getConnection()->select()->from(
					$this->getMainTable(),
					[$this->getIdFieldName()]
			)->where(
					$condition
			);
			if ($this->getConnection()->fetchOne($select) !== false) {
				$data = $this->prepareDataForUpdate($object);
				$data[$this->getVersionNumberFieldName()] = $nextVersion;
				if (!empty($data)) {
					$rowAffected = $this->getConnection()->update($this->getMainTable(), $data, $condition);
					if($rowAffected == 0) {
						throw new \Customweb\OgoneCw\Model\Exception\OptimisticLockingException(get_class($object), $object->getId());
					}
					$object->setData($this->getVersionNumberFieldName(), $nextVersion);
				}
			} else {
				$object->setData($this->getVersionNumberFieldName(), 1);
				$this->getConnection()->insert(
						$this->getMainTable(),
						$this->_prepareDataForSave($object)
				);
			}
		}
	}

	protected function _serializeField(\Magento\Framework\DataObject $object, $field, $defaultValue = null, $unsetEmpty = false)
	{
		parent::_serializeField($object, $field, $defaultValue, $unsetEmpty);
		$object->setData($field, base64_encode($object->getData($field)));
		return $this;
	}

	protected function _unserializeField(\Magento\Framework\DataObject $object, $field, $defaultValue = null)
	{
		$object->setData($field, base64_decode($object->getData($field)));
		parent::_unserializeField($object, $field, $defaultValue);
	}
}