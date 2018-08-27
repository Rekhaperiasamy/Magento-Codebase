<?php

namespace Netresearch\OPS\Controller\Adminhtml\Alias;

class Exception extends \Netresearch\OPS\Controller\Alias
{
    /**
     * exception-action for Alias-generating iframe-response
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $errors = [];

        foreach ($params as $key => $value) {
            if (stristr($key, 'error') && 0 != $value) {
                $errors[] = $value;
            }
        }

        $this->oPSHelper->log(
            __(
                "Incoming exception Ingenico ePayments Alias Feedback\n\nRequest Path: %1\nParams: %2\n",
                $this->getRequest()->getPathInfo(),
                serialize($params)
            )
        );

        // @codingStandardsIgnoreStart
        $result
            = "<script type='application/javascript'>window.onload =  function() {  top.document.fire('alias:failure'); };</script>";
        // @codingStandardsIgnoreEnd

        return $this->getResponse()->setBody($result);
    }
}
