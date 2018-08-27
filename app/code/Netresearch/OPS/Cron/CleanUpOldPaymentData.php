<?php

namespace Netresearch\OPS\Cron;

class CleanUpOldPaymentData
{
    /**
     * @var \Netresearch\OPS\Helper\Quote
     */
    protected $oPSQuoteHelper;

    /**
     * @param \Netresearch\OPS\Helper\Quote $oPSQuoteHelper
     */
    public function __construct(\Netresearch\OPS\Helper\Quote $oPSQuoteHelper)
    {
        $this->oPSQuoteHelper = $oPSQuoteHelper;
    }

    /**
     * triggered by cron for deleting old payment data from the additional payment information
     *
     * @return void
     */
    public function execute()
    {
        $this->oPSQuoteHelper->cleanUpOldPaymentInformation();
    }
}
