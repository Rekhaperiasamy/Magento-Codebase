<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 18/05/2016
 * Time: 15:03
 */

namespace Magenest\AbandonedCartReminder\Controller\Adminhtml\Rule;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class Upload extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;


    /**
     * @param \Magento\Backend\App\Action\Context             $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;

    }//end __construct()


    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AbandonedCartReminder::rule');

    }//end _isAllowed()


    public function execute()
    {
        try {
            /*
                * @var  $uploader \Magento\MediaStorage\Model\File\Uploader
            */
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'txt']);
            /*
                * @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter
            */
            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
            // $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            /*
                * @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory
            */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
            $config         = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config');
            $result         = $uploader->save($mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath()));

            $this->_eventManager->dispatch(
                'ufe_attach_upload_after',
                [
                 'result' => $result,
                 'action' => $this,
                ]
            );
            $anaName     = explode('/', $result['file']);
            $desireIndex = (count($anaName) - 1);

            $result['label'] = $anaName[$desireIndex];
            unset($result['tmp_name']);
            unset($result['path']);

            $result['url']  = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config')->getTmpMediaUrl($result['file']);
           // $result['file'] = $result['file'].'.tmp';
            $result['file'] = $result['file'];
        } catch (\Exception $e) {
            $result = [
                       'error'     => $e->getMessage(),
                       'errorcode' => $e->getCode(),
                      ];
        }//end try

        /*
            * @var \Magento\Framework\Controller\Result\Raw $response
        */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;

    }//end execute()
}//end class
