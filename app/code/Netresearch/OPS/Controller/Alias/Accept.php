<?php

namespace Netresearch\OPS\Controller\Alias;

class Accept extends \Netresearch\OPS\Controller\Alias
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
            $quote = $this->quoteQuoteFactory->create()->load($params['Alias_OrderId']);
            $this->updateAdditionalInformation($quote, $params);
        } else {
            $quote = $this->getQuote();
        }

        // OGNH-7 special handling for admin orders
        $this->oPSAliasHelper->setAliasToPayment(
            $quote->getPayment(),
            array_change_key_case($params, CASE_LOWER),
            false
        );

        // @codingStandardsIgnoreStart
        $result
            = sprintf(
                "<script type='application/javascript'>window.onload =  function() {  top.jQuery('body').trigger('alias:success', ['%s']); };</script>",
                $params['Alias_AliasId']
            );
        // @codingStandardsIgnoreEnd

        return $this->getResponse()->setBody($result);
    }
}
