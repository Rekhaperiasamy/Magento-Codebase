<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use FME\Geoipultimatelock\Model\Rule;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PostDataProcessor */
    protected $dataProcessor;

    /** @var Geoipultimatelock  */
    protected $_geoipultimatelockRule;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param PostDataProcessor $dataProcessor
     * @param Index $geoipultimatelockRule
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Rule $geoipultimatelockRule,
        JsonFactory $jsonFactory
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->_geoipultimatelockRule = $geoipultimatelockRule;
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
            /** @var \Magento\Geoipultimatelock\Model\Rule $geoipultimatelockRule */
            $model = $this->_geoipultimatelockRule->load($id);
            try {
                $data = $this->dataProcessor->filter($postItems[$id]);
                $model->setData($data);
                $model->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithGeoipultimatelockId($model, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithGeoipultimatelockId($model, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithGeoipultimatelockId(
                    $model,
                    __('Something went wrong while saving the Rule.')
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
    protected function getErrorWithGeoipultimatelockId(Rule $geoipultimatelockRule, $errorText)
    {
        return '[Geoipultimatelock ID: ' . $geoipultimatelockRule->getId() . '] ' . $errorText;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
