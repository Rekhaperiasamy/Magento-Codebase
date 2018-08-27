<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Restrict;

use Magento\Backend\App\Action\Context;
use FME\Geoipultimatelock\Model\Restrict;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PostDataProcessor */
    protected $dataProcessor;

    /** @var geoipultimatelock  */
    protected $_geoipultimatelockRestrict;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param PostDataProcessor $dataProcessor
     * @param Restrict $geoipultimatelockRestrict
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Restrict $geoipultimatelockRestrict,
        JsonFactory $jsonFactory
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->_geoipultimatelockRestrict = $geoipultimatelockRestrict;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = array();

        $postItems = $this->getRequest()->getParam('items', array());
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                array(
                        'messages' => array(__('Please correct the data sent.')),
                        'error' => true,
                )
            );
        }

        foreach (array_keys($postItems) as $id) {
            /** @var \Magento\Geoipultimatelock\Model\Restrict $geoipultimatelockRestrict */
            $model = $this->_geoipultimatelockRestrict->load($id);
            try {
                $data = $this->dataProcessor->filter($postItems[$id]);
                $model->setData($data);
                $model->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithBlockedId($model, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithBlockedId($model, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithBlockedId(
                    $model,
                    __('Something went wrong while saving the Restrict IPs info.')
                );
                $error = true;
            }
        }

        return $resultJson->setData(
            array(
                    'messages' => $messages,
                    'error' => $error
            )
        );
    }

    /**
     * Add GeoipultimatelockIndex title to error message
     *
     * @param Index $geoipultimatelockRule
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithGeoipultimatelockId(Restrict $geoipultimatelockRestrict, $errorText)
    {
        return '[Blcoked ID: ' . $geoipultimatelockRestrict->getId() . '] ' . $errorText;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
