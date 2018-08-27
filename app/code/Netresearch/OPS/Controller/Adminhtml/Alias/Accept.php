<?php

namespace Netresearch\OPS\Controller\Adminhtml\Alias;

class Accept extends \Netresearch\OPS\Controller\Adminhtml\Alias
{
    /**
     * accept-action for Alias-generating iframe-response
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $this->oPSHelper->log(
            __(
                "Incoming accepted Ingenico ePayments Alias Feedback\n\nRequest Path: %1\nParams: %2\n",
                $this->getRequest()->getPathInfo(),
                serialize($params)
            )
        );
        $this->oPSAliasHelper->saveAlias($params);

        if (array_key_exists('Alias_OrderId', $params)) {
            $quote = $this->getQuote();
            $this->updateAdditionalInformation($quote, $params);
        } else {
            $quote = $this->getQuote();
        }

        // OGNH-7 special handling for admin orders
        $this->oPSAliasHelper->setAliasToPayment(
            $quote->getPayment(),
            array_change_key_case($params, CASE_LOWER),
            false,
            false
        );

        // @codingStandardsIgnoreStart
        $result
            = sprintf(
                "<script type='application/javascript'>window.onload =  function() {  top.document.fire('alias:success', '%s'); };</script>",
                $params['Alias_AliasId']
            );
        // @codingStandardsIgnoreEnd

        return $this->getResponse()->setBody($result);
    }
}
