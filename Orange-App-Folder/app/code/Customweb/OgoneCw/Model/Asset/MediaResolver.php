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

namespace Customweb\OgoneCw\Model\Asset;

class MediaResolver implements \Customweb_Asset_IResolver
{
	const TARGET_DIRECTORY = 'customweb/ogonecw/assets';

	const SOURCE_DIRECTORY = 'Customweb/OgoneCw/media/assets';

	/**
	 * @var \Magento\Framework\Filesystem
	 */
	protected $_filesystem;

	/**
	 * @var \Customweb\OgoneCw\Model\Configuration
	 */
	private $_configuration;

	/**
	 * @param \Magento\Framework\Filesystem $filesystem
	 */
	public function __construct(
			\Magento\Framework\Filesystem $filesystem,
			\Customweb\OgoneCw\Model\Configuration $configuration
	) {
		$this->_filesystem = $filesystem;
		$this->_configuration = $configuration;
	}

	public function resolveAssetStream($identifier)
	{
		if (!$this->publish($identifier)) {
			throw new \Customweb_Asset_Exception_UnresolvableAssetException($identifier);
		}

		$mediaDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		$filePath = $mediaDirectory->getAbsolutePath(self::TARGET_DIRECTORY . '/' . $identifier);
		return new \Customweb_Core_Stream_Input_File($filePath);
	}

	public function resolveAssetUrl($identifier)
	{
		if (!$this->publish($identifier)) {
			throw new \Customweb_Asset_Exception_UnresolvableAssetException($identifier);
		}

		return $this->_configuration->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::TARGET_DIRECTORY . '/' . $identifier;
	}

	/**
	 * @param string $identifier
	 * @return boolean
	 */
	private function publish($identifier) {
		$modulesDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MODULES);
		if (!$modulesDirectory->isExist(self::SOURCE_DIRECTORY . '/' . $identifier)) {
			return false;
		}

		$mediaDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		if ($mediaDirectory->isExist(self::TARGET_DIRECTORY . '/' . $identifier)) {
			return true;
		}

		$sourceDirectory = $this->_filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MODULES);
		$targetDirectory = $this->_filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		$sourcePath = $modulesDirectory->getRelativePath(self::SOURCE_DIRECTORY . '/' . $identifier);
		$destinationPath = self::TARGET_DIRECTORY . '/' . $identifier;
		try {
			return $sourceDirectory->createSymlink($sourcePath, $destinationPath, $targetDirectory);
		} catch (\Magento\Framework\Exception\FileSystemException $e) {
			return false;
		}
	}
}