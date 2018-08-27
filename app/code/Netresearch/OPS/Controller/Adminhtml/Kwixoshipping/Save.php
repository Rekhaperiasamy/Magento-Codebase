<?php

namespace Netresearch\OPS\Controller\Adminhtml\Kwixoshipping;

class Save extends \Netresearch\OPS\Controller\Adminhtml\Kwixoshipping
{
    /**
     * save submitted form data
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            $methodCodes = array_keys(
                $this->shippingConfig->getAllCarriers()
            );
            $validator = $this->oPSValidatorKwixoShippingSettingFactory->create();
            if (true === $validator->isValid($postData)) {
                foreach ($postData as $shippingCode => $kwixoData) {
                    if (!in_array($shippingCode, $methodCodes)) {
                        continue;
                    }
                    $kwixoShippingModel = $this->oPSKwixoShippingSettingFactory->create()
                        ->load($shippingCode, 'shipping_code');
                    $kwixoShippingModel
                        ->setShippingCode($shippingCode)
                        ->setKwixoShippingType(
                            $kwixoData['kwixo_shipping_type']
                        )
                        ->setKwixoShippingSpeed(
                            $kwixoData['kwixo_shipping_speed']
                        )
                        ->setKwixoShippingDetails(
                            $kwixoData['kwixo_shipping_details']
                        )
                        ->save();
                }
            } else {
                $postData = array_merge_recursive(
                    $postData,
                    $validator->getMessages()
                );
                $this->backendSessionFactory->create()->setData(
                    'errorneousData',
                    $postData
                );
            }
        }

        return $this->_redirect('adminhtml/kwixoshipping/index');
    }
}
