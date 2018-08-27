<?php

namespace Netresearch\OPS\Controller\Api;

class PostBack extends \Netresearch\OPS\Controller\Api
{
    /**
     * Action to control postback data from ops
     *
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        try {
            $status = $this->getPaymentHelper()->applyStateForOrder(
                $this->_getOrder(),
                $params
            );
            $redirectRoute = $this->oPSApiHelper
                ->getRedirectRouteFromStatus($status);
            return $this->_redirect(
                $redirectRoute,
                [
                    '_store' => $this->_getOrder()->getStoreId(),
                    '_query' => $params,
                    '_nosid' => true
                ]
            );
        } catch (\Exception $e) {
            $this->oPSHelper->log(sprintf('Run into exception %s in postBackAction', $e->getMessage()));
            return $this->getResponse()->setHttpResponseCode(500);
        }
    }
}
