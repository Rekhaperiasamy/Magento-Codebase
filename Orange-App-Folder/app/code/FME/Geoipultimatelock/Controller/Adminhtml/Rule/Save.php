<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Rule;

class Save extends \Magento\Backend\App\Action
{

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Geoipultimatelock::save');
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $model = $this->_objectManager->create('FME\Geoipultimatelock\Model\Rule');

            $id = $this->getRequest()->getParam('geoipultimatelock_id');

            if ($id) {
                $model->load($id);
            }

            if (isset($data['cms_page_ids'])) {
                $data['cms_page_ids'] = implode(',', $data['cms_page_ids']);
            } else {
                $data['cms_page_ids'] = '';
            }
            
            if (isset($data['countries_list']) && count($data['countries_list']) > 0) {
                $data['countries_list'] = serialize($data['countries_list']);
            }

            if (isset($data['exception_ips'])) {
                $exceptionIps = preg_split('@,@', $data['exception_ips'], null, PREG_SPLIT_NO_EMPTY);
                foreach ($exceptionIps as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
                        $this->messageManager->addError(__('Ip has wrong format.'));
                        $this->_getSession()->setFormData($data);
                        return $resultRedirect->setPath('*/*/edit', array('geoipultimatelock_id' => $this->getRequest()->getParam('geoipultimatelock_id')));
                    }
                }
            }

            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);

            $model->loadPost($data);

            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());

            try {
                if ($model->getCreationTime() == null || $model->getUpdateTime() == null) {
                    $model->setCreationTime(date('y-m-d h:i:s'))
                            ->setUpdateTime(date('y-m-d h:i:s'));
                } else {
                    $model->setUpdateTime(date('y-m-d h:i:s'));
                }

                $model->save();
                $this->messageManager->addSuccess(__('Rule saved successfully.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', array('geoipultimatelock_id' => $model->getId(), '_current' => true));
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the rule.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', array('geoipultimatelock_id' => $this->getRequest()->getParam('geoipultimatelock_id')));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
