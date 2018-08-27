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

namespace Customweb\OgoneCw\Plugin\Framework\App;

class Http
{
	/**
	 * @var \Magento\Framework\Filesystem\DirectoryList
	 */
	private $_directoryList;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	private $_urlBuilder;

	/**
	 * @var \Customweb\OgoneCw\Model\Adapter\StorageBackend
	 */
	private $_storage;

	/**
	 * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
	 * @param \Magento\Framework\UrlInterface $urlBuilder
	 * @param \Customweb\OgoneCw\Model\Adapter\StorageBackend $storage
	 */
	public function __construct(
			\Magento\Framework\Filesystem\DirectoryList $directoryList,
			\Magento\Framework\UrlInterface $urlBuilder,
			\Customweb\OgoneCw\Model\Adapter\StorageBackend $storage
	) {
		$this->_directoryList = $directoryList;
		$this->_urlBuilder = $urlBuilder;
		$this->_storage = $storage;
	}

	/**
	 * @param \Magento\Framework\App\Http $subject
	 */
	public function beforeLaunch(\Magento\Framework\App\Http $subject)
	{
		$this->registerAutoload();
		$this->registerTranslationResolver();
		$this->setupLicensingAdapter();
	}

	private function registerAutoload()
	{
		$internalLibraryDirectory = $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::LIB_INTERNAL);

		$directories = [
			'Customweb',
			'File',
			'Mobile',
			'Crypt',
			'Math',
			'Net',
			'System',
		];
		foreach ($directories as $directory) {
			\Magento\Framework\Autoload\AutoloaderRegistry::getAutoloader()->addPsr0($directory . '_', $internalLibraryDirectory, true);
		}
	}

	private function registerTranslationResolver()
	{
		\Customweb_I18n_Translation::getInstance()->addResolver(new \Customweb\OgoneCw\Model\TranslationResolver());
	}

	private function setupLicensingAdapter()
	{
		
		$arguments = null;
		return \Customweb_Licensing_OgoneCw_License::run('tles84dmqh58ds1s', $this, $arguments);
	}

	final public function call_fflq9bqujut74uld() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}

	private function getStorageBackend()
	{
		return $this->_storage;
	}

	private function getUrlBuilder()
	{
		return $this->_urlBuilder;
	}
}