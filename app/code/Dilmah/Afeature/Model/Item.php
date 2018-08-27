<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Afeature
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Afeature\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Item.
 */
class Item extends AbstractModel
{
    /**
     * name of the base afeature upload directory.
     */
    const BASE_UPLOAD_DIRECTORY = 'afeature';

    /**
     * sub directory name of desktop banners.
     */
    const DESKTOP_BANNER_DIRECTORY = 'desktop';

    /**
     * sub directory name of desktop small banners.
     */
    const DESKTOP_SMALL_BANNER_DIRECTORY = 'desktop_small';

    /**
     * sub directory name of tablet banners.
     */
    const TABLET_BANNER_DIRECTORY = 'tablet';

    /**
     * sub directory name of mobile banners.
     */
    const MOBILE_BANNER_DIRECTORY = 'mobile';

    /**
     * resize height of the desktop image
     */
    const DESKTOP_IMAGE_RESIZE_WIDTH = '1980';

    /**
     * resize width of the desktop image
     */
    const DESKTOP_IMAGE_RESIZE_HEIGHT = '666';

    /**
     * Filesystem facade.
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $imageFactory;

    /**
     * Item constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Image\Factory $imageFactory
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Image\Factory $imageFactory
    ) {
        parent::__construct($context, $registry);
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->imageFactory = $imageFactory;
    }

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dilmah\Afeature\Model\ResourceModel\Item');
    }

    /**
     * before save.
     *
     * @return $this
     */
    public function beforeSave()
    {
        $attributeData = [
            self::DESKTOP_BANNER_DIRECTORY => 'desktop_image_url',
            self::TABLET_BANNER_DIRECTORY => 'tablet_image_url',
            self::MOBILE_BANNER_DIRECTORY => 'mobile_image_url',
        ];
        foreach ($attributeData as $subDir => $attributeCode) {
            $fileName = $this->_handleUpload($subDir, $attributeCode);

            if (is_string($fileName)) {
                $this->setData($attributeCode, $fileName);
            }
        }

        return parent::beforeSave();
    }

    /**
     * handle banner upload
     *
     * @param string $subDir
     * @param string $attributeCode
     *
     * @return $this|string
     */
    protected function _handleUpload($subDir, $attributeCode)
    {
        $value = $this->getData($attributeCode);

        // if no image was set - nothing to do
        if (empty($value) && empty($_FILES)) {
            return $this;
        }

        if (is_array($value) && !empty($value['delete'])) {
            $this->setData($attributeCode, '');

            return $this;
        }

        $path = $this->getPath($subDir);

        try {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->fileUploaderFactory->create(['fileId' => $attributeCode]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $uploader->save($path);

            $fileName = $uploader->getUploadedFileName();
            $filePath = self::BASE_UPLOAD_DIRECTORY . '/' . $subDir . $fileName;

            if ($attributeCode == 'desktop_image_url') {
                $this->resizeImage($fileName);
            }
        } catch (\Exception $e) {
            $filePath = $value['value'];
        }

        return $filePath;
    }

    /**
     * absolute path to the dir
     *
     * @param string $subDir
     * @return string
     */
    protected function getPath($subDir)
    {
        return $this->filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        )->getAbsolutePath(
            self::BASE_UPLOAD_DIRECTORY . '/' . $subDir
        );
    }

    /**
     * resize and save the non-retina desktop version of the desktop image
     *
     * @param string $fileName
     * @return void
     */
    protected function resizeImage($fileName)
    {
        $path = $this->getPath(self::DESKTOP_BANNER_DIRECTORY);

        $image = $this->imageFactory->create($path . $fileName);

        $newFile = $this->prepareFileName($fileName);
        $image->quality(100);
        $image->constrainOnly(true);
        $image->keepAspectRatio(true);
        $image->resize(self::DESKTOP_IMAGE_RESIZE_WIDTH, self::DESKTOP_IMAGE_RESIZE_HEIGHT);
        $image->save($this->getPath(self::DESKTOP_SMALL_BANNER_DIRECTORY) . $newFile['path'], $newFile['name']);
    }

    /**
     * Image url /m/a/magento.png return ['name' => 'magento.png', 'path => '/m/a']
     *
     * @param string $imageUrl
     * @return array
     */
    protected function prepareFileName($imageUrl)
    {
        $fileArray = explode('/', $imageUrl);
        $fileName = array_pop($fileArray);
        $filePath = implode('/', $fileArray);
        return ['name' => $fileName, 'path' => $filePath];
    }
}
