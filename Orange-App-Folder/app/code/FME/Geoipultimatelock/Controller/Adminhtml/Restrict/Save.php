<?php

namespace FME\Geoipultimatelock\Controller\Adminhtml\Restrict;

class Save extends \Magento\Backend\App\Action
{

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Geoipultimatelock::restrict_save');
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $_helper = $this->_objectManager->create('FME\Geoipultimatelock\Helper\Data');
//        echo '<pre>';
//        print_r($data);
//        exit;
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $model = $this->_objectManager->create('FME\Geoipultimatelock\Model\Restrict');
            $id = $this->getRequest()->getParam('blocked_id');

            if ($id) {
                $model->load($id);
            }

            if (isset($data['blocked_ip'])) {
                $blockedIps = preg_split('@,@', $data['blocked_ip'], null, PREG_SPLIT_NO_EMPTY);
                foreach ($blockedIps as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
                        $this->messageManager->addError(__('Ip has wrong format.'));
                        $this->_getSession()->setFormData($data);
                        return $resultRedirect->setPath('*/*/edit', array('blocked_id' => $this->getRequest()->getParam('blocked_id')));
                    }
                }
            }

            $blockedIps = explode(',', $data['blocked_ip']);

            if (count($blockedIps) > 1) {
                $blockedIpsUnique = array_unique($blockedIps);

                foreach ($blockedIpsUnique as $ip) {
                    if ($_helper->isIpBlocked($ip)) {
                        continue;
                    }

                    $model = $this->_objectManager->create('FME\Geoipultimatelock\Model\Restrict');
                    $remoteAddr = ip2long($ip);

                    $model->setRemoteAddr($remoteAddr)
                            ->setBlockedIp($ip)
                            ->setIsActive(1)
                            ->setCreationTime(date('y-m-d h:i:s'))
                            ->setUpdateTime(date('y-m-d h:i:s'));

                    $model->save();
                }

                $this->messageManager->addSuccess(__('Restrict IPs saved successfully.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', array('blocked_id' => $model->getId(), '_current' => true));
                }

                return $resultRedirect->setPath('*/*/');
            }

            if ($_helper->isIpBlocked($data['blocked_ip'], false)) {
                $this->messageManager->addError('This IP already exists!');
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());

            try {
                if ($model->getCreationTime() == null || $model->getUpdateTime() == null) {
                    $model->setCreationTime(date('y-m-d h:i:s'))
                            ->setUpdateTime(date('y-m-d h:i:s'));
                } else {
                    $model->setUpdateTime(date('y-m-d h:i:s'));
                }

                $model->save();
                $this->messageManager->addSuccess(__('Restrict IPs saved successfully.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', array('blocked_id' => $model->getId(), '_current' => true));
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the restrict IP(s) info.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', array('blocked_id' => $this->getRequest()->getParam('blocked_id')));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
